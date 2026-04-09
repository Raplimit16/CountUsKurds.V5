<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

use CountUsKurds\Http\Controllers\AdminController;

// Security headers for admin area
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header('Cache-Control: no-cache, no-store, must-revalidate, private');
header('Pragma: no-cache');
header('Expires: 0');

// HSTS for HTTPS
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if ($isHttps) {
    header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
}

// Content Security Policy for admin
$csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline'",
    "style-src 'self' 'unsafe-inline'",
    "img-src 'self' data: https://countuskurds.com",
    "font-src 'self'",
    "connect-src 'self'",
    "frame-ancestors 'none'",
    "base-uri 'self'",
    "form-action 'self'",
];
header('Content-Security-Policy: ' . implode('; ', $csp));

$controller = new AdminController();
$controller->handle();
