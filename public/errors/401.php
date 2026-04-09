<?php
declare(strict_types=1);
http_response_code(401);
$statusTitle = '401 - Ej autentiserad';
$statusMessage = 'Du maste logga in for att komma at denna sida. Ange dina inloggningsuppgifter.';
include __DIR__ . '/generic.php';
