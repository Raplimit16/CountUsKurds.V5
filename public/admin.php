<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

use CountUsKurds\Http\Controllers\AdminController;

$controller = new AdminController();
$controller->handle();
