<?php
declare(strict_types=1);
$statusTitle = $statusTitle ?? '429 - För många förfrågningar';
$statusMessage = $statusMessage ?? 'Du har gjort för många försök. Vänta en stund och försök igen.';
include __DIR__ . '/generic.php';
