<?php
/**
 * Count Us Kurds - Login Diagnostics Tool
 * 
 * VIKTIGT: Radera denna fil efter anvandning av sakerhetsskal!
 * 
 * Anvandning: Ladda upp till /public/diagnose.php och besok i webblasaren
 */
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__DIR__) . '/bootstrap/app.php';

use CountUsKurds\Services\Database;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Diagnostics - Count Us Kurds</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #1a1a2e; color: #eee; }
        h1 { color: #ed1c24; }
        h2 { color: #f59e0b; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 30px; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .info { color: #3b82f6; }
        pre { background: #0f0f23; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        code { background: #2d2d44; padding: 2px 6px; border-radius: 4px; }
        .box { background: #16213e; border: 1px solid #333; border-radius: 8px; padding: 20px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #333; }
        th { background: #0f0f23; }
        .btn { display: inline-block; background: #ed1c24; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; margin: 5px; }
        .btn:hover { background: #c41920; }
    </style>
</head>
<body>
    <h1>Login Diagnostics</h1>
    <p class="warning">VIKTIGT: Radera denna fil nar du ar klar!</p>

    <h2>1. Databasanslutning</h2>
    <div class="box">
    <?php
    $dbHost = env('DB_HOST', '');
    $dbName = env('DB_DATABASE', '');
    $dbUser = env('DB_USERNAME', '');
    $dbPass = env('DB_PASSWORD', '');
    
    echo "<p><strong>Host:</strong> <code>{$dbHost}</code></p>";
    echo "<p><strong>Database:</strong> <code>{$dbName}</code></p>";
    echo "<p><strong>Username:</strong> <code>{$dbUser}</code></p>";
    echo "<p><strong>Password:</strong> <code>" . str_repeat('*', min(8, strlen($dbPass))) . "</code> (" . strlen($dbPass) . " tecken)</p>";
    
    try {
        $conn = Database::connection();
        echo "<p class='success'>✓ Databasanslutning LYCKADES</p>";
        $dbConnected = true;
    } catch (Throwable $e) {
        echo "<p class='error'>✗ Databasanslutning MISSLYCKADES: " . htmlspecialchars($e->getMessage()) . "</p>";
        $dbConnected = false;
    }
    ?>
    </div>

    <h2>2. Admin-konfiguration (.env)</h2>
    <div class="box">
    <?php
    $authMode = env('ADMIN_AUTH_MODE', 'env');
    $adminTable = env('ADMIN_USERS_TABLE', 'admin_users');
    $totpSecret = env('ADMIN_TOTP_SECRET', '');
    $totpUsernames = env('ADMIN_TOTP_USERNAMES', '');
    $adminUsername = env('ADMIN_USERNAME', 'admin');
    
    echo "<table>";
    echo "<tr><th>Variabel</th><th>Varde</th><th>Status</th></tr>";
    echo "<tr><td>ADMIN_AUTH_MODE</td><td><code>{$authMode}</code></td><td>" . ($authMode === 'database' ? "<span class='info'>Anvander databas</span>" : "<span class='info'>Anvander .env</span>") . "</td></tr>";
    echo "<tr><td>ADMIN_USERS_TABLE</td><td><code>{$adminTable}</code></td><td>-</td></tr>";
    echo "<tr><td>ADMIN_TOTP_SECRET</td><td><code>" . (strlen($totpSecret) > 0 ? substr($totpSecret, 0, 4) . '...' : 'SAKNAS') . "</code></td><td>" . (strlen($totpSecret) >= 16 ? "<span class='success'>✓ OK</span>" : "<span class='error'>✗ For kort/saknas</span>") . "</td></tr>";
    echo "<tr><td>ADMIN_TOTP_USERNAMES</td><td><code>{$totpUsernames}</code></td><td>" . (strlen($totpUsernames) > 0 ? "<span class='success'>✓ OK</span>" : "<span class='warning'>Tomt</span>") . "</td></tr>";
    echo "<tr><td>ADMIN_USERNAME</td><td><code>{$adminUsername}</code></td><td>-</td></tr>";
    echo "</table>";
    ?>
    </div>

    <h2>3. Tabell: admin_users</h2>
    <div class="box">
    <?php
    if ($dbConnected) {
        try {
            $result = $conn->query("SHOW TABLES LIKE 'admin_users'");
            if ($result && $result->num_rows > 0) {
                echo "<p class='success'>✓ Tabellen 'admin_users' FINNS</p>";
                
                // Visa struktur
                $cols = $conn->query("DESCRIBE admin_users");
                echo "<h4>Tabellstruktur:</h4><table><tr><th>Kolumn</th><th>Typ</th><th>Null</th><th>Default</th></tr>";
                while ($col = $cols->fetch_assoc()) {
                    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Default']}</td></tr>";
                }
                echo "</table>";
                
                // Visa anvandare
                $users = $conn->query("SELECT id, username, totp_enabled, totp_secret, failed_attempts, locked_until, last_login FROM admin_users");
                echo "<h4>Anvandare i tabellen:</h4>";
                if ($users && $users->num_rows > 0) {
                    echo "<table><tr><th>ID</th><th>Username</th><th>TOTP</th><th>Secret</th><th>Failed</th><th>Locked</th><th>Last Login</th></tr>";
                    while ($user = $users->fetch_assoc()) {
                        $totpStatus = $user['totp_enabled'] ? "<span class='success'>ON</span>" : "<span class='warning'>OFF</span>";
                        $secretStatus = !empty($user['totp_secret']) ? "<span class='success'>" . substr($user['totp_secret'], 0, 4) . "...</span>" : "<span class='error'>SAKNAS</span>";
                        $lockedStatus = !empty($user['locked_until']) && strtotime($user['locked_until']) > time() ? "<span class='error'>{$user['locked_until']}</span>" : "<span class='success'>Nej</span>";
                        echo "<tr><td>{$user['id']}</td><td><strong>{$user['username']}</strong></td><td>{$totpStatus}</td><td>{$secretStatus}</td><td>{$user['failed_attempts']}</td><td>{$lockedStatus}</td><td>{$user['last_login']}</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='error'>✗ INGA ANVANDARE i tabellen!</p>";
                    echo "<p>Kor foljande SQL for att lagga till ceoadmin:</p>";
                    echo "<pre>INSERT INTO admin_users (username, totp_secret, totp_enabled) 
VALUES ('ceoadmin', 'RP6IMPOYMAJD72P2', 1);</pre>";
                }
            } else {
                echo "<p class='error'>✗ Tabellen 'admin_users' SAKNAS!</p>";
                echo "<p>Kor foljande SQL i phpMyAdmin:</p>";
                echo "<pre>" . htmlspecialchars(file_get_contents(base_path('database/admin_users_table.sql'))) . "</pre>";
            }
        } catch (Throwable $e) {
            echo "<p class='error'>✗ Fel vid kontroll: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='warning'>Kan inte kontrollera - ingen databasanslutning</p>";
    }
    ?>
    </div>

    <h2>4. TOTP-verifiering (Test)</h2>
    <div class="box">
    <?php
    $secret = $totpSecret;
    if (strlen($secret) >= 16) {
        // Generate current TOTP
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $clean = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret));
        $bits = '';
        for ($i = 0; $i < strlen($clean); $i++) {
            $val = strpos($alphabet, $clean[$i]);
            if ($val !== false) {
                $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
            }
        }
        $decodedSecret = '';
        $chunks = str_split($bits, 8);
        foreach ($chunks as $chunk) {
            if (strlen($chunk) === 8) {
                $decodedSecret .= chr(bindec($chunk));
            }
        }
        
        $timeSlice = (int) floor(time() / 30);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $decodedSecret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $binary = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        $otp = str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);
        
        echo "<p><strong>Aktuell TOTP-kod:</strong> <code style='font-size: 24px; color: #10b981;'>{$otp}</code></p>";
        echo "<p class='info'>Koden byts var 30:e sekund. Nuvarande tid: " . date('H:i:s') . "</p>";
        echo "<p>Jamfor med din Authenticator-app. Om koderna matchar ar TOTP korrekt konfigurerat.</p>";
    } else {
        echo "<p class='error'>ADMIN_TOTP_SECRET ar inte korrekt konfigurerat i .env</p>";
    }
    ?>
    </div>

    <h2>5. Senaste Loggar</h2>
    <div class="box">
    <?php
    if ($dbConnected) {
        try {
            $result = $conn->query("SHOW TABLES LIKE 'app_logs'");
            if ($result && $result->num_rows > 0) {
                $logs = $conn->query("SELECT * FROM app_logs WHERE channel LIKE '%admin%' OR message LIKE '%login%' ORDER BY id DESC LIMIT 20");
                if ($logs && $logs->num_rows > 0) {
                    echo "<table><tr><th>Tid</th><th>Level</th><th>Kanal</th><th>Meddelande</th></tr>";
                    while ($log = $logs->fetch_assoc()) {
                        $levelClass = match($log['level'] ?? '') {
                            'error' => 'error',
                            'warning' => 'warning',
                            'info' => 'info',
                            default => ''
                        };
                        $time = $log['created_at'] ?? $log['timestamp'] ?? '-';
                        echo "<tr><td>{$time}</td><td class='{$levelClass}'>{$log['level']}</td><td>{$log['channel']}</td><td>" . htmlspecialchars(substr($log['message'] ?? '', 0, 100)) . "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='warning'>Inga relevanta loggar hittades</p>";
                }
            } else {
                echo "<p class='warning'>Tabellen 'app_logs' finns inte</p>";
            }
        } catch (Throwable $e) {
            echo "<p class='error'>Fel: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    ?>
    </div>

    <h2>6. Snabbfix-SQL</h2>
    <div class="box">
    <p>Kopiera och kor detta i phpMyAdmin om admin_users inte finns eller ar tom:</p>
    <pre>
-- Skapa tabell om den saknas
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

-- Lagg till eller uppdatera ceoadmin
INSERT INTO `admin_users` (`username`, `totp_secret`, `totp_enabled`, `failed_attempts`)
VALUES ('ceoadmin', 'RP6IMPOYMAJD72P2', 1, 0)
ON DUPLICATE KEY UPDATE 
  `totp_secret` = 'RP6IMPOYMAJD72P2',
  `totp_enabled` = 1,
  `failed_attempts` = 0,
  `locked_until` = NULL;

-- Verifiera
SELECT * FROM admin_users;
    </pre>
    </div>

    <h2>7. Atgarder</h2>
    <div class="box">
    <p>Baserat pa diagnostiken ovan:</p>
    <ol>
        <li>Om <strong>admin_users saknas</strong> → Kor SQL i sektion 6</li>
        <li>Om <strong>ceoadmin saknas i tabellen</strong> → Kor INSERT i sektion 6</li>
        <li>Om <strong>kontot ar last</strong> → Kor: <code>UPDATE admin_users SET failed_attempts=0, locked_until=NULL WHERE username='ceoadmin';</code></li>
        <li>Om <strong>TOTP-koden inte matchar</strong> → Kontrollera att ADMIN_TOTP_SECRET i .env matchar din Authenticator</li>
    </ol>
    <p class="error" style="margin-top: 20px;">RADERA DENNA FIL NAR DU AR KLAR: /public/diagnose.php</p>
    </div>

</body>
</html>
