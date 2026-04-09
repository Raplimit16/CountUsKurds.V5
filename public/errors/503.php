<?php
declare(strict_types=1);
http_response_code(503);
$statusTitle = '503 - Tjansten otillganglig';
$statusMessage = 'Webbplatsen genomgar underhall just nu. Vi ar snart tillbaka!';
include __DIR__ . '/generic.php';
