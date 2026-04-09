# Count Us Kurds - Sakerhetsgranskningsrapport

## Genomforda Sakerhetsatgarder

### 1. HTTP Security Headers
- [x] X-Frame-Options: DENY (admin) / SAMEORIGIN (public)
- [x] X-Content-Type-Options: nosniff
- [x] X-XSS-Protection: 1; mode=block
- [x] Referrer-Policy: strict-origin-when-cross-origin
- [x] Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()
- [x] Cache-Control: no-cache pa PHP-filer
- [x] HSTS for HTTPS-anslutningar
- [x] CSP (Content Security Policy) for admin och frontend

### 2. Autentisering & Session
- [x] CSRF-skydd med hash_equals() jamforelse
- [x] Session cookie: HttpOnly, Secure, SameSite=Strict
- [x] Brute-force skydd med rate limiting (5 forsok / 15 min)
- [x] Kontolasning efter misslyckade forsok
- [x] Session regenerering efter inloggning
- [x] TOTP 2FA-stod med tidstolerans (+/- 30 sek)
- [x] PBKDF2 losenordshashning (min 100,000 iterationer)
- [x] bcrypt for databaslagrade losenord

### 3. Databassakerhet
- [x] Prepared statements for alla queries (SQL injection skydd)
- [x] Input-validering och sanitering
- [x] Undvikande av direkt felmeddelande-exponering

### 4. Filsakerhet (.htaccess)
- [x] Blockering av kansliga filer (.env, .git, etc.)
- [x] Blockering av backup-filer (.bak, .sql, .log)
- [x] Blockering av suspekta HTTP-metoder (TRACE, DELETE, etc.)
- [x] SQL injection-monsterdetektion i query strings
- [x] Blockering av kanda skadliga bots
- [x] Directory listing inaktiverat
- [x] Server signature inaktiverat

### 5. API-sakerhet
- [x] CORS med specificerade origins
- [x] Rate limiting
- [x] Input-validering
- [x] JSON-only responses

### 6. Felsidor
- [x] 400 - Bad Request
- [x] 401 - Unauthorized
- [x] 403 - Forbidden
- [x] 404 - Not Found
- [x] 419 - Session Expired
- [x] 429 - Too Many Requests
- [x] 500 - Internal Server Error
- [x] 503 - Service Unavailable
- [x] Ingen kansllig information exponeras i felmeddelanden

---

## Rekommendationer for Produktion

### Hog Prioritet
1. **Aktivera HTTPS-omdirigering** - Avkommentera force HTTPS i .htaccess
2. **Satt starka ADMIN_PASSWORD_HASH** i .env
3. **Korsysylla admin_users tabell** med SQL fran database/admin_users_table.sql
4. **Granska CORS origins** - Laga till endast godkanda domaner

### Medel Prioritet
1. **Aktivera hotlinking-skydd** om bildstold ar ett problem
2. **Konfigurera backup-rutiner** for databasen
3. **Satt upp log-rotation** for app_logs tabellen
4. **Overvaka failed_login forsok** i admin_users

### Lag Prioritet
1. **Implementera IP-whitelist** for admin om mojligt
2. **Lagg till recaptcha** for offentliga formuler
3. **Overvag WAF** (Web Application Firewall)

---

## SQL-tabeller att Skapa/Verifiera

### 1. admin_users (KRÄVS för inloggning)
```sql
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NULL,
  `totp_secret` VARCHAR(64) NULL,
  `totp_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `failed_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `last_login` DATETIME NULL,
  `password_changed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lagg till ceoadmin
INSERT INTO `admin_users` (`username`, `totp_secret`, `totp_enabled`)
VALUES ('ceoadmin', 'RP6IMPOYMAJD72P2', 1)
ON DUPLICATE KEY UPDATE 
  `totp_secret` = 'RP6IMPOYMAJD72P2',
  `totp_enabled` = 1,
  `failed_attempts` = 0,
  `locked_until` = NULL;
```

---

## Filstruktur - Uppdaterade Filer

```
public/
├── .htaccess                    # Forbattrad med sakerhet + felhantering
├── admin.php                    # Forbattrad med security headers
├── maintenance.php              # NY - Underhallssida
├── api/
│   └── index.php                # Forbattrad med CORS + security
└── errors/
    ├── generic.php              # NY - Snygg felsida
    ├── 401.php                  # NY
    ├── 403.php                  # Uppdaterad
    ├── 404.php                  # Uppdaterad
    ├── 419.php                  # Uppdaterad
    ├── 429.php                  # Uppdaterad
    ├── 500.php                  # Uppdaterad
    └── 503.php                  # NY

app/Http/Controllers/
└── AdminController.php          # NY - Fixad encoding + battre debug

database/
└── admin_users_table.sql        # NY - SQL for admin-tabell
```

---

## Kontakt vid Sakerhetsproblem
Om du hittar sakerhetsproblem, kontakta omedelbart utvecklingsteamet.

Granskad: <?php echo date('Y-m-d H:i:s'); ?>
