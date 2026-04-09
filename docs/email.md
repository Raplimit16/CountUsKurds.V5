# Count Us Kurds â€“ Email Operations

The platform now standardises all outbound acknowledgements and auto-responses around the official mailbox `info@countuskurds.com`. This document captures the current setup so that operations, infrastructure, and communications stay aligned.

## Authorised Mailbox

- **Primary address:** `info@countuskurds.com`
- **Usage:** All form confirmations, foundation-team correspondence, and automated replies.
- **Brand voice:** Security & Coordination Team (multi-language strings live in `resources/lang` under `email.*`).

## Strato Server Details

Update `.env` (see `.env.example`) with the credentials supplied for your Strato workspace:

| Purpose | Host | Port | Notes |
| --- | --- | --- | --- |
| SMTP (submission + auto-reply) | `smtp.strato.com` | 465 | SSL (`MAIL_ENCRYPTION=ssl`) |
| IMAP (shared inbox) | `imap.strato.com` | 993 | SSL |
| POP3 (optional pull) | `pop3.strato.com` | 995 | SSL |

Optional brand settings:

- `MAIL_LOGO_URL=https://countuskurds.com/assets/img/count-us-kurds-logo.png` (or any absolute HTTPS image) keeps the email header branded inside `buildHtmlEmail()`.

> Tip: keep `MAIL_USERNAME=info@countuskurds.com` and generate an app-specific password inside Strato for SMTP/IMAP. Never commit the real password.

## Auto-Reply Template

- Source: `CountUsKurds\Services\SubmissionService::sendThankYouEmail()`
- Rendering: dual part (plain text + HTML). The HTML layout includes a gradient hero, highlighted auto-reply notice, response-time promise, and contact footer.
- Localisation: All strings pulled from `resources/lang/{locale}.php` (`email.subject`, `email.auto_reply`, etc.). Adding a new language only requires inserting the corresponding translations.
- Reuse: Call `buildHtmlEmail()` with the desired text blocks to send the same template from other entry points (e.g., when mirroring IMAP auto-responses).

## Current SMTP Implementation

The platform now uses authenticated SMTP through the `CountUsKurds\Services\Mail\SmtpMailer` class, which:

- Connects directly to `smtp.strato.com:465` using SSL encryption
- Supports both HTML and plain text email parts
- Includes proper authentication and error handling
- Logs all SMTP operations for debugging and monitoring
- Is optimized for Strato email hosting infrastructure

The `AutoResponder` service uses this SMTP implementation for reliable email delivery, while `SubmissionService` continues to use PHP's native `mail()` function for basic acknowledgments.

## Future Enhancements

1. ~~Switch from PHP `mail()` to authenticated SMTP~~ ✓ **Completed** - SMTP integration implemented
2. Mirror incoming IMAP messages into the database if audit trails become mandatory.
3. Add a templated signature block for specific working groups (legal, outreach, etc.) while keeping the same visual frame.
4. Migrate `SubmissionService` to use the new `SmtpMailer` for consistency.

The SMTP integration is now complete and ready for production use. The server credentials in `.env` are actively used by the `SmtpMailer` class. Continue to verify that `info@countuskurds.com` remains the single outward-facing address in privacy notices, responses, and marketing collateral.
