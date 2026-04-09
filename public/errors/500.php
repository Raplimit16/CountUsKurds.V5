<?php
declare(strict_types=1);
http_response_code(500);
$statusTitle = '500 - Internt serverfel';
$statusMessage = 'Ett ovantat fel uppstod pa servern. Vart team har meddelats. Forsok igen senare.';
include __DIR__ . '/generic.php';
