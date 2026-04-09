<?php
declare(strict_types=1);

$statusCode = http_response_code() ?: 500;
$statusTitle = $statusTitle ?? '';
$statusMessage = $statusMessage ?? '';

// Default titles and messages based on status code
$defaults = [
    400 => ['title' => '400 - Felaktig forfragan', 'message' => 'Servern kunde inte forsta din forfragan.'],
    401 => ['title' => '401 - Ej autentiserad', 'message' => 'Du maste logga in for att komma at denna sida.'],
    403 => ['title' => '403 - Atkomst nekad', 'message' => 'Du har inte behorighet att visa denna sida.'],
    404 => ['title' => '404 - Sidan hittades inte', 'message' => 'Sidan du soker finns inte eller har flyttats.'],
    405 => ['title' => '405 - Metod ej tillaten', 'message' => 'HTTP-metoden stods inte for denna resurs.'],
    408 => ['title' => '408 - Timeout', 'message' => 'Servern vantade for lange pa din forfragan.'],
    419 => ['title' => '419 - Sessionen har gatt ut', 'message' => 'Din session har gatt ut. Ladda om sidan och forsok igen.'],
    429 => ['title' => '429 - For manga forfragningar', 'message' => 'For manga forsok. Vanta en stund och forsok igen.'],
    500 => ['title' => '500 - Internt serverfel', 'message' => 'Ett ovantat fel uppstod. Forsok igen senare.'],
    502 => ['title' => '502 - Bad Gateway', 'message' => 'Servern mottog ett ogiltigt svar.'],
    503 => ['title' => '503 - Tjansten otillganglig', 'message' => 'Tjansten ar tillfalligt otillganglig. Forsok igen senare.'],
    504 => ['title' => '504 - Gateway Timeout', 'message' => 'Servern svarade inte i tid.'],
];

if ($statusTitle === '' && isset($defaults[$statusCode])) {
    $statusTitle = $defaults[$statusCode]['title'];
}
if ($statusMessage === '' && isset($defaults[$statusCode])) {
    $statusMessage = $defaults[$statusCode]['message'];
}

// Fallback defaults
if ($statusTitle === '') {
    $statusTitle = "{$statusCode} - Fel uppstod";
}
if ($statusMessage === '') {
    $statusMessage = 'Ett ovantat fel uppstod. Forsok igen senare.';
}

// Escape for output
$safeTitle = htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8');
$safeMessage = htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8');

// Determine icon based on status code range
$iconSvg = match (true) {
    $statusCode >= 500 => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ed1c24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    $statusCode === 404 => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
    $statusCode === 403 || $statusCode === 401 => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
    $statusCode === 429 => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    default => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
};
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $safeTitle ?> - Count Us Kurds</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.6;
        }
        
        .error-container {
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        
        .error-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 48px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .error-icon {
            margin-bottom: 24px;
            opacity: 0.9;
        }
        
        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: #ed1c24;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 0 4px 12px rgba(237, 28, 36, 0.3);
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 16px;
        }
        
        .error-message {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 32px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ed1c24, #ff4757);
            color: white;
            box-shadow: 0 4px 14px rgba(237, 28, 36, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(237, 28, 36, 0.5);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .logo {
            margin-top: 32px;
            opacity: 0.6;
        }
        
        .logo img {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            font-size: 14px;
            color: #64748b;
            margin-top: 8px;
        }
        
        @media (max-width: 480px) {
            .error-card {
                padding: 32px 20px;
            }
            .error-code {
                font-size: 56px;
            }
            .error-title {
                font-size: 20px;
            }
            .error-actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <?= $iconSvg ?>
            </div>
            
            <div class="error-code"><?= $statusCode ?></div>
            
            <h1 class="error-title"><?= $safeTitle ?></h1>
            
            <p class="error-message"><?= $safeMessage ?></p>
            
            <div class="error-actions">
                <a href="/" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Till startsidan
                </a>
                <button onclick="history.back()" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Ga tillbaka
                </button>
            </div>
        </div>
        
        <div class="logo">
            <div class="logo-text">Count Us Kurds</div>
        </div>
    </div>
</body>
</html>
