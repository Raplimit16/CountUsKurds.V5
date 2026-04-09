<?php
declare(strict_types=1);
http_response_code(429);
$statusTitle = '429 - For manga forfragningar';
$statusMessage = 'Du har gjort for manga forfragan pa kort tid. Vanta nagra minuter och forsok igen.';
include __DIR__ . '/generic.php';
