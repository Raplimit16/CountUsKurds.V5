<?php
/**
 * Count Us Kurds - Configuration
 */

return [
    // Application
    'app' => [
        'name' => 'Count Us Kurds',
        'url' => getenv('APP_URL') ?: 'https://countuskurds.com',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') === 'true',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Stockholm',
    ],
    
    // Database
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: 3306,
        'name' => getenv('DB_DATABASE') ?: 'countuskurds',
        'user' => getenv('DB_USERNAME') ?: 'root',
        'pass' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
    
    // Mail
    'mail' => [
        'host' => getenv('MAIL_HOST') ?: 'smtp.strato.com',
        'port' => (int)(getenv('MAIL_PORT') ?: 465),
        'user' => getenv('MAIL_USERNAME') ?: '',
        'pass' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'ssl',
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'info@countuskurds.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Count Us Kurds',
    ],
    
    // Security
    'security' => [
        'session_lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 120) * 60,
        'rate_limit_max' => (int)(getenv('FORM_RATE_LIMIT_MAX') ?: 5),
        'rate_limit_window' => (int)(getenv('FORM_RATE_LIMIT_WINDOW') ?: 900),
        'login_max_attempts' => 5,
        'login_lockout_time' => 900, // 15 minutes
    ],
    
    // Supported Languages
    'languages' => [
        'sv' => ['name' => 'Svenska', 'dir' => 'ltr', 'flag' => '🇸🇪'],
        'en' => ['name' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
        'ku' => ['name' => 'Kurdî (Kurmancî)', 'dir' => 'ltr', 'flag' => '🟢🟡🔴'],
        'ckb' => ['name' => 'کوردی (سۆرانی)', 'dir' => 'rtl', 'flag' => '🟢🟡🔴'],
        'ar' => ['name' => 'العربية', 'dir' => 'rtl', 'flag' => '🇸🇦'],
        'tr' => ['name' => 'Türkçe', 'dir' => 'ltr', 'flag' => '🇹🇷'],
        'de' => ['name' => 'Deutsch', 'dir' => 'ltr', 'flag' => '🇩🇪'],
        'fr' => ['name' => 'Français', 'dir' => 'ltr', 'flag' => '🇫🇷'],
        'es' => ['name' => 'Español', 'dir' => 'ltr', 'flag' => '🇪🇸'],
        'fi' => ['name' => 'Suomi', 'dir' => 'ltr', 'flag' => '🇫🇮'],
        'no' => ['name' => 'Norsk', 'dir' => 'ltr', 'flag' => '🇳🇴'],
        'he' => ['name' => 'עברית', 'dir' => 'rtl', 'flag' => '🇮🇱'],
        'fa' => ['name' => 'فارسی', 'dir' => 'rtl', 'flag' => '🇮🇷'],
        'nl' => ['name' => 'Nederlands', 'dir' => 'ltr', 'flag' => '🇳🇱'],
        'it' => ['name' => 'Italiano', 'dir' => 'ltr', 'flag' => '🇮🇹'],
        'ru' => ['name' => 'Русский', 'dir' => 'ltr', 'flag' => '🇷🇺'],
        'da' => ['name' => 'Dansk', 'dir' => 'ltr', 'flag' => '🇩🇰'],
        'pl' => ['name' => 'Polski', 'dir' => 'ltr', 'flag' => '🇵🇱'],
        'pt' => ['name' => 'Português', 'dir' => 'ltr', 'flag' => '🇵🇹'],
    ],
    
    // Kurdish Regions
    'kurdish_regions' => [
        'bakur' => 'Bakur (North Kurdistan / Turkey)',
        'bashur' => 'Bashur (South Kurdistan / Iraq)',
        'rojava' => 'Rojava (West Kurdistan / Syria)',
        'rojhelat' => 'Rojhelat (East Kurdistan / Iran)',
        'diaspora' => 'Diaspora',
    ],
    
    // Analytics
    'analytics' => [
        'ga_id' => getenv('GA_MEASUREMENT_ID') ?: '',
    ],
];
