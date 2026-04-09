<?php
/**
 * Count Us Kurds - Admin Panel Entry
 */

declare(strict_types=1);

// Security headers for admin
header('X-Frame-Options: DENY');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

// Get action
$action = $_GET['action'] ?? 'dashboard';

// Public admin actions (no auth required)
$publicActions = ['login', 'login-totp', 'reset-password', 'verify-2fa'];

// Check authentication
if (!in_array($action, $publicActions) && !$auth->check()) {
    redirect('/admin?action=login');
}

// Route admin actions
switch ($action) {
    // Authentication
    case 'login':
        $controller = new App\Controllers\Admin\AuthController($auth, $config, $locale);
        if ($method === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'login-totp':
        $controller = new App\Controllers\Admin\AuthController($auth, $config, $locale);
        if ($method === 'POST') {
            $controller->loginTotp();
        } else {
            $controller->showLoginTotp();
        }
        break;
        
    case 'verify-2fa':
        $controller = new App\Controllers\Admin\AuthController($auth, $config, $locale);
        if ($method === 'POST') {
            $controller->verify2fa();
        } else {
            $controller->showVerify2fa();
        }
        break;
        
    case 'reset-password':
        $controller = new App\Controllers\Admin\AuthController($auth, $config, $locale);
        if ($method === 'POST') {
            $controller->resetPassword();
        } else {
            $controller->showResetPassword();
        }
        break;
        
    case 'logout':
        $auth->logout();
        flash('success', __('logged_out'));
        redirect('/admin?action=login');
        break;
    
    // Dashboard
    case 'dashboard':
        $controller = new App\Controllers\Admin\DashboardController($database, $auth, $config, $locale);
        $controller->index();
        break;
    
    // Applications
    case 'applications':
        $controller = new App\Controllers\Admin\ApplicationController($database, $auth, $config, $locale);
        $controller->index();
        break;
        
    case 'application-view':
        $controller = new App\Controllers\Admin\ApplicationController($database, $auth, $config, $locale);
        $controller->view((int)($_GET['id'] ?? 0));
        break;
        
    case 'application-status':
        $controller = new App\Controllers\Admin\ApplicationController($database, $auth, $config, $locale);
        $controller->updateStatus();
        break;
        
    case 'application-delete':
        $controller = new App\Controllers\Admin\ApplicationController($database, $auth, $config, $locale);
        $controller->delete((int)($_GET['id'] ?? 0));
        break;
        
    case 'export':
        $controller = new App\Controllers\Admin\ApplicationController($database, $auth, $config, $locale);
        $controller->export();
        break;
    
    // Settings
    case 'settings':
        $controller = new App\Controllers\Admin\SettingsController($database, $auth, $config, $locale);
        if ($method === 'POST') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;
        
    case 'change-totp':
        $controller = new App\Controllers\Admin\SettingsController($database, $auth, $config, $locale);
        if ($method === 'POST') {
            $controller->changeTotp();
        } else {
            $controller->showChangeTotp();
        }
        break;
        
    case 'change-password':
        $controller = new App\Controllers\Admin\SettingsController($database, $auth, $config, $locale);
        if ($method === 'POST') {
            $controller->changePassword();
        } else {
            $controller->showChangePassword();
        }
        break;
    
    // Statistics
    case 'statistics':
        $controller = new App\Controllers\Admin\StatisticsController($database, $auth, $config, $locale);
        $controller->index();
        break;
    
    // Activity log
    case 'activity':
        $controller = new App\Controllers\Admin\ActivityController($database, $auth, $config, $locale);
        $controller->index();
        break;
        
    default:
        redirect('/admin?action=dashboard');
        break;
}
