<?php
declare(strict_types=1);

$statusTitle = $statusTitle ?? '500 - Internt serverfel';
$statusMessage = $statusMessage ?? 'Ett oväntat fel inträffade. Försök igen senare.';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f3f4f6;color:#111827;display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px}
        .card{max-width:680px;background:#fff;border-radius:14px;padding:32px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
        h1{margin:0 0 10px;color:#ed1c24;font-size:28px}
        p{margin:0 0 18px;line-height:1.6}
        a{display:inline-block;background:#ed1c24;color:#fff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:600}
    </style>
</head>
<body>
    <main class="card">
        <h1><?= htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/">Till startsidan</a>
    </main>
</body>
</html>
