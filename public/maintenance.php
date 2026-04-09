<?php
/**
 * Count Us Kurds - Maintenance Mode Page
 * 
 * To enable maintenance mode:
 * 1. Create file: storage/.maintenance
 * 2. Traffic will be redirected here
 * 
 * To disable:
 * 1. Delete storage/.maintenance
 */
declare(strict_types=1);

http_response_code(503);
header('Retry-After: 3600');
header('Cache-Control: no-cache, no-store, must-revalidate');
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Underhall - Count Us Kurds</title>
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
        
        .container {
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 48px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .icon {
            margin-bottom: 24px;
        }
        
        .icon svg {
            width: 80px;
            height: 80px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #f8fafc;
        }
        
        p {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 24px;
        }
        
        .progress {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #ed1c24, #ff4757);
            border-radius: 2px;
            animation: loading 2s ease-in-out infinite;
        }
        
        @keyframes loading {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
        
        .info {
            font-size: 14px;
            color: #64748b;
        }
        
        .logo {
            margin-top: 32px;
            opacity: 0.6;
            font-size: 14px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            
            <h1>Vi uppdaterar sidan</h1>
            
            <p>Count Us Kurds genomgar just nu underhall for att ge dig en battre upplevelse. Vi ar snart tillbaka!</p>
            
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
            
            <p class="info">Forvantat fardig: om nagra minuter</p>
        </div>
        
        <div class="logo">Count Us Kurds</div>
    </div>
</body>
</html>
