<?php
declare(strict_types=1);

/**
 * Configuration file for Count Us Kurds PHP application
 * Optimized for standard web hosting environments (PHP 8.3+)
 */

return [
    // Application name
    'name' => 'Count Us Kurds',
    
    // Default language (sv, en, ku, ckb, ar, fa, fr, de, tr)
    'default_locale' => env('APP_LOCALE', 'sv'),
    
    // All supported languages with their display labels and text direction
    'supported_locales' => [
        'sv' => [
            'label' => 'Svenska',
            'dir' => 'ltr',
        ],
        'en' => [
            'label' => 'English',
            'dir' => 'ltr',
        ],
        'ku' => [
            'label' => 'Kurdî (Kurmanji)',
            'dir' => 'ltr',
        ],
        'ckb' => [
            'label' => 'سۆرانی (Sorani)',
            'dir' => 'rtl',
        ],
        'ar' => [
            'label' => 'العربية (Arabic)',
            'dir' => 'rtl',
        ],
        'tr' => [
            'label' => 'Türkçe',
            'dir' => 'ltr',
        ],
    ],
    
    // Security settings
    'security' => [
        // Enable Content Security Policy headers
        'enable_csp' => true,
        
        // Allowed external script sources (for CDN, analytics, etc)
        'allowed_script_hosts' => [
            'https://cdn.tailwindcss.com',
            'https://www.googletagmanager.com',
        ],
        
        // Allowed external style sources (for fonts, CSS CDN, etc)
        'allowed_style_hosts' => [
            'https://fonts.googleapis.com',
        ],
        
        // Allowed font sources
        'allowed_font_hosts' => [
            'https://fonts.gstatic.com',
        ],

        // Allowed external connect sources (analytics beacons, APIs)
        'allowed_connect_hosts' => [
            'https://www.google-analytics.com',
            'https://region1.google-analytics.com',
        ],

        // Allowed external image sources
        'allowed_img_hosts' => [
            'https://www.google-analytics.com',
        ],
    ],
];
