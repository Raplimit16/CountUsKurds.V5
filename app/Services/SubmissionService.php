<?php
declare(strict_types=1);

namespace CountUsKurds\Services;

use CountUsKurds\Support\Logger;
use CountUsKurds\Support\Translator;
use Exception;
use mysqli;
use mysqli_stmt;

class SubmissionService
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Process an application submission.
     *
     * @param array $input Raw request payload.
     * @param string $locale Current locale for validation messages.
     */
    public function handle(array $input, string $locale): array
    {
        $applicationType = $this->sanitizeString($input['application_mode'] ?? 'individual');
        $name = $this->sanitizeString($input['name'] ?? '');
        $email = filter_var($input['email'] ?? null, FILTER_VALIDATE_EMAIL);
        $region = $this->sanitizeString($input['region'] ?? '');
        $gdprConsent = isset($input['gdpr_consent']) ? 1 : 0;

        $individualContribution = $this->sanitizeText($input['individual_contribution'] ?? null);
        $orgName = $this->sanitizeString($input['org_name'] ?? null);
        $orgContribution = $this->sanitizeText($input['org_contribution'] ?? null);
        $orgMotive = $this->sanitizeText($input['org_motive'] ?? null);

        if (!$email) {
            return $this->errorResponse($locale, 'form.messages.invalid_email');
        }

        if ($gdprConsent !== 1) {
            return $this->errorResponse($locale, 'form.messages.consent_required');
        }

        if ($name === '' || $region === '') {
            return $this->errorResponse($locale, 'form.messages.required_fields');
        }

        if ($applicationType === 'individual') {
            if ($individualContribution === null || trim($individualContribution) === '') {
                return $this->errorResponse($locale, 'form.messages.describe_contribution');
            }
        } elseif ($applicationType === 'group') {
            if ($orgName === null || trim($orgName) === '' ||
                $orgContribution === null || trim($orgContribution) === '' ||
                $orgMotive === null || trim($orgMotive) === '') {
                return $this->errorResponse($locale, 'form.messages.group_required');
            }
        } else {
            $applicationType = 'individual';
        }

        try {
            $conn = Database::connection();
            $stmt = $this->prepareStatement($conn, $applicationType);

            if ($applicationType === 'individual') {
                $stmt->bind_param(
                    'sssssi',
                    $applicationType,
                    $name,
                    $email,
                    $region,
                    $individualContribution,
                    $gdprConsent
                );
            } else {
                $stmt->bind_param(
                    'sssssssi',
                    $applicationType,
                    $name,
                    $email,
                    $region,
                    $orgName,
                    $orgContribution,
                    $orgMotive,
                    $gdprConsent
                );
            }

            if (!$stmt->execute()) {
                if ($conn->errno === 1062) {
                    return $this->errorResponse($locale, 'form.messages.duplicate');
                }

                Logger::error('Submission insert failed', ['error' => $stmt->error, 'code' => $conn->errno]);
                return $this->errorResponse($locale, 'form.messages.database_error');
            }

            $stmt->close();

            // Try to send email, but don't fail if it doesn't work
            try {
                $this->sendThankYouEmail(
                    $email,
                    $name,
                    $locale,
                    $applicationType === 'group' ? ($orgName ?? $name) : $name
                );
            } catch (\Throwable $emailError) {
                // Log but don't fail the submission
                Logger::error('Email sending failed but submission saved', [
                    'error' => $emailError->getMessage(),
                    'email' => $email
                ]);
            }

            return [
                'success' => true,
                'message' => $this->translator->get($locale, 'form.messages.success'),
            ];
        } catch (Exception $e) {
            Logger::error('Unexpected submission failure', ['exception' => $e->getMessage()]);

            return $this->errorResponse($locale, 'form.messages.exception');
        }
    }

    private function errorResponse(string $locale, string $key): array
    {
        return [
            'success' => false,
            'message' => $this->translator->get($locale, $key),
        ];
    }

    private function prepareStatement(mysqli $conn, string $applicationType): mysqli_stmt
    {
        if ($applicationType === 'individual') {
            $stmt = $conn->prepare(
                'INSERT INTO grundteam_applications
                (application_type, name, email, region, individual_contribution, gdpr_consent)
                VALUES (?, ?, ?, ?, ?, ?)'
            );
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO grundteam_applications
                (application_type, name, email, region, org_name, org_contribution, org_motive, gdpr_consent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
        }

        if (!$stmt) {
            Logger::error('Failed to prepare submission statement', ['error' => $conn->error]);
            throw new Exception('Database error');
        }

        return $stmt;
    }

    private function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $clean = trim($value);
        $clean = preg_replace('/\s+/u', ' ', $clean);
        $clean = strip_tags($clean);

        if (function_exists('mb_substr')) {
            return mb_substr($clean, 0, 255);
        }

        return substr($clean, 0, 255);
    }

    private function sanitizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = trim(str_replace(["\r\n", "\r"], "\n", $value));
        $clean = strip_tags($clean);
        $clean = preg_replace("/\n{3,}/u", "\n\n", $clean);

        return $clean === '' ? null : $clean;
    }

    private function sendThankYouEmail(string $email, string $name, string $locale, string $displayName): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            // Use AutoReplyTemplates for all languages
            $template = \CountUsKurds\Services\AutoReplyTemplates::getTemplate($locale);
            $subject = $template['subject'];
            $htmlBody = \CountUsKurds\Services\AutoReplyTemplates::buildEmail($locale, $displayName ?: $name);
            
            // Plain text version
            $plainBody = strip_tags(str_replace('<br>', "\n", $htmlBody));
            
            // Get SMTP configuration
            $fromAddress = (string) env('MAIL_FROM_ADDRESS', 'info@countuskurds.com');
            $fromName = (string) env('MAIL_FROM_NAME', 'Count Us Kurds');
            $smtpHost = (string) env('MAIL_HOST', 'smtp.strato.com');
            $smtpPort = (int) env('MAIL_PORT', 465);
            $smtpUser = (string) env('MAIL_USERNAME', 'info@countuskurds.com');
            $smtpPass = (string) env('MAIL_PASSWORD', '');
            $smtpEncryption = (string) env('MAIL_ENCRYPTION', 'ssl');
            
            // Send email
            $mailer = new Mail\SmtpMailer($smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEncryption);
            $success = $mailer->send($email, $subject, $htmlBody, $plainBody, $fromAddress, $fromName);
            
            if ($success) {
                Logger::info('Auto-reply email sent', ['email' => $email, 'locale' => $locale]);
            } else {
                Logger::error('Auto-reply email failed', ['email' => $email, 'locale' => $locale]);
            }
        } catch (\Throwable $e) {
            Logger::error('Auto-reply email exception', ['email' => $email, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Build the styled HTML email body used for acknowledgements and auto-replies.
     *
     * @param array{locale:string,subject:string,greeting:string,line1:string,line2:string,autoReply:string,responseTime:string,closing:string,contactLine:string} $data
     */
    private function buildHtmlEmail(array $data): string
    {
        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        $greeting = nl2br($escape($data['greeting']));
        $line1 = nl2br($escape($data['line1']));
        $line2 = nl2br($escape($data['line2']));
        $autoReply = nl2br($escape($data['autoReply']));
        $responseTime = nl2br($escape($data['responseTime']));
        $closing = nl2br($escape($data['closing']));
        $contactLine = nl2br($escape($data['contactLine']));
        $subject = $escape($data['subject']);
        $locale = $escape($data['locale']);
        $contactEmail = 'info@countuskurds.com';
        $contactAnchor = sprintf(
            '<a href="mailto:%1$s" style="color:#ffffff;font-weight:600;text-decoration:none;">%1$s</a>',
            $escape($contactEmail)
        );
        $logoUrl = (string) env('MAIL_LOGO_URL', 'https://countuskurds.com/assets/img/count-us-kurds-logo.png');
        $logoImg = sprintf(
            '<img src="%s" alt="Count Us Kurds" width="64" height="64" style="display:block;width:64px;height:64px;border-radius:16px;background:#fff;padding:6px;box-shadow:0 8px 18px rgba(15,23,42,0.18);">',
            $escape($logoUrl)
        );

        return <<<HTML
<!DOCTYPE html>
<html lang="{$locale}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$subject}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6fb;font-family:'Inter','Segoe UI',Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#ed1c24,#ff684f);padding:24px 32px 20px;color:#fff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:72px;vertical-align:top;">{$logoImg}</td>
                                    <td style="padding-left:16px;vertical-align:middle;">
                                        <div style="font-size:13px;text-transform:uppercase;letter-spacing:0.2em;font-weight:700;opacity:0.85;">Count Us Kurds</div>
                                        <div style="font-size:22px;font-weight:800;margin-top:4px;">{$subject}</div>
                                        <div style="font-size:13px;margin-top:6px;opacity:0.9;">Sent from info@countuskurds.com</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">{$greeting}</p>
                            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">{$line1}</p>
                            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">{$line2}</p>
                            <div style="margin:24px 0;padding:18px;border-radius:14px;background:#f8fafc;border:1px solid #e5e7f1;">
                                <p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#111827;font-weight:600;">{$autoReply}</p>
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#4b5563;">{$responseTime}</p>
                            </div>
                            <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">{$closing}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#111827;color:#fff;padding:20px 32px;">
                            <p style="margin:0 0 6px;font-size:13px;letter-spacing:0.18em;text-transform:uppercase;color:#9ca3af;">Direct contact</p>
                            <p style="margin:0;font-size:16px;font-weight:600;">{$contactLine}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0b111d;color:#d1d5db;padding:18px 32px;font-size:13px;line-height:1.5;text-align:center;">
                            <p style="margin:0 0 6px;">{$contactAnchor}</p>
                            <p style="margin:0;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;">Count Us Kurds Coordination Desk</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
