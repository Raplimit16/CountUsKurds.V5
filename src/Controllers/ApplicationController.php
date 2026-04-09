<?php
/**
 * Application Controller - Handle public applications
 */

namespace App\Controllers;

use App\Services\Database;

class ApplicationController
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
    
    public function create(): void
    {
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        $regions = $this->config['kurdish_regions'];
        
        $pageTitle = __('register.title', $this->locale);
        $errors = [];
        $old = [];
        
        include TEMPLATE_PATH . '/public/register.php';
    }
    
    public function store(): void
    {
        // Verify CSRF
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', __('error.csrf', $this->locale));
            redirect('/register');
            return;
        }
        
        // Rate limiting
        $ip = client_ip();
        $rateKey = 'register_' . md5($ip);
        
        // Validate input
        $errors = $this->validate($_POST);
        
        if (!empty($errors)) {
            $languages = $this->config['languages'];
            $currentLang = $languages[$this->locale] ?? $languages['en'];
            $dir = $currentLang['dir'];
            $regions = $this->config['kurdish_regions'];
            $pageTitle = __('register.title', $this->locale);
            $old = $_POST;
            
            include TEMPLATE_PATH . '/public/register.php';
            return;
        }
        
        // Prepare data
        $type = $_POST['application_type'] ?? 'individual';
        
        $data = [
            'application_type' => $type,
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'region' => trim($_POST['region'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
            'gdpr_consent' => isset($_POST['gdpr_consent']) ? 1 : 0,
            'newsletter_consent' => isset($_POST['newsletter_consent']) ? 1 : 0,
            'ip_address' => $ip,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            'locale' => $this->locale,
            'source' => trim($_POST['source'] ?? ''),
        ];
        
        if ($type === 'individual') {
            $data['birth_year'] = !empty($_POST['birth_year']) ? (int)$_POST['birth_year'] : null;
            $data['gender'] = $_POST['gender'] ?? null;
            $data['kurdish_dialect'] = trim($_POST['kurdish_dialect'] ?? '');
            $data['household_size'] = !empty($_POST['household_size']) ? (int)$_POST['household_size'] : 1;
        } else {
            $data['org_name'] = trim($_POST['org_name'] ?? '');
            $data['org_type'] = trim($_POST['org_type'] ?? '');
            $data['org_website'] = trim($_POST['org_website'] ?? '');
            $data['org_member_count'] = !empty($_POST['org_member_count']) ? (int)$_POST['org_member_count'] : null;
            $data['org_kurdish_percentage'] = !empty($_POST['org_kurdish_percentage']) ? (int)$_POST['org_kurdish_percentage'] : null;
        }
        
        // Insert
        try {
            $this->db->insert('applications', $data);
            
            // Send notification email (optional)
            // $this->sendNotification($data);
            
            flash('success', __('register.success', $this->locale));
            redirect('/thank-you');
        } catch (\Exception $e) {
            error_log('Application insert error: ' . $e->getMessage());
            flash('error', __('error.general', $this->locale));
            redirect('/register');
        }
    }
    
    public function thankYou(): void
    {
        $languages = $this->config['languages'];
        $currentLang = $languages[$this->locale] ?? $languages['en'];
        $dir = $currentLang['dir'];
        
        $pageTitle = __('thankyou.title', $this->locale);
        
        include TEMPLATE_PATH . '/public/thank-you.php';
    }
    
    private function validate(array $data): array
    {
        $errors = [];
        
        if (empty($data['full_name'])) {
            $errors['full_name'] = __('validation.required', $this->locale, ['field' => __('field.name', $this->locale)]);
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = __('validation.email', $this->locale);
        }
        
        if (empty($data['country'])) {
            $errors['country'] = __('validation.required', $this->locale, ['field' => __('field.country', $this->locale)]);
        }
        
        if (empty($data['gdpr_consent'])) {
            $errors['gdpr_consent'] = __('validation.gdpr', $this->locale);
        }
        
        // Organization-specific validation
        if (($data['application_type'] ?? '') === 'organization') {
            if (empty($data['org_name'])) {
                $errors['org_name'] = __('validation.required', $this->locale, ['field' => __('field.org_name', $this->locale)]);
            }
        }
        
        return $errors;
    }
}
