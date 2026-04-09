<?php
declare(strict_types=1);

/**
 * Shared global helper functions for the Count Us Kurds web application.
 * Keep helpers framework-agnostic and side-effect free whenever possible.
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../../'));
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

/**
 * Resolve an absolute path relative to the project root.
 */
function base_path(string $path = ''): string
{
    return rtrim(BASE_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''), DIRECTORY_SEPARATOR);
}

function app_path(string $path = ''): string
{
    return base_path('app' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
}

function config_path(string $path = ''): string
{
    return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
}

function resource_path(string $path = ''): string
{
    return base_path('resources' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
}

function public_path(string $path = ''): string
{
    return base_path('public' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
}

function storage_path(string $path = ''): string
{
    return base_path('storage' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
}

/**
 * Lightweight environment loader. Supports simple KEY=VALUE pairs.
 */
function load_env(string $filePath): void
{
    if (!is_file($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);

        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($key === '') {
            continue;
        }

        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
        if (!array_key_exists($key, $_SERVER)) {
            $_SERVER[$key] = $value;
        }
        putenv("$key=$value");
    }
}

/**
 * Fetch an environment value with optional default.
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }

    return match (strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'empty', '(empty)' => '',
        'null', '(null)' => null,
        default => $value,
    };
}

/**
 * Retrieve a nested array value using dot notation.
 */
function array_get(array $data, string $key, mixed $default = null): mixed
{
    if ($key === '') {
        return $default;
    }

    $segments = explode('.', $key);
    foreach ($segments as $segment) {
        if (is_array($data) && array_key_exists($segment, $data)) {
            $data = $data[$segment];
        } else {
            return $default;
        }
    }

    return $data;
}

function asset(string $path): string
{
    $configured = env('ASSET_BASE_PATH');
    if (is_string($configured) && trim($configured) !== '') {
        $base = '/' . trim($configured, '/');
        return $base . '/' . ltrim($path, '/');
    }

    $scriptName = isset($_SERVER['SCRIPT_NAME'])
        ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME'])
        : '';

    $scriptDir = rtrim(dirname($scriptName), '/');
    if ($scriptDir === '.') {
        $scriptDir = '';
    }

    $projectRoot = preg_replace('#/public$#', '', $scriptDir, 1) ?? '';
    $projectRoot = trim($projectRoot, '/');

    $base = $projectRoot !== '' ? '/' . $projectRoot : '';

    return $base . '/' . ltrim($path, '/');
}

/**
 * Simple CSRF token helper backed by the PHP session.
 */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render branded error page and terminate response.
 */
function render_error_page(int $statusCode, ?string $title = null, ?string $message = null): never
{
    http_response_code($statusCode);

        $defaultTitle = match ($statusCode) {
        403 => '403 - Atkomst nekad',
        404 => '404 - Sidan hittades inte',
        405 => '405 - Metod inte tillaten',
        419 => '419 - Sessionen har gatt ut',
        429 => '429 - For manga forfraganingar',
        default => '500 - Internt serverfel',
    };

        $defaultMessage = match ($statusCode) {
        403 => 'Du har inte behorighet att se denna sida.',
        404 => 'Sidan du soker finns inte eller har flyttats.',
        405 => 'HTTP-metoden stods inte for denna resurs.',
        419 => 'Din session har gatt ut. Ladda om sidan och forsok igen.',
        429 => 'For manga forsok. Vanta en stund och forsok igen.',
        default => 'Ett ovantat fel intraffade. Forsok igen senare.',
    };

    $statusTitle = $title ?? $defaultTitle;
    $statusMessage = $message ?? $defaultMessage;

    $viewPath = public_path('errors' . DIRECTORY_SEPARATOR . $statusCode . '.php');
    $genericViewPath = public_path('errors' . DIRECTORY_SEPARATOR . 'generic.php');

    if (is_file($viewPath)) {
        include $viewPath;
        exit;
    }

    if (is_file($genericViewPath)) {
        include $genericViewPath;
        exit;
    }

    echo '<!DOCTYPE html><html lang="sv"><head><meta charset="UTF-8"><title>'
        . htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8')
        . '</title></head><body><h1>'
        . htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8')
        . '</h1><p>'
        . htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8')
        . '</p></body></html>';
    exit;
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!is_string($ip) || $ip === '') {
        return 'unknown';
    }

    if (strlen($ip) > 64) {
        return 'unknown';
    }

    return $ip;
}

/**
 * @return array{allowed: bool, remaining: int, retry_after: int}
 */
function rate_limit_check(string $key, int $maxAttempts, int $windowSeconds): array
{
    $driver = strtolower((string) env('RATE_LIMIT_DRIVER', 'db'));
    if ($driver === 'db') {
        $dbResult = rate_limit_check_db($key, $maxAttempts, $windowSeconds);
        if ($dbResult !== null) {
            return $dbResult;
        }
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key) ?? 'default';
    $dir = storage_path('rate_limits');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $file = $dir . DIRECTORY_SEPARATOR . $safeKey . '.json';
    $now = time();
    $cutoff = $now - $windowSeconds;
    $timestamps = [];

    if (is_file($file)) {
        $content = file_get_contents($file);
        $decoded = is_string($content) ? json_decode($content, true) : null;
        if (is_array($decoded)) {
            foreach ($decoded as $timestamp) {
                if (is_int($timestamp) && $timestamp >= $cutoff) {
                    $timestamps[] = $timestamp;
                }
            }
        }
    }

    if (count($timestamps) >= $maxAttempts) {
        sort($timestamps);
        $retryAfter = max(1, ($timestamps[0] + $windowSeconds) - $now);
        return [
            'allowed' => false,
            'remaining' => 0,
            'retry_after' => $retryAfter,
        ];
    }

    $timestamps[] = $now;
    file_put_contents($file, json_encode($timestamps), LOCK_EX);

    return [
        'allowed' => true,
        'remaining' => max(0, $maxAttempts - count($timestamps)),
        'retry_after' => 0,
    ];
}

/**
 * @return array{allowed: bool, remaining: int, retry_after: int}|null
 */
function rate_limit_check_db(string $key, int $maxAttempts, int $windowSeconds): ?array
{
    static $conn = null;
    static $tableChecked = false;

    $host = (string) env('DB_HOST', '');
    $port = (int) env('DB_PORT', 3306);
    $database = (string) env('DB_DATABASE', '');
    $username = (string) env('DB_USERNAME', '');
    $password = (string) env('DB_PASSWORD', '');

    if ($host === '' || $database === '' || $username === '') {
        return null;
    }

    if (!$conn instanceof \mysqli) {
        try {
            $conn = @new \mysqli($host, $username, $password, $database, $port);
            if ($conn->connect_errno !== 0) {
                return null;
            }
            $conn->set_charset('utf8mb4');
        } catch (\Throwable) {
            return null;
        }
    }

    if (!$tableChecked) {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `app_rate_limits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rate_key` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rate_key_created_at` (`rate_key`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        try {
            $ok = $conn->query($sql);
            if ($ok !== true) {
                return null;
            }
            $tableChecked = true;
        } catch (\Throwable) {
            return null;
        }
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key) ?? 'default';
    $windowSeconds = max(60, $windowSeconds);
    $maxAttempts = max(1, $maxAttempts);

    try {
        $deleteStmt = $conn->prepare(
            'DELETE FROM app_rate_limits WHERE rate_key = ? AND created_at < (NOW() - INTERVAL ? SECOND)'
        );
        if (!$deleteStmt) {
            return null;
        }
        $deleteStmt->bind_param('si', $safeKey, $windowSeconds);
        $deleteStmt->execute();
        $deleteStmt->close();

        $countStmt = $conn->prepare(
            'SELECT COUNT(*) AS cnt, MIN(created_at) AS oldest
             FROM app_rate_limits
             WHERE rate_key = ?'
        );
        if (!$countStmt) {
            return null;
        }
        $countStmt->bind_param('s', $safeKey);
        $countStmt->execute();
        $result = $countStmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $countStmt->close();

        $count = (int) ($row['cnt'] ?? 0);
        if ($count >= $maxAttempts) {
            $oldest = isset($row['oldest']) ? strtotime((string) $row['oldest']) : false;
            $retryAfter = 1;
            if (is_int($oldest) || is_float($oldest)) {
                $retryAfter = max(1, (int) (($oldest + $windowSeconds) - time()));
            }

            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $retryAfter,
            ];
        }

        $insertStmt = $conn->prepare('INSERT INTO app_rate_limits (rate_key, created_at) VALUES (?, NOW())');
        if (!$insertStmt) {
            return null;
        }
        $insertStmt->bind_param('s', $safeKey);
        $ok = $insertStmt->execute();
        $insertStmt->close();
        if (!$ok) {
            return null;
        }

        return [
            'allowed' => true,
            'remaining' => max(0, $maxAttempts - ($count + 1)),
            'retry_after' => 0,
        ];
    } catch (\Throwable) {
        return null;
    }
}
