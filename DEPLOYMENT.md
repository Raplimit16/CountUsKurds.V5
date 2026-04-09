# Count Us Kurds - Deployment Guide för Webbhotel

## 🚀 Snabbstart

### Steg 1: Förbered filerna lokalt

```bash
# Kontrollera att alla filer finns
ls -la

# Verifiera att .env finns och är konfigurerad
cat .env
```

### Steg 2: Ladda upp till webbhotellet

#### Via FTP/SFTP (FileZilla, Cyberduck, etc)

1. Anslut till ditt webbhotel:
   - Host: `ftp.countuskurds.com` (eller din FTP-server)
   - Användarnamn: Ditt FTP-användarnamn
   - Lösenord: Ditt FTP-lösenord
   - Port: 21 (FTP) eller 22 (SFTP)

2. Navigera till din root-mapp:
   - Vanligtvis `public_html/` eller `htdocs/`

3. Ladda upp ALLA filer och mappar:
   ```
   /app/
   /bootstrap/
   /config/
   /database/
   /docs/
   /public/
   /resources/
   /storage/
   .htaccess
   .env
   index.php
   ```

#### Via SSH/Terminal (om tillgängligt)

```bash
# Anslut till servern
ssh dittanvändarnamn@countuskurds.com

# Navigera till root
cd public_html/

# Ladda upp via rsync eller scp
rsync -avz /local/path/to/countuskurds/ ./

# Eller med scp
scp -r /local/path/to/countuskurds/* dittanvändarnamn@countuskurds.com:/public_html/
```

### Steg 3: Sätt filbehörigheter

```bash
# Via SSH
chmod -R 755 storage/
chmod -R 755 storage/logs/
chmod 644 .env
chmod 644 .htaccess
chmod 644 public/.htaccess

# Gör storage skrivbar
chmod -R 775 storage/
```

**Via FTP:** Högerklicka på varje mapp → File Permissions → Sätt till 755 (eller 775 för storage)

### Steg 4: Skapa databasen

1. Logga in på phpMyAdmin (via ditt webbhotells kontrollpanel)
2. Välj databas: `dbs14910556`
3. Klicka "SQL"-fliken
4. Kopiera hela innehållet från `/database/schema.sql`
5. Klistra in och klicka "Kör"
6. Verifiera att tabellen `grundteam_applications` skapades under "Struktur"-fliken

### Steg 5: Testa installationen

1. Besök: `https://countuskurds.com`
2. Kontrollera att startsidan laddas
3. Testa språkväxling (dropdown i header)
4. Scrolla ner till formuläret
5. Fyll i testdata och skicka
6. Verifiera att du får ett bekräftelsemeddelande
7. Kolla i databasen att data sparades

## 🔧 Felsökning

### Problem: Blank sida eller 500 Error

**Aktivera error display (tillfälligt):**

Redigera `/bootstrap/app.php` och lägg till:
```php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
```

**Kolla loggar:**
```bash
# PHP error log (plats varierar beroende på webbhotel)
tail -f /path/to/error_log

# Applikationslogg
tail -f storage/logs/app.log
```

**Vanliga orsaker:**
- PHP-version < 8.3
- Saknade PHP-extensions (mbstring, mysqli)
- Felaktig filbehörighet på storage/
- .htaccess-problem (mod_rewrite ej aktiverad)

### Problem: Formuläret skickar men sparar inte

**Testa databasanslutning:**

Skapa testfil `/test-db.php`:
```php
<?php
require __DIR__ . '/bootstrap/app.php';

try {
    $conn = \CountUsKurds\Services\Database::connection();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
```

Besök: `https://countuskurds.com/test-db.php`

**Radera testfilen efteråt!**

### Problem: Email skickas inte

**Testa SMTP:**

Kontrollera att SMTP-credentials i `.env` är korrekta:
```
MAIL_HOST=smtp.strato.com
MAIL_PORT=587
MAIL_USERNAME=info@countuskurds.com
MAIL_PASSWORD=ditt_lösenord
MAIL_ENCRYPTION=tls
```

**Kolla loggar:**
```bash
tail -f storage/logs/app.log | grep -i mail
```

### Problem: CSS/JS laddas inte

**Kontrollera asset-path:**

I `.env`, sätt:
```
ASSET_BASE_PATH=
```

**Om du använder en subdirectory:**
```
ASSET_BASE_PATH=countuskurds
```

**Cache-problem:**
- Rensa webbläsarens cache
- Tryck Ctrl+Shift+R (eller Cmd+Shift+R på Mac)

## 🔒 Säkerhet

### Obligatoriska säkerhetsåtgärder:

1. **SSL-certifikat:**
   - Aktivera HTTPS via ditt webbhotells kontrollpanel
   - Använd Let's Encrypt (gratis) om tillgängligt
   - Uppdatera `.htaccess` för att tvinga HTTPS (redan konfigurerat)

2. **Skydda känsliga filer:**
   ```bash
   # Verifiera att dessa filer EJ är tillgängliga via webben:
   curl https://countuskurds.com/.env  # Ska ge 403 Forbidden
   curl https://countuskurds.com/config/database.php  # Ska ge 403 Forbidden
   ```

3. **Stark lösenord:**
   - Databas-lösenord: Minst 16 tecken, blandade tecken
   - Email-lösenord: Använd app-specific password från Strato

4. **Uppdatera regelbundet:**
   - Håll PHP uppdaterad
   - Övervaka säkerhetsloggar

## 📊 Övervakning

### Viktiga saker att övervaka:

1. **Diskutrymme:**
   ```bash
   du -sh storage/logs/
   ```

2. **Databas-storlek:**
   ```sql
   SELECT table_name, 
          ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
   FROM information_schema.TABLES 
   WHERE table_schema = 'dbs14910556';
   ```

3. **Felloggar:**
   ```bash
   tail -f storage/logs/app.log
   ```

## 💾 Backup-rutin

### Daglig backup (automatisera via cron):

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/path/to/backups"

# Backup databas
mysqldump -h database-5018906310.webspace-host.com \
-u dbu3925798 \
-p'YOUR_DB_PASSWORD' \
dbs14910556 > "$BACKUP_DIR/db_$DATE.sql"

# Backup filer
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" \
  /path/to/countuskurds/ \
  --exclude='storage/logs/*'

# Radera backups äldre än 30 dagar
find "$BACKUP_DIR" -name "*.sql" -mtime +30 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

**Schemalägg med cron:**
```bash
crontab -e

# Lägg till (körs varje dag kl 02:00):
0 2 * * * /path/to/backup.sh >> /path/to/backup.log 2>&1
```

## ✅ Checklista efter deployment

- [ ] Alla filer uppladdade
- [ ] Filbehörigheter satta korrekt
- [ ] .env konfigurerad med rätt värden
- [ ] Databas skapad och schema kört
- [ ] Startsidan laddas korrekt
- [ ] Alla 9 språk fungerar
- [ ] Formulär kan skickas och sparas
- [ ] Email-bekräftelse fungerar
- [ ] HTTPS aktiverat och fungerar
- [ ] Känsliga filer skyddade (.env, etc)
- [ ] Backup-rutin satt upp
- [ ] Övervakningsverktyg konfigurerade

## 📞 Support

För teknisk hjälp:
- Email: info@countuskurds.com
- Se `/docs/` för detaljerad dokumentation
- Läs `INSTALLATION.md` för grundläggande setup
