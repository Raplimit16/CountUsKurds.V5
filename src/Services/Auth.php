<?php
/**
 * Authentication Service
 * Handles login, logout, TOTP, password reset
 */

namespace App\Services;

class Auth
{
    private \PDO $db;
    private array $config;
    
    public function __construct(\PDO $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * Attempt login with password
     */
    public function attemptPassword(string $username, string $password): array
    {
        $user = $this->findUser($username);
        
        if (!$user) {
            return ['success' => false, 'error' => 'invalid_credentials'];
        }
        
        if ($this->isLocked($user)) {
            $remaining = strtotime($user['locked_until']) - time();
            return ['success' => false, 'error' => 'account_locked', 'retry_after' => $remaining];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            $this->recordFailedAttempt($user['id']);
            return ['success' => false, 'error' => 'invalid_credentials'];
        }
        
        // Check if TOTP is required
        if ($user['totp_enabled']) {
            $_SESSION['pending_2fa_user_id'] = $user['id'];
            return ['success' => true, 'requires_2fa' => true];
        }
        
        return $this->completeLogin($user);
    }
    
    /**
     * Attempt login with TOTP only
     */
    public function attemptTotp(string $username, string $code): array
    {
        $user = $this->findUser($username);
        
        if (!$user) {
            return ['success' => false, 'error' => 'invalid_credentials'];
        }
        
        if ($this->isLocked($user)) {
            return ['success' => false, 'error' => 'account_locked'];
        }
        
        if (!$user['totp_enabled'] || !$user['totp_secret']) {
            return ['success' => false, 'error' => 'totp_not_enabled'];
        }
        
        if (!$this->verifyTotp($user['totp_secret'], $code)) {
            $this->recordFailedAttempt($user['id']);
            return ['success' => false, 'error' => 'invalid_totp'];
        }
        
        return $this->completeLogin($user);
    }
    
    /**
     * Verify 2FA for pending login
     */
    public function verify2fa(string $code): array
    {
        $userId = $_SESSION['pending_2fa_user_id'] ?? null;
        
        if (!$userId) {
            return ['success' => false, 'error' => 'no_pending_2fa'];
        }
        
        $user = $this->findUserById($userId);
        
        if (!$user || !$this->verifyTotp($user['totp_secret'], $code)) {
            return ['success' => false, 'error' => 'invalid_totp'];
        }
        
        unset($_SESSION['pending_2fa_user_id']);
        return $this->completeLogin($user);
    }
    
    /**
     * Reset password using TOTP
     */
    public function resetPasswordWithTotp(string $username, string $totpCode, string $newPassword): array
    {
        $user = $this->findUser($username);
        
        if (!$user) {
            return ['success' => false, 'error' => 'user_not_found'];
        }
        
        if (!$user['totp_enabled'] || !$user['totp_secret']) {
            return ['success' => false, 'error' => 'totp_not_enabled'];
        }
        
        if (!$this->verifyTotp($user['totp_secret'], $totpCode)) {
            return ['success' => false, 'error' => 'invalid_totp'];
        }
        
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'password_too_short'];
        }
        
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $this->db->prepare('
            UPDATE admin_users 
            SET password_hash = ?, failed_attempts = 0, locked_until = NULL, password_changed_at = NOW()
            WHERE id = ?
        ');
        $stmt->execute([$hash, $user['id']]);
        
        $this->logActivity($user['id'], 'password_reset', 'admin_users', $user['id']);
        
        return ['success' => true];
    }
    
    /**
     * Change TOTP secret
     */
    public function changeTotpSecret(int $userId, string $currentTotp, string $newSecret): array
    {
        $user = $this->findUserById($userId);
        
        if (!$user) {
            return ['success' => false, 'error' => 'user_not_found'];
        }
        
        // Verify current TOTP
        if ($user['totp_enabled'] && !$this->verifyTotp($user['totp_secret'], $currentTotp)) {
            return ['success' => false, 'error' => 'invalid_current_totp'];
        }
        
        // Validate new secret
        if (strlen($newSecret) < 16) {
            return ['success' => false, 'error' => 'invalid_secret'];
        }
        
        $stmt = $this->db->prepare('
            UPDATE admin_users SET totp_secret = ?, totp_enabled = 1 WHERE id = ?
        ');
        $stmt->execute([$newSecret, $userId]);
        
        $this->logActivity($userId, 'totp_secret_changed', 'admin_users', $userId);
        
        return ['success' => true];
    }
    
    /**
     * Generate new TOTP secret
     */
    public function generateTotpSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }
    
