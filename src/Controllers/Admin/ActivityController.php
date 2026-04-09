<?php
/**
 * Admin Activity Controller
 */

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Services\Auth;

class ActivityController
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
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $total = $this->db->count('activity_log');
        $totalPages = ceil($total / $perPage);
        
        $activities = $this->db->select("
            SELECT al.*, au.username 
            FROM activity_log al 
            LEFT JOIN admin_users au ON al.user_id = au.id 
            ORDER BY al.created_at DESC 
            LIMIT {$perPage} OFFSET {$offset}
        ");
        
        $pageTitle = 'Activity Log';
        
        include TEMPLATE_PATH . '/admin/activity.php';
    }
}
