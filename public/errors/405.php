<?php
declare(strict_types=1);
$statusTitle = $statusTitle ?? '405 - Metod inte tillåten';
$statusMessage = $statusMessage ?? 'HTTP-metoden stöds inte för denna åtgärd.';
include __DIR__ . '/generic.php';
