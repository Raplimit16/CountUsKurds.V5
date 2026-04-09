<?php
/**
 * Admin Statistics Controller
 */

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Services\Auth;

class StatisticsController
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
        
        // By country
        $byCountry = $this->db->select('
            SELECT country, COUNT(*) as count,
                   SUM(CASE WHEN application_type = "individual" THEN COALESCE(household_size, 1) ELSE 0 END) as individuals,
                   SUM(CASE WHEN application_type = "organization" THEN 1 ELSE 0 END) as organizations
            FROM applications 
            GROUP BY country 
            ORDER BY count DESC
        ');
        
        // By region
        $byRegion = $this->db->select('
            SELECT region, COUNT(*) as count 
            FROM applications 
            WHERE region IS NOT NULL AND region != ""
            GROUP BY region 
            ORDER BY count DESC
        ');
        
        // By month
        $byMonth = $this->db->select('
            SELECT DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count
            FROM applications 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month 
            ORDER BY month ASC
        ');
        
        // By status
        $byStatus = $this->db->select('
            SELECT status, COUNT(*) as count 
            FROM applications 
            GROUP BY status
        ');
        
        // Totals
        $totals = $this->db->selectOne('
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN application_type = "individual" THEN COALESCE(household_size, 1) ELSE 0 END) as total_individuals,
                SUM(CASE WHEN application_type = "organization" THEN COALESCE(org_member_count, 0) ELSE 0 END) as total_org_members
            FROM applications
        ');
        
        $pageTitle = 'Statistics';
        
        include TEMPLATE_PATH . '/admin/statistics.php';
    }
}
