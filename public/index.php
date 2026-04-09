<?php
/**
 * Count Us Kurds - Main Entry Point
 */

declare(strict_types=1);

// Load bootstrap
require_once dirname(__DIR__) . '/src/bootstrap.php';

// Initialize services
$database = new App\Services\Database($db);
$auth = new App\Services\Auth($db, $config);

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Detect and set locale
$locale = $_GET['lang'] ?? $_SESSION['locale'] ?? $config['app']['default_locale'] ?? 'sv';
if (isset($config['languages'][$locale])) {
    $_SESSION['locale'] = $locale;
} else {
    $locale = 'sv';
    $_SESSION['locale'] = $locale;
}

// Router
try {
    // Admin routes
    if (strpos($uri, '/admin') === 0) {
        require __DIR__ . '/admin.php';
        exit;
    }
    
    // API routes
    if (strpos($uri, '/api') === 0) {
        require __DIR__ . '/api.php';
        exit;
    }
    
    // Public routes
    switch ($uri) {
        case '/':
            $controller = new App\Controllers\HomeController($database, $config, $locale);
            $controller->index();
            break;
            
        case '/register':
        case '/ansokan':
            $controller = new App\Controllers\ApplicationController($database, $config, $locale);
            if ($method === 'POST') {
                $controller->store();
            } else {
                $controller->create();
            }
            break;
            
        case '/thank-you':
        case '/tack':
            $controller = new App\Controllers\ApplicationController($database, $config, $locale);
            $controller->thankYou();
            break;
            
        case '/about':
        case '/om-oss':
            $controller = new App\Controllers\PageController($database, $config, $locale);
            $controller->about();
            break;
            
        case '/privacy':
        case '/integritetspolicy':
            $controller = new App\Controllers\PageController($database, $config, $locale);
            $controller->privacy();
            break;
            
        case '/contact':
        case '/kontakt':
            $controller = new App\Controllers\PageController($database, $config, $locale);
            $controller->contact();
            break;
            
        default:
            http_response_code(404);
            include TEMPLATE_PATH . '/errors/404.php';
            break;
    }
} catch (Exception $e) {
    error_log('Application error: ' . $e->getMessage());
    
    if ($config['app']['debug']) {
        echo '<pre>' . e($e->getMessage()) . '</pre>';
    } else {
        http_response_code(500);
        include TEMPLATE_PATH . '/errors/500.php';
    }
}
