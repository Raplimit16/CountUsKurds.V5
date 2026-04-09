<?php
declare(strict_types=1);
http_response_code(419);
$statusTitle = '419 - Sessionen har gatt ut';
$statusMessage = 'Din session har gatt ut av sakerhetsskal. Ladda om sidan och forsok igen.';
include __DIR__ . '/generic.php';
