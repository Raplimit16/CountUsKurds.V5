<?php
/**
 * Admin Dashboard Controller
 */

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Services\Auth;

class DashboardController
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
        
        // Statistics
        $stats = [
            'total' => $this->db->count('applications'),
            'pending' => $this->db->count('applications', "status = 'pending'"),
            'approved' => $this->db->count('applications', "status = 'approved'"),
            'today' => $this->db->count('applications', "DATE(created_at) = CURDATE()"),
            'this_week' => $this->db->count('applications', "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"),
            'this_month' => $this->db->count('applications', "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"),
        ];
        
        // Recent applications
        $recentApplications = $this->db->select('
            SELECT id, full_name, email, application_type, country, status, created_at
            FROM applications 
            ORDER BY created_at DESC 
            LIMIT 10
        ');
        
        // Applications by country
        $byCountry = $this->db->select('
            SELECT country, COUNT(*) as count 
            FROM applications 
            GROUP BY country 
            ORDER BY count DESC 
            LIMIT 10
        ');
        
        // Applications by type
        $byType = $this->db->select('
            SELECT application_type, COUNT(*) as count 
            FROM applications 
            GROUP BY application_type
        ');
        
        // Recent activity
        $recentActivity = $this->db->select('
            SELECT al.*, au.username 
            FROM activity_log al 
            LEFT JOIN admin_users au ON al.user_id = au.id 
            ORDER BY al.created_at DESC 
            LIMIT 10
        ');
        
        $pageTitle = 'Dashboard';
        
        include TEMPLATE_PATH . '/admin/dashboard.php';
    }
}
