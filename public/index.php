<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

use CountUsKurds\Http\Controllers\ApplicationController;

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if ($isHttps) {
    header('Strict-Transport-Security: max-age=63072000; includeSubDomains');
}

if (!headers_sent() && ($appConfig['security']['enable_csp'] ?? false)) {
    $styleHosts = implode(' ', ['\'self\'', ...($appConfig['security']['allowed_style_hosts'] ?? [])]);
    $fontHosts = implode(' ', ['\'self\'', ...($appConfig['security']['allowed_font_hosts'] ?? [])]);
    $scriptHosts = implode(' ', ['\'self\'', ...($appConfig['security']['allowed_script_hosts'] ?? [])]);
    $connectHosts = implode(' ', ['\'self\'', ...($appConfig['security']['allowed_connect_hosts'] ?? [])]);
    $imgHosts = implode(' ', ['\'self\'', 'data:', ...($appConfig['security']['allowed_img_hosts'] ?? [])]);

    $csp = [
        "default-src 'self'",
        "script-src {$scriptHosts}",
        "style-src {$styleHosts}",
        "font-src {$fontHosts} data:",
        "img-src {$imgHosts}",
        "connect-src {$connectHosts}",
        "frame-ancestors 'self'",
    ];

    header('Content-Security-Policy: ' . implode('; ', $csp));
}

$controller = new ApplicationController($translator, $submissionService);
$controller->handle();
