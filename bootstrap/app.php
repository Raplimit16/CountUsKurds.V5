<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Support/helpers.php';

// Load environment variables before anything else.
load_env(base_path('.env'));
load_env(base_path('.env.local'));

// Configure application timezone.
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Minimal PSR-4 autoloader for the application namespace.
spl_autoload_register(static function (string $class): void {
    $prefix = 'CountUsKurds\\';
    $baseDir = app_path();

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

// Ensure sessions are available for CSRF and flash handling.
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $secureCookieEnv = env('SESSION_SECURE_COOKIE', $isHttps);
    $secureCookie = is_bool($secureCookieEnv) ? $secureCookieEnv : $isHttps;
    $sameSite = (string) env('SESSION_SAMESITE', 'Strict');
    $sameSite = in_array($sameSite, ['Strict', 'Lax', 'None'], true) ? $sameSite : 'Strict';
    $sessionLifetimeSeconds = (int) env('SESSION_LIFETIME', 120) * 60;
    $sessionDriver = strtolower((string) env('SESSION_DRIVER', 'db'));

    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.gc_maxlifetime', (string) max(300, $sessionLifetimeSeconds));

    if ($sessionDriver === 'db') {
        $handler = new CountUsKurds\Support\DatabaseSessionHandler(max(300, $sessionLifetimeSeconds));
        if ($handler->isAvailable()) {
            session_set_save_handler($handler, true);
        }
    }

    @session_start([
        'cookie_httponly' => true,
        'cookie_secure' => $secureCookie,
        'cookie_samesite' => $sameSite,
        'cookie_lifetime' => 0,
    ]);

    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '0');
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => $secureCookie,
            'cookie_samesite' => $sameSite,
            'cookie_lifetime' => 0,
        ]);
    }
}

// Load configurations shared across the application.
$appConfig = require config_path('app.php');
$supportedLocales = $appConfig['supported_locales'] ?? [];
$defaultLocale = $appConfig['default_locale'] ?? 'sv';

// Instantiate shared services.
$translator = new CountUsKurds\Support\Translator(
    $defaultLocale,
    $supportedLocales
);

$submissionService = new CountUsKurds\Services\SubmissionService($translator);
$apiController = new CountUsKurds\Http\Controllers\ApiController($translator);
$privacyController = new CountUsKurds\Http\Controllers\PrivacyController($translator);

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(static function (\Throwable $exception): void {
    $debug = (bool) env('APP_DEBUG', false);
    @error_log(sprintf(
        'Unhandled exception: %s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    CountUsKurds\Support\Logger::error('Unhandled exception', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
    ]);

    if ($debug) {
        render_error_page(500, 'Unhandled Exception', $exception->getMessage());
    }

    render_error_page(500);
});

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if (!is_array($error)) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
    if (!in_array($error['type'] ?? 0, $fatalTypes, true)) {
        return;
    }

    CountUsKurds\Support\Logger::error('Fatal shutdown error', [
        'message' => (string) ($error['message'] ?? 'Unknown fatal error'),
        'file' => (string) ($error['file'] ?? ''),
        'line' => (int) ($error['line'] ?? 0),
    ]);
    @error_log(sprintf(
        'Fatal shutdown error: %s in %s:%d',
        (string) ($error['message'] ?? 'Unknown fatal error'),
        (string) ($error['file'] ?? ''),
        (int) ($error['line'] ?? 0)
    ));

    render_error_page(500);
});
