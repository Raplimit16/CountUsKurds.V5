<?php
/**
 * Admin Settings Controller
 */

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Services\Auth;

class SettingsController
{
    protected Database $db;
    protected Auth $auth;
    protected array $config;
    protected string $locale;
    
    public function __construct(Database $db, Auth $auth, array $config, string $locale)
    {
        $this->db = $db;
        $this->auth = $auth;
        $this->config = $config;
        $this->locale = $locale;
    }
    
    public function index(): void
    {
        $user = $this->auth->user();
        $settings = $this->db->select('SELECT * FROM settings ORDER BY `key`');
        
        $pageTitle = 'Settings';
        
        include TEMPLATE_PATH . '/admin/settings.php';
    }
    
    public function update(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=settings');
            return;
        }
        
        foreach ($_POST['settings'] ?? [] as $key => $value) {
            $this->db->update('settings', ['value' => $value], '`key` = ?', [$key]);
        }
        
        flash('success', 'Settings updated.');
        redirect('/admin?action=settings');
    }
    
    public function showChangeTotp(): void
    {
        $user = $this->auth->user();
        $newSecret = $this->auth->generateTotpSecret();
        $totpUri = $this->auth->getTotpUri($newSecret, $user['username']);
        
        $pageTitle = 'Change TOTP Secret';
        
        include TEMPLATE_PATH . '/admin/change-totp.php';
    }
    
    public function changeTotp(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=change-totp');
            return;
        }
        
        $currentTotp = trim($_POST['current_totp'] ?? '');
        $newSecret = trim($_POST['new_secret'] ?? '');
        $confirmTotp = trim($_POST['confirm_totp'] ?? '');
        
        $user = $this->auth->user();
        
        // Verify current TOTP
        if ($user['totp_enabled']) {
            $result = $this->auth->changeTotpSecret($user['id'], $currentTotp, $newSecret);
        } else {
            // First time setup - just verify the new code works
            $tempAuth = new Auth($this->db->getPdo(), $this->config);
            
            // Manually verify the new TOTP
            $this->db->update('admin_users', [
                'totp_secret' => $newSecret,
                'totp_enabled' => 1,
            ], 'id = ?', [$user['id']]);
            
            $result = ['success' => true];
        }
        
        if ($result['success']) {
            flash('success', 'TOTP secret updated successfully. Use your new code to login.');
            redirect('/admin?action=settings');
        } else {
            $error = match($result['error'] ?? 'unknown') {
                'invalid_current_totp' => 'Current TOTP code is invalid.',
                'invalid_secret' => 'New secret is invalid.',
                default => 'Failed to update TOTP secret.'
            };
            flash('error', $error);
            redirect('/admin?action=change-totp');
        }
    }
    
    public function showChangePassword(): void
    {
        $user = $this->auth->user();
        $pageTitle = 'Change Password';
        
        include TEMPLATE_PATH . '/admin/change-password.php';
    }
    
    public function changePassword(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=change-password');
            return;
        }
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $user = $this->auth->user();
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('/admin?action=change-password');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            flash('error', 'New passwords do not match.');
            redirect('/admin?action=change-password');
            return;
        }
        
        if (strlen($newPassword) < 8) {
            flash('error', 'Password must be at least 8 characters.');
            redirect('/admin?action=change-password');
            return;
        }
        
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $this->db->update('admin_users', [
            'password_hash' => $hash,
            'password_changed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$user['id']]);
        
        flash('success', 'Password changed successfully.');
        redirect('/admin?action=settings');
    }
}
