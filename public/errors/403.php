<?php
declare(strict_types=1);
http_response_code(403);
$statusTitle = '403 - Atkomst nekad';
$statusMessage = 'Du har inte behorighet att visa denna sida. Kontrollera att du har ratt att komma at denna resurs.';
include __DIR__ . '/generic.php';
