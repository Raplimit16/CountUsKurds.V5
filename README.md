# Count Us Kurds - Clean PHP Application

## Overview
A modern, clean PHP application for counting and documenting the Kurdish population worldwide.

## Features
- **Multi-language support** (20+ languages including Kurdish dialects)
- **Public registration** for individuals and organizations
- **Admin panel** with SaaS-style dashboard
- **Secure authentication** (Password + TOTP 2FA)
- **Statistics and reporting**
- **CSV export**

## Installation

### 1. Upload Files
Upload all files to your web server (Strato).

### 2. Configure Environment
Copy `.env.example` to `.env` and update:
- Database credentials
- Mail settings
- Site URL

### 3. Create Database Tables
Run the SQL in `database/schema.sql` in phpMyAdmin.

### 4. Set Permissions
```bash
chmod 755 public/
chmod 644 .env
chmod 755 storage/
```

## File Structure
```
/
├── public/              # Web root (point domain here)
│   ├── index.php        # Main entry point
│   ├── admin.php        # Admin routes
│   ├── api.php          # API endpoints
│   ├── .htaccess        # Apache config
│   └── assets/          # CSS, JS, images
├── src/                 # Application code
│   ├── bootstrap.php    # App initialization
│   ├── Controllers/     # Route handlers
│   └── Services/        # Business logic
├── templates/           # View templates
│   ├── public/          # Public pages
│   ├── admin/           # Admin panel
│   └── errors/          # Error pages
├── lang/                # Translation files
├── config/              # Configuration
├── database/            # SQL schemas
├── storage/             # Logs, cache, uploads
└── .env                 # Environment config
```

## Admin Access
- URL: `https://yourdomain.com/admin`
- Default user: `ceoadmin`
- Auth: Password + TOTP 2FA

## Security Features
- CSRF protection
- Password hashing (bcrypt)
- TOTP 2FA
- Rate limiting
- SQL injection prevention
- XSS protection

## Adding Languages
1. Copy `lang/en.php` to `lang/XX.php`
2. Translate all strings
3. Add language to `config/app.php` in `languages` array

## Support
Contact: info@countuskurds.com
