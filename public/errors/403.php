<?php
declare(strict_types=1);
$statusTitle = $statusTitle ?? '403 - Åtkomst nekad';
$statusMessage = $statusMessage ?? 'Du har inte behörighet att visa denna sida.';
include __DIR__ . '/generic.php';
