<?php
declare(strict_types=1);

namespace CountUsKurds\Services;

use CountUsKurds\Services\Mail\SmtpMailer;
use CountUsKurds\Support\Logger;

class AutoResponder
{
    private SmtpMailer $mailer;
    private string $fromAddress;
    private string $fromName;
    private ?string $logoDataUri = null;

    public function __construct(SmtpMailer $mailer, string $fromAddress, string $fromName)
    {
        $this->mailer = $mailer;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function sendAcknowledgement(string $name, string $recipient, string $locale): void
    {
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $safeName = $name !== '' ? $name : 'friend';

        $subject = 'Count Us Kurds – confirmation';
        if ($locale === 'sv') {
            $subject = 'Count Us Kurds – bekräftelse';
        }

        $html = $this->buildHtmlBody($safeName);
        $text = $this->buildTextBody($safeName);

        $success = $this->mailer->send($recipient, $subject, $html, $text, $this->fromAddress, $this->fromName);
        if (!$success) {
            Logger::error('Auto-responder SMTP failure', ['recipient' => $recipient, 'subject' => $subject]);
        }
    }

    private function buildHtmlBody(string $name): string
    {
        $privacyUrl = $this->absoluteUrl('privacy');
        $logo = $this->logoData();
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $english = <<<HTML
<p style="margin:0 0 12px;">Dear {$safeName},</p>
<p style="margin:0 0 12px;">Thank you for reaching out to Count Us Kurds. This automatic confirmation verifies that we securely received your submission to join the foundation team.</p>
<p style="margin:0 0 12px;">Our coordination staff will review your information, align it with our mission, and respond as soon as possible. Sensitive details remain encrypted in Strato’s EU data centres and are governed by our <a href="{$privacyUrl}" style="color:#c93a20; font-weight:600;">privacy & data ownership policy</a>.</p>
<p style="margin:0 0 12px;"><strong>Data ownership and usage</strong><br>By sharing your expertise you confirm that Count Us Kurds may store, adapt, analyse, publish, or transfer the information in order to build the first global Kurdish census and its safeguarding structures.</p>
<p style="margin:0 0 12px;">If you need to correct or add details, simply reply to this email or write to <a href="mailto:info@countuskurds.com" style="color:#c93a20;">info@countuskurds.com</a>.</p>
HTML;

        $swedish = <<<HTML
<p style="margin:24px 0 8px; font-weight:600;">Svenska</p>
<p style="margin:0 0 12px;">Hej {$safeName},</p>
<p style="margin:0 0 12px;">Tack för ert meddelande till Count Us Kurds. Det här automatiska svaret bekräftar att vi säkert har tagit emot er intresseanmälan.</p>
<p style="margin:0 0 12px;">Vårt samordningsteam granskar uppgifterna och hör av sig så snart som möjligt. All information lagras krypterat i Stratos datacenter inom EU och omfattas av vår <a href="{$privacyUrl}" style="color:#c93a20; font-weight:600;">integritetspolicy</a>.</p>
<p style="margin:0 0 12px;">Genom att dela era uppgifter ger ni Count Us Kurds rätt att använda dem för att bygga den globala kurdiska folkräkningen och dess skyddsmekanismer. Vid behov av uppdateringar kan ni svara direkt på detta mejl eller kontakta <a href="mailto:info@countuskurds.com" style="color:#c93a20;">info@countuskurds.com</a>.</p>
HTML;

        $footer = <<<HTML
<p style="margin:24px 0 6px; font-size:12px; color:#6b7280;">This confirmation was sent from {$this->fromAddress} via Strato secure servers on behalf of Count Us Kurds.</p>
HTML;

        return <<<HTML
<div style="font-family: 'Inter', Arial, sans-serif; background:#f7f7fb; padding:32px 20px;">
    <div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:18px; padding:32px; box-shadow:0 18px 40px rgba(15,23,42,0.12);">
        <div style="text-align:center; margin-bottom:24px;">
            <img src="{$logo}" alt="Count Us Kurds" style="width:80px; height:80px; border-radius:18px;">
            <p style="margin:12px 0 0; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#c93a20;">Count Us Kurds</p>
        </div>
        {$english}
        {$swedish}
        {$footer}
    </div>
</div>
HTML;
    }

    private function buildTextBody(string $name): string
    {
        $privacyUrl = $this->absoluteUrl('privacy');

        $english = <<<TEXT
Dear {$name},

Thank you for reaching out to Count Us Kurds. This automatic confirmation verifies that we securely received your expression of interest. Our coordination team will review your information, align it with our mission, and respond as soon as possible. Sensitive details remain encrypted in Strato’s EU data centres and are protected by our privacy & data ownership policy ({$privacyUrl}).

By sharing your expertise you confirm that Count Us Kurds may store, adapt, analyse, publish, or transfer the information to build the global Kurdish census and its safeguards. If you need to edit or add details, reply to this email or contact info@countuskurds.com.
TEXT;

        $swedish = <<<TEXT
---
Svenska

Hej {$name},

Tack för ert meddelande. Det här automatiska svaret bekräftar att vi säkert har tagit emot er intresseanmälan. Vårt team hör av sig så snart som möjligt. All information lagras krypterat i Stratos datacenter inom EU och omfattas av vår integritetspolicy ({$privacyUrl}).

Genom att dela era uppgifter ger ni Count Us Kurds rätt att använda dem för att bygga den globala kurdiska folkräkningen och dess skyddsmekanismer. Behöver ni uppdatera något, svara på detta mejl eller skriv till info@countuskurds.com.
TEXT;

        return $english . "\n\n" . $swedish . "\n\nSent from {$this->fromAddress} via Strato secure servers.";
    }

    private function absoluteUrl(string $path): string
    {
        $configured = env('APP_URL');
        if (is_string($configured) && trim($configured) !== '') {
            return rtrim($configured, '/') . '/' . ltrim($path, '/');
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'countuskurds.com');

        return $scheme . '://' . rtrim($host, '/') . '/' . ltrim($path, '/');
    }

    private function logoData(): string
    {
        if ($this->logoDataUri !== null) {
            return $this->logoDataUri;
        }

        $logoPath = public_path('assets/img/count-us-kurds-logo.png');
        if (!is_file($logoPath)) {
            $this->logoDataUri = '';
            return '';
        }

        $contents = file_get_contents($logoPath);
        if ($contents === false) {
            $this->logoDataUri = '';
            return '';
        }

        $this->logoDataUri = 'data:image/png;base64,' . base64_encode($contents);
        return $this->logoDataUri;
    }
}
