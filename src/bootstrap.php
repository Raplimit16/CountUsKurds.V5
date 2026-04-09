<?php
/**
 * Count Us Kurds - Bootstrap File
 * Initializes the application
 */

declare(strict_types=1);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LANG_PATH', ROOT_PATH . '/lang');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Load environment variables
loadEnv(ROOT_PATH . '/.env');

// Load configuration
$config = require CONFIG_PATH . '/app.php';

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Autoloader
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = SRC_PATH . '/';
    
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize database connection
$db = null;
try {
    $db = new PDO(
        sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['database']['host'],
            $config['database']['port'],
            $config['database']['name'],
            $config['database']['charset']
        ),
        $config['database']['user'],
        $config['database']['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    if ($config['app']['debug']) {
        die('Database connection failed: ' . $e->getMessage());
    }
    die('Database connection failed. Please try again later.');
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    
    session_start();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Load environment variables from file
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        
        if ($key !== '') {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

/**
 * Get translation
 */
function __($key, $locale = null, $params = []): string
{
    global $config;
    static $translations = [];
    
    $locale = $locale ?? ($_SESSION['locale'] ?? $config['app']['default_locale'] ?? 'en');
    
    if (!isset($translations[$locale])) {
        $file = LANG_PATH . "/{$locale}.php";
        $translations[$locale] = file_exists($file) ? require $file : [];
    }
    
    $text = $translations[$locale][$key] ?? $translations['en'][$key] ?? $key;
    
    foreach ($params as $param => $value) {
        $text = str_replace(":{$param}", $value, $text);
    }
    
    return $text;
}

/**
 * Escape HTML
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get CSRF token
 */
function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Verify CSRF token
 */
function csrf_verify($token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', (string)$token);
}

/**
 * Redirect helper
 */
function redirect(string $url, int $code = 302): void
{
    header("Location: $url", true, $code);
    exit;
}

/**
 * Get client IP
 */
function client_ip(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR'] 
        ?? $_SERVER['HTTP_X_REAL_IP'] 
        ?? $_SERVER['REMOTE_ADDR'] 
        ?? 'unknown';
}

/**
 * Flash message helper
 */
function flash(string $key, $value = null)
{
    if ($value === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    $_SESSION['flash'][$key] = $value;
}

/**
 * Asset URL helper
 */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

/**
 * URL helper
 */
function url(string $path = ''): string
{
    global $config;
    return rtrim($config['app']['url'], '/') . '/' . ltrim($path, '/');
}

// Make config and db globally available
$GLOBALS['config'] = $config;
$GLOBALS['db'] = $db;
