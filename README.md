# Count Us Kurds

En modern, flerspr\u00e5kig webbplats f\u00f6r Count Us Kurds-initiativet - en s\u00e4ker och oberoende global plattform som kartl\u00e4gger, synligg\u00f6r och st\u00e4rker den demografiska och kulturella n\u00e4rvaron av kurder v\u00e4rlden \u00f6ver.

## \u2728 Funktioner

- \ud83c\udf0d **9 spr\u00e5k**: Engelska, Svenska, Kurmanji, Sorani, Arabiska, Farsi, Franska, Tyska, Turkiska
- \ud83d\udcdd **Ansökningsformulär**: För både individuella och organisations-ansökningar till Foundation Team
- \ud83d\udd12 **GDPR-kompatibel**: Fullständig integritetsskydd och datatransparens
- \ud83d\udce7 **Email-automatisering**: Automatiska bekräftelse-email på alla språk
- \ud83d\udcf1 **Responsiv design**: Optimerad för mobil, surfplatta och desktop
- \u26a1 **Höga prestanda**: Optimerad med caching, compression och lazy loading

## \ud83d\udee0\ufe0f Teknisk Stack

- **Backend**: PHP 8.3+
- **Databas**: MySQL 5.7+ / MariaDB 10.3+
- **Webbserver**: Apache med mod_rewrite
- **Email**: SMTP (Strato)
- **S\u00e4kerhet**: CSRF-skydd, SQL injection-skydd, XSS-skydd

## \ud83d\ude80 Snabbstart

### Systemkrav

- PHP 8.3 eller högre
- MySQL 5.7+ eller MariaDB 10.3+
- Apache med mod_rewrite aktiverat
- Minst 256MB PHP-minne
- SSL-certifikat (rekommenderas starkt)

### Installation

1. **Klona eller ladda ner projektet**
   ```bash
   git clone https://github.com/countuskurds/website.git
   cd website
   ```

2. **Konfigurera miljövariabler**
   ```bash
   cp .env.example .env
   nano .env  # Uppdatera databasuppgifter och email-konfiguration
   ```

3. **Skapa databasen**
   - Importera `database/schema.sql` via phpMyAdmin eller MySQL CLI
   ```bash
   mysql -h ditt_host -u ditt_användarnamn -p ditt_databas < database/schema.sql
   ```

4. **Sätt filbehörigheter**
   ```bash
   chmod -R 755 storage/
   chmod -R 775 storage/logs/
   chmod 644 .env
   ```

5. **Testa installationen**
   - Besök din webbplats i en webbläsare
   - Testa språkväxling
   - Skicka testformulär

För detaljerade instruktioner, se [INSTALLATION.md](INSTALLATION.md) och [DEPLOYMENT.md](DEPLOYMENT.md).

## \ud83d\udcc1 Projektstruktur

```
countuskurds/
├── app/                        # Applikationskod
│   ├── Http/
│   │   └── Controllers/       # HTTP Controllers
│   ├── Services/              # Business logic & services
│   └── Support/               # Helper functions & utilities
├── bootstrap/                 # Application bootstrap
├── config/                    # Konfigurationsfiler
│   ├── app.php               # Huvudkonfiguration
│   └── database.php          # Databaskonfiguration
├── database/                  # Databas-schema och migrations
│   ├── schema.sql            # Huvudschema
│   └── README.md             # Databasdokumentation
├── docs/                      # Dokumentation
├── public/                    # Publik root (DocumentRoot)
│   ├── assets/               # CSS, JS, bilder
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   ├── .htaccess            # Apache-konfiguration
│   └── index.php            # Front controller
├── resources/                 # Vyer och språkfiler
│   ├── lang/                 # Översättningar (9 språk)
│   │   ├── en.php
│   │   ├── sv.php
│   │   ├── ku.php
│   │   ├── ckb.php
│   │   ├── ar.php
│   │   ├── fa.php
│   │   ├── fr.php
│   │   ├── de.php
│   │   └── tr.php
│   └── views/                # PHP-templates
│       └── home.php
├── storage/                   # Lagring och cache
│   └── logs/                 # Applikationsloggar
├── .env                       # Miljökonfiguration (EJ i git)
├── .env.example              # Exempel på miljökonfiguration
├── .htaccess                 # Root Apache-konfiguration
├── index.php                 # Root redirect
├── INSTALLATION.md           # Installationsguide
├── DEPLOYMENT.md             # Deployment-guide
└── README.md                 # Denna fil
```

## \ud83c\udf0d Språk

Webbplatsen stöder följande språk:

| Kod | Språk | Riktning |
|-----|-------|----------|
| en | English | LTR |
| sv | Svenska | LTR |
| ku | Kurdî (Kurmanji) | LTR |
| ckb | سۆرانی (Sorani) | RTL |
| ar | العربية (Arabic) | RTL |
| fa | فارسی (Farsi) | RTL |
| fr | Français | LTR |
| de | Deutsch | LTR |
| tr | Türkçe | LTR |

### Lägga till nytt språk

1. Skapa ny språkfil: `resources/lang/XX.php` (där XX är språkkoden)
2. Kopiera struktur från `resources/lang/en.php`
3. Översätt alla texter
4. Lägg till språket i `config/app.php` under `supported_locales`
5. Testa språkväxlingen

## \ud83d\udce7 Email-konfiguration

Webbplatsen skickar automatiska bekräftelse-email när någon ansöker till Foundation Team.

### Konfigurera SMTP (Strato)

I `.env`:
```
MAIL_FROM_ADDRESS=info@countuskurds.com
MAIL_FROM_NAME="Count Us Kurds"
MAIL_HOST=smtp.strato.com
MAIL_PORT=587
MAIL_USERNAME=info@countuskurds.com
MAIL_PASSWORD=ditt_lösenord
MAIL_ENCRYPTION=tls
```

