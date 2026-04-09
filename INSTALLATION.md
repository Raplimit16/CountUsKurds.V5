# Count Us Kurds - Installation på Webbhotel

Denna guide hjälper dig att installera Count Us Kurds på ett standard PHP 8.3 webbhotel.

## Systemkrav

- PHP 8.3 eller högre
- MySQL 5.7+ eller MariaDB 10.3+
- mod_rewrite aktiverat (Apache)
- Minst 256MB PHP-minne
- SSL-certifikat (rekommenderas starkt)

## Installationssteg

### 1. Ladda upp filer

Ladda upp alla filer till ditt webbhotel via FTP/SFTP:
- Om ditt webbhotel använder `public_html` eller `htdocs` som rot-mapp, ladda upp ALLT innehåll till den mappen
- Strukturen ska bli:
  ```
  public_html/
  ├── app/
  ├── bootstrap/
  ├── config/
  ├── public/
  ├── resources/
  ├── storage/
  ├── .htaccess
  └── index.php
  ```

### 2. Konfigurera .env-fil (VIKTIGT!)

1. Kopiera `.env.example` till `.env`:
   ```
   cp .env.example .env
   ```

2. Öppna `.env` och uppdatera följande:
   ```
   DB_PASSWORD=ditt_riktiga_lösenord
   MAIL_PASSWORD=ditt_email_lösenord
   ```

3. Verifiera att databasens credentials stämmer:
   ```
   DB_HOST=database-5018906310.webspace-host.com
   DB_DATABASE=dbs14910556
   DB_USERNAME=dbu3925798
   ```

### 3. Sätt filrättigheter

Sätt korrekt behörighet på storage-mappen:
```bash
chmod -R 755 storage/
chmod -R 755 storage/logs/
```

### 4. Skapa databastabeller

Kör denna SQL i phpMyAdmin eller din MySQL-klient:

```sql
CREATE TABLE IF NOT EXISTS `grundteam_applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `application_type` VARCHAR(20) NOT NULL DEFAULT 'individual',
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `region` VARCHAR(100) NOT NULL,
  `individual_contribution` TEXT NULL,
  `org_name` VARCHAR(255) NULL,
  `org_contribution` TEXT NULL,
  `org_motive` TEXT NULL,
  `gdpr_consent` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_application_type` (`application_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Testa installationen

Besök din webbplats:
- `https://countuskurds.com` - Ska visa startsidan
- Testa språkväxling via dropdown
- Fyll i och skicka testformulär

## Felsökning

### Problem: 500 Internal Server Error

**Lösning 1:** Kontrollera PHP-versionen
```bash
php -v  # Ska visa 8.3 eller högre
```

**Lösning 2:** Kontrollera felloggar
- Titta i `storage/logs/app.log`
- Kontakta ditt webbhotell för PHP error logs

**Lösning 3:** Verifiera .htaccess
- Kontrollera att mod_rewrite är aktiverat
- Kontakta support om du behöver aktivera det

### Problem: Databas-anslutning misslyckas

**Lösning:**
1. Verifiera credentials i `.env`-filen
2. Kolla att databasservern tillåter anslutningar från din webbserver
3. Testa anslutning via phpMyAdmin

### Problem: Email skickas inte

**Lösning:**
1. Kontrollera SMTP-credentials i `.env`
2. Verifiera att port 465 är öppen (SSL)
3. Testa email-inställningar via ett email-klient först
4. Kontrollera `storage/logs/app.log` för email-fel

### Problem: Språk visar inte korrekt

**Lösning:**
1. Kontrollera att alla filer i `resources/lang/` finns
2. Verifiera att PHP mbstring extension är aktiverad
3. Säkerställ att filerna är UTF-8 utan BOM

## Säkerhet

### Viktiga säkerhetsåtgärder:

1. **SSL-certifikat**: Aktivera alltid HTTPS
2. **Skydda .env**: Filen får ALDRIG vara tillgänglig via webben
3. **Uppdatera lösenord**: Byt standardlösenord i `.env`
4. **Backup**: Ta regelbundna säkerhetskopior av databasen
5. **Loggövervakning**: Kontrollera `storage/logs/` regelbundet

## Support

För teknisk support:
- Email: info@countuskurds.com
- Dokumentation: Se /docs/ mappen

## Underhåll

### Regelbundna uppgifter:

1. **Säkerhetskopior** (veckovis):
   ```bash
   mysqldump -u dbu3925798 -p dbs14910556 > backup_$(date +%Y%m%d).sql
   ```

2. **Rensa gamla loggar** (månadsvis):
   ```bash
   # Arkivera eller radera gamla loggfiler från storage/logs/
   ```

3. **Övervaka diskutrymme**:
   ```bash
   du -sh storage/logs/
   ```

## Versionshantering

Denna installation är optimerad för:
- PHP 8.3+
- MySQL 5.7+
- Apache med mod_rewrite

Senast uppdaterad: 2024-12-21