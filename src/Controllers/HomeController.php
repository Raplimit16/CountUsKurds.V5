<?php
/**
 * Home Controller - Public homepage
 */

namespace App\Controllers;

use App\Services\Database;

class HomeController
{
    protected Database $db;
    protected array $config;
    protected string $locale;
    
    public function __construct(Database $db, array $config, string $locale)
    {
        $this->db = $db;
        $this->config = $config;
        $this->locale = $locale;
    }
    
    public function index(): void
    {
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get language info
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        
        // Page data
        $pageTitle = __('home.title', $this->locale);
        $pageDescription = __('home.description', $this->locale);
        
        include TEMPLATE_PATH . '/public/home.php';
    }
    
    private function getStatistics(): array
    {
        try {
            // Count applications
            $totalApplications = $this->db->count('applications');
            $individualCount = $this->db->count('applications', "application_type = 'individual'");
            $organizationCount = $this->db->count('applications', "application_type = 'organization'");
            
            // Get country distribution
            $countries = $this->db->select('
                SELECT country, COUNT(*) as count 
                FROM applications 
                GROUP BY country 
                ORDER BY count DESC 
                LIMIT 10
            ');
            
            // Calculate estimated Kurdish count
            $estimatedCount = $this->db->selectOne('
                SELECT 
                    SUM(CASE WHEN application_type = "individual" THEN COALESCE(household_size, 1) ELSE 0 END) +
                    SUM(CASE WHEN application_type = "organization" THEN COALESCE(org_member_count * org_kurdish_percentage / 100, 0) ELSE 0 END) as total
                FROM applications
            ');
            
            return [
                'total_applications' => $totalApplications,
                'individual_count' => $individualCount,
                'organization_count' => $organizationCount,
                'estimated_kurds' => (int)($estimatedCount['total'] ?? 0),
                'countries' => $countries,
            ];
        } catch (\Exception $e) {
            return [
                'total_applications' => 0,
                'individual_count' => 0,
                'organization_count' => 0,
                'estimated_kurds' => 0,
                'countries' => [],
            ];
        }
    }
}