För andra email-leverantörer, justera `MAIL_HOST`, `MAIL_PORT` och `MAIL_ENCRYPTION` enligt deras specifikationer.

## \ud83d\udd12 Säkerhet

### Inbyggda säkerhetsfunktioner:

- \u2705 **CSRF-skydd**: Alla formulär skyddade med CSRF-tokens
- \u2705 **SQL Injection-skydd**: Prepared statements för alla databasfrågor
- \u2705 **XSS-skydd**: Alla outputs escapade med htmlspecialchars
- \u2705 **Security Headers**: X-Frame-Options, X-Content-Type-Options, CSP
- \u2705 **HTTPS-omdirigering**: Automatisk omdirigering till HTTPS (när aktiverad)
- \u2705 **Skyddade filer**: .env och config-filer skyddade via .htaccess

### Rekommenderade säkerhetsåtgärder:

1. **Använd HTTPS**: Aktivera SSL-certifikat (Let's Encrypt är gratis)
2. **Starka lösenord**: Minst 16 tecken för databas och email
3. **Regelbundna backups**: Dagliga backups av databas och filer
4. **Övervaka loggar**: Kontrollera `storage/logs/app.log` regelbundet
5. **Håll uppdaterad**: Se till att PHP och MySQL är uppdaterade

## \ud83d\udccb Databas

### Schema

Webbplatsen använder en enda tabell:

**`grundteam_applications`** - Lagrar alla ansökningar till Foundation Team

Kolumner:
- `id` - Unikt ID
- `application_type` - 'individual' eller 'group'
- `name` - Fullständigt namn
- `email` - Email (UNIQUE)
- `region` - Vald region/diaspora
- `individual_contribution` - Individuellt bidrag
- `org_name` - Organisationsnamn (för grupper)
- `org_contribution` - Organisationens bidrag (för grupper)
- `org_motive` - Organisationens motivation (för grupper)
- `gdpr_consent` - GDPR-samtycke (1 = ja)
- `created_at` - Skapad datum
- `updated_at` - Uppdaterad datum

### Backup

**Skapa backup:**
```bash
mysqldump -h database-5018906310.webspace-host.com -u dbu3925798 -p dbs14910556 > backup_$(date +%Y%m%d).sql
```

**Återställ från backup:**
```bash
mysql -h database-5018906310.webspace-host.com -u dbu3925798 -p dbs14910556 < backup_20241221.sql
```

## \ud83d\udee0\ufe0f Utveckling

### Lokal utveckling

1. Använd PHP:s inbyggda webbserver:
   ```bash
   php -S localhost:8000 -t public/
   ```

2. Eller använd XAMPP/MAMP:
   - Placera projektet i `htdocs/`
   - Besök `http://localhost/countuskurds/`

### Felsökning

**Aktivera debug-läge** (endast i utveckling!):

I `.env`:
```
APP_DEBUG=true
```

**Visa felmeddelanden** (endast temporärt):

I `bootstrap/app.php`:
```php
ini_set('display_errors', '1');
error_reporting(E_ALL);
```

**Kolla loggar:**
```bash
tail -f storage/logs/app.log
```

## \ud83d\udcca Övervakning & Underhåll

### Regelbundna uppgifter:

1. **Kontrollera loggar** (veckovis):
   ```bash
   tail -100 storage/logs/app.log
   ```

2. **Databas-backup** (dagligen):
   - Se backup-instruktioner ovan
   - Automatisera via cron

3. **Optimera databas** (månadsvis):
   ```sql
   OPTIMIZE TABLE grundteam_applications;
   ```

4. **Rensa gamla loggar** (månadsvis):
   ```bash
   # Arkivera eller radera loggar äldre än 30 dagar
   find storage/logs/ -name "*.log" -mtime +30 -delete
   ```

## \ud83d\udcdd Dokumentation

- [INSTALLATION.md](INSTALLATION.md) - Detaljerad installationsguide
- [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment till production
- [database/README.md](database/README.md) - Databasdokumentation
- [docs/email.md](docs/email.md) - Email-konfiguration

## \ud83e\udd1d Bidra

Count Us Kurds är ett community-drivet projekt. Vi välkomnar bidrag!

### Hur du kan bidra:

1. **Översättningar**: Hjälp till att förbättra eller lägga till nya språk
2. **Buggfixar**: Rapportera eller fixa buggar
3. **Funktioner**: Föreslå eller implementera nya funktioner
4. **Dokumentation**: Förbättra dokumentationen

### Riktlinjer:

- Följ PHP 8.3 best practices
- Använd meaningful commit messages
- Testa alla ändringar innan du pushar
- Dokumentera nya funktioner

## \ud83d\udce9 Support

För support eller frågor:
- **Email**: info@countuskurds.com
- **Issues**: Skapa ett issue i GitHub-repot
- **Dokumentation**: Se `/docs/` mappen

## \ud83d\udcdc Licens

Copyright © 2024 Count Us Kurds. All rights reserved.

Detta projekt är proprietärt och ägs av Count Us Kurds-initiativet. Ingen del av denna kod får användas, kopieras eller distribueras utan skriftligt tillstånd.

## \ud83d\ude80 Versions-historik

### v1.0.0 (2024-12-21)
- \u2728 Initial release
- \u2705 9 språk stöd
- \u2705 Ansökningsformulär med validation
- \u2705 Email-automatisering
- \u2705 GDPR-kompatibel
- \u2705 Responsiv design
- \u2705 Optimerad för webbhotell

---

**Byggt med \u2764\ufe0f för den kurdiska gemenskapen världen över.**
