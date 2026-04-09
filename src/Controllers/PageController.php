<?php
/**
 * Page Controller - Static pages
 */

namespace App\Controllers;

use App\Services\Database;

class PageController
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
    
    public function about(): void
    {
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        
        $pageTitle = __('about.title', $this->locale);
        
        include TEMPLATE_PATH . '/public/about.php';
    }
    
    public function privacy(): void
    {
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        
        $pageTitle = __('privacy.title', $this->locale);
        
        include TEMPLATE_PATH . '/public/privacy.php';
    }
    
    public function contact(): void
    {
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        
        $pageTitle = __('contact.title', $this->locale);
        
        include TEMPLATE_PATH . '/public/contact.php';
    }
}