    /**
     * Get TOTP provisioning URI for authenticator apps
     */
    public function getTotpUri(string $secret, string $username): string
    {
        $issuer = 'CountUsKurds';
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            urlencode($issuer),
            urlencode($username),
            $secret,
            urlencode($issuer)
        );
    }
    
    /**
     * Logout user
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->logActivity($userId, 'logout', null, null);
        }
        
        $_SESSION = [];
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is logged in
     */
    public function check(): bool
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user
     */
    public function user(): ?array
    {
        if (!$this->check()) return null;
        return $this->findUserById($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    // ==================== PRIVATE METHODS ====================
    
    private function findUser(string $username): ?array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM admin_users WHERE username = ? OR email = ? LIMIT 1
        ');
        $stmt->execute([$username, $username]);
        return $stmt->fetch() ?: null;
    }
    
    private function findUserById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM admin_users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    private function isLocked(array $user): bool
    {
        if (!$user['locked_until']) return false;
        return strtotime($user['locked_until']) > time();
    }
    
    private function recordFailedAttempt(int $userId): void
    {
        $maxAttempts = $this->config['security']['login_max_attempts'];
        $lockoutTime = $this->config['security']['login_lockout_time'];
        
        $stmt = $this->db->prepare('
            UPDATE admin_users 
            SET failed_attempts = failed_attempts + 1,
                locked_until = CASE 
                    WHEN failed_attempts + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                    ELSE locked_until 
                END
            WHERE id = ?
        ');
        $stmt->execute([$maxAttempts, $lockoutTime, $userId]);
        
        $this->logActivity($userId, 'login_failed', 'admin_users', $userId);
    }
    
    private function completeLogin(array $user): array
    {
        // Clear failed attempts
        $stmt = $this->db->prepare('
            UPDATE admin_users 
            SET failed_attempts = 0, locked_until = NULL, last_login = NOW(), last_ip = ?
            WHERE id = ?
        ');
        $stmt->execute([client_ip(), $user['id']]);
        
        // Set session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        $this->logActivity($user['id'], 'login_success', 'admin_users', $user['id']);
        
        return ['success' => true, 'user' => $user];
    }
    
    private function verifyTotp(string $secret, string $code): bool
    {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) return false;
        
        $secret = $this->base32Decode($secret);
        if ($secret === '') return false;
        
        $timeSlice = floor(time() / 30);
        
        // Check current and adjacent time slices
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->calculateTotp($secret, $timeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function calculateTotp(string $secret, int $timeSlice): string
    {
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        
        $code = ((ord($hash[$offset]) & 0x7F) << 24) |
                ((ord($hash[$offset + 1]) & 0xFF) << 16) |
                ((ord($hash[$offset + 2]) & 0xFF) << 8) |
                (ord($hash[$offset + 3]) & 0xFF);
        
        return str_pad((string)($code % 1000000), 6, '0', STR_PAD_LEFT);
    }
    
    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input));
        if ($input === '') return '';
        
        $bits = '';
        for ($i = 0; $i < strlen($input); $i++) {
            $bits .= str_pad(decbin(strpos($alphabet, $input[$i])), 5, '0', STR_PAD_LEFT);
        }
        
        $output = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $output .= chr(bindec($chunk));
            }
        }
        
        return $output;
    }
    
    private function logActivity(int $userId, string $action, ?string $entityType, ?int $entityId): void
    {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO activity_log (user_id, action, entity_type, entity_id, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                client_ip(),
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break login for logging issues
        }
    }
}
