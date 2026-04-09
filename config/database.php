<?php
declare(strict_types=1);

// Database configuration for Count Us Kurds
// Uses environment variables with fallback to direct values for web hosting

return [
    'driver' => 'mysql',
    
    // Use env() values from .env
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => (int) env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', ''),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    
    // Character set - always use utf8mb4 for full Unicode support
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
