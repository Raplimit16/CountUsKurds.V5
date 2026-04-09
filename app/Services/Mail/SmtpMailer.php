<?php
declare(strict_types=1);

namespace CountUsKurds\Services\Mail;

use CountUsKurds\Support\Logger;

/**
 * SMTP Mailer using native PHP sockets
 * Optimized for Strato email hosting (smtp.strato.com:465 SSL)
 */
final class SmtpMailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password,
        string $encryption = 'ssl'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = strtolower($encryption);
    }

    public function send(
        string $to,
        string $subject,
        string $htmlBody,
        string $plainBody,
        string $fromEmail,
        string $fromName
    ): bool {
        try {
            // Connect to SMTP server
            $smtp = $this->connect();
            if (!$smtp) {
                Logger::error('SMTP connection failed');
                return false;
            }

            // Send SMTP commands
            $this->sendCommand($smtp, "EHLO {$this->host}");
            $this->sendCommand($smtp, "AUTH LOGIN");
            $this->sendSensitiveCommand($smtp, base64_encode($this->username));
            $this->sendSensitiveCommand($smtp, base64_encode($this->password));
            $this->sendCommand($smtp, "MAIL FROM: <{$fromEmail}>");
            $this->sendCommand($smtp, "RCPT TO: <{$to}>");
            $this->sendCommand($smtp, "DATA");

            // Build email
            $boundary = 'CUK-' . bin2hex(random_bytes(12));
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

            $email = "From: {$encodedFromName} <{$fromEmail}>\r\n";
            $email .= "Reply-To: {$fromEmail}\r\n";
            $email .= "To: {$to}\r\n";
            $email .= "Subject: {$encodedSubject}\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $email .= "\r\n";
            
            // Plain text part
            $email .= "--{$boundary}\r\n";
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $email .= "Content-Transfer-Encoding: 8bit\r\n";
            $email .= "\r\n";
            $email .= $plainBody . "\r\n";
            $email .= "\r\n";
            
            // HTML part
            $email .= "--{$boundary}\r\n";
            $email .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email .= "Content-Transfer-Encoding: 8bit\r\n";
            $email .= "\r\n";
            $email .= $htmlBody . "\r\n";
            $email .= "\r\n";
            $email .= "--{$boundary}--\r\n";
            
            // Send email content
            fwrite($smtp, $email);
            fwrite($smtp, "\r\n.\r\n");
            
            // Get response
            $response = fgets($smtp);
            
            // Quit
            $this->sendCommand($smtp, "QUIT");
            fclose($smtp);

            // Check if successful (250 = OK)
            if (strpos($response, '250') !== false) {
                Logger::info('Email sent successfully', ['to' => $to, 'subject' => $subject]);
                return true;
            } else {
                Logger::error('SMTP send failed', ['response' => $response]);
                return false;
            }

        } catch (\Throwable $e) {
            Logger::error('SMTP mail exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function connect()
    {
        $errno = 0;
        $errstr = '';
        
        // Build connection string based on encryption
        if ($this->encryption === 'ssl') {
            $connectionString = "ssl://{$this->host}:{$this->port}";
        } elseif ($this->encryption === 'tls') {
            $connectionString = "tls://{$this->host}:{$this->port}";
        } else {
            $connectionString = "{$this->host}:{$this->port}";
        }

        // Connect with timeout
        $smtp = @fsockopen($connectionString, 0, $errno, $errstr, 30);
        
        if (!$smtp) {
            Logger::error('SMTP connection error', [
                'host' => $connectionString,
                'errno' => $errno,
                'errstr' => $errstr
            ]);
            return false;
        }

        // Set timeout
        stream_set_timeout($smtp, 30);
        
        // Read greeting
        $response = fgets($smtp);
        Logger::info('SMTP connected', ['response' => trim($response)]);

        return $smtp;
    }

    private function sendCommand($smtp, string $command): string
    {
        fwrite($smtp, $command . "\r\n");
        $response = fgets($smtp);
        Logger::info('SMTP command', ['command' => $command, 'response' => trim($response)]);
        return $response;
    }

    private function sendSensitiveCommand($smtp, string $command): string
    {
        fwrite($smtp, $command . "\r\n");
        $response = fgets($smtp);
        Logger::info('SMTP command', ['command' => '[REDACTED]', 'response' => trim($response)]);
        return $response;
    }
}
