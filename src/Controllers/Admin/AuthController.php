<?php
/**
 * Admin Auth Controller
 */

namespace App\Controllers\Admin;

use App\Services\Auth;

class AuthController
{
    protected Auth $auth;
    protected array $config;
    protected string $locale;
    
    public function __construct(Auth $auth, array $config, string $locale)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->locale = $locale;
    }
    
    public function showLogin(): void
    {
        if ($this->auth->check()) {
            redirect('/admin?action=dashboard');
        }
        
        $error = flash('error');
        $success = flash('success');
        
        include TEMPLATE_PATH . '/admin/login.php';
    }
    
    public function login(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session. Please try again.');
            redirect('/admin?action=login');
            return;
        }
        
        $method = $_POST['login_method'] ?? 'password';
        $username = trim($_POST['username'] ?? '');
        
        if ($method === 'totp') {
            $code = trim($_POST['totp_code'] ?? '');
            $result = $this->auth->attemptTotp($username, $code);
        } else {
            $password = $_POST['password'] ?? '';
            $result = $this->auth->attemptPassword($username, $password);
        }
        
        if ($result['success']) {
            if (!empty($result['requires_2fa'])) {
                redirect('/admin?action=verify-2fa');
            } else {
                flash('success', 'Welcome back!');
                redirect('/admin?action=dashboard');
            }
        } else {
            $error = match($result['error'] ?? 'unknown') {
                'invalid_credentials' => 'Invalid username or password.',
                'invalid_totp' => 'Invalid 2FA code.',
                'account_locked' => 'Account locked. Try again in ' . ceil(($result['retry_after'] ?? 900) / 60) . ' minutes.',
                'totp_not_enabled' => 'TOTP is not enabled for this account.',
                default => 'Login failed. Please try again.'
            };
            flash('error', $error);
            redirect('/admin?action=login');
        }
    }
    
    public function showLoginTotp(): void
    {
        if ($this->auth->check()) {
            redirect('/admin?action=dashboard');
        }
        
        $error = flash('error');
        
        include TEMPLATE_PATH . '/admin/login-totp.php';
    }
    
    public function loginTotp(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=login-totp');
            return;
        }
        
        $username = trim($_POST['username'] ?? '');
        $code = trim($_POST['totp_code'] ?? '');
        
        $result = $this->auth->attemptTotp($username, $code);
        
        if ($result['success']) {
            redirect('/admin?action=dashboard');
        } else {
            flash('error', 'Invalid username or 2FA code.');
            redirect('/admin?action=login-totp');
        }
    }
    
    public function showVerify2fa(): void
    {
        if (!isset($_SESSION['pending_2fa_user_id'])) {
            redirect('/admin?action=login');
        }
        
        $error = flash('error');
        
        include TEMPLATE_PATH . '/admin/verify-2fa.php';
    }
    
    public function verify2fa(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=verify-2fa');
            return;
        }
        
        $code = trim($_POST['totp_code'] ?? '');
        $result = $this->auth->verify2fa($code);
        
        if ($result['success']) {
            redirect('/admin?action=dashboard');
        } else {
            flash('error', 'Invalid 2FA code.');
            redirect('/admin?action=verify-2fa');
        }
    }
    
    public function showResetPassword(): void
    {
        $error = flash('error');
        $success = flash('success');
        
        include TEMPLATE_PATH . '/admin/reset-password.php';
    }
    
    public function resetPassword(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=reset-password');
            return;
        }
        
        $username = trim($_POST['username'] ?? '');
        $totpCode = trim($_POST['totp_code'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($newPassword !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
            redirect('/admin?action=reset-password');
            return;
        }
        
        if (strlen($newPassword) < 8) {
            flash('error', 'Password must be at least 8 characters.');
            redirect('/admin?action=reset-password');
            return;
        }
        
        $result = $this->auth->resetPasswordWithTotp($username, $totpCode, $newPassword);
        
        if ($result['success']) {
            flash('success', 'Password reset successfully. You can now login.');
            redirect('/admin?action=login');
        } else {
            $error = match($result['error'] ?? 'unknown') {
                'user_not_found' => 'User not found.',
                'totp_not_enabled' => '2FA is not enabled for this account.',
                'invalid_totp' => 'Invalid 2FA code.',
                'password_too_short' => 'Password must be at least 8 characters.',
                default => 'Password reset failed.'
            };
            flash('error', $error);
            redirect('/admin?action=reset-password');
        }
    }
}
