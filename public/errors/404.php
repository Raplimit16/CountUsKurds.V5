<?php
declare(strict_types=1);
http_response_code(404);
$statusTitle = '404 - Sidan hittades inte';
$statusMessage = 'Sidan du soker finns inte eller har flyttats. Kontrollera URL:en eller ga tillbaka till startsidan.';
include __DIR__ . '/generic.php';
