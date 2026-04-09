# Database Setup för Count Us Kurds

## Översikt

Denna mapp innehåller alla SQL-filer som behövs för att sätta upp databasen.

## Filer

- `schema.sql` - Huvudsaklig databas-schema med alla tabeller

## Installation

### Via phpMyAdmin (Rekommenderat för webbhotell)

1. Logga in på phpMyAdmin
2. Välj din databas: `dbs14910556`
3. Klicka på "SQL"-fliken
4. Kopiera innehållet från `schema.sql`
5. Klistra in och kör SQL-koden
6. Verifiera att tabellen `grundteam_applications` skapades

### Via MySQL Command Line

```bash
mysql -h database-5018906310.webspace-host.com -u dbu3925798 -p dbs14910556 < schema.sql
```

## Tabellstruktur

### grundteam_applications

Lagrar alla ansökningar till Foundation Team.

**Kolumner:**
- `id` - Unikt ID (auto-increment)
- `application_type` - 'individual' eller 'group'
- `name` - Namn eller kontaktperson
- `email` - Email (UNIQUE)
- `region` - Vald region/diaspora
- `individual_contribution` - Individuellt bidrag (NULL för grupper)
- `org_name` - Organisationsnamn (NULL för individuella)
- `org_contribution` - Organisationens bidrag (NULL för individuella)
- `org_motive` - Organisationens motivation (NULL för individuella)
- `gdpr_consent` - GDPR-samtycke (1 = ja)
- `created_at` - Skapad tidsstämpel
- `updated_at` - Uppdaterad tidsstämpel

**Index:**
- PRIMARY KEY på `id`
- UNIQUE KEY på `email` (förhindrar duplicerade ansökningar)
- INDEX på `application_type` (snabbare filtrering)
- INDEX på `created_at` (snabbare datumsökningar)
- INDEX på `region` (snabbare region-filtrering)

## Backup

### Skapa backup

```bash
mysqldump -h database-5018906310.webspace-host.com -u dbu3925798 -p dbs14910556 > backup_$(date +%Y%m%d).sql
```

### Återställ från backup

```bash
mysql -h database-5018906310.webspace-host.com -u dbu3925798 -p dbs14910556 < backup_20241221.sql
```

## Underhåll

### Kontrollera tabellstorlek

```sql
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)",
    table_rows
FROM information_schema.TABLES
WHERE table_schema = 'dbs14910556'
    AND table_name = 'grundteam_applications';
```

### Optimera tabell

```sql
OPTIMIZE TABLE grundteam_applications;
```

### Visa senaste ansökningar

```sql
SELECT 
    application_type,
    name,
    email,
    region,
    created_at
FROM grundteam_applications
ORDER BY created_at DESC
LIMIT 10;
```

## Säkerhet

- Email-fältet är UNIQUE för att förhindra duplicerade ansökningar
- Använd alltid prepared statements (redan implementerat i PHP-koden)
- Ta regelbundna backups
- Övervaka onormal aktivitet i loggarna