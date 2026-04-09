<?php
/**
 * Admin Application Controller
 */

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Services\Auth;

class ApplicationController
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
        
        // Filters
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        
        // Build query
        $where = '1=1';
        $params = [];
        
        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }
        
        if ($type) {
            $where .= ' AND application_type = ?';
            $params[] = $type;
        }
        
        if ($search) {
            $where .= ' AND (full_name LIKE ? OR email LIKE ? OR country LIKE ?)';
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Count total
        $total = $this->db->count('applications', $where, $params);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Get applications
        $applications = $this->db->select("
            SELECT * FROM applications 
            WHERE {$where} 
            ORDER BY created_at DESC 
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);
        
        $pageTitle = 'Applications';
        
        include TEMPLATE_PATH . '/admin/applications.php';
    }
    
    public function view(int $id): void
    {
        $user = $this->auth->user();
        
        $application = $this->db->selectOne('SELECT * FROM applications WHERE id = ?', [$id]);
        
        if (!$application) {
            flash('error', 'Application not found.');
            redirect('/admin?action=applications');
            return;
        }
        
        $pageTitle = 'View Application';
        
        include TEMPLATE_PATH . '/admin/application-view.php';
    }
    
    public function updateStatus(): void
    {
        if (!csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid session.');
            redirect('/admin?action=applications');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes = trim($_POST['admin_notes'] ?? '');
        
        $validStatuses = ['pending', 'reviewed', 'approved', 'rejected', 'contacted'];
        if (!in_array($status, $validStatuses)) {
            flash('error', 'Invalid status.');
            redirect('/admin?action=application-view&id=' . $id);
            return;
        }
        
        $this->db->update('applications', [
            'status' => $status,
            'admin_notes' => $notes,
            'reviewed_by' => $this->auth->id(),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
        
        // Log activity
        $this->logActivity('application_status_updated', 'applications', $id);
        
        flash('success', 'Application status updated.');
        redirect('/admin?action=application-view&id=' . $id);
    }
    
    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_token'] ?? '')) {
            flash('error', 'Invalid request.');
            redirect('/admin?action=applications');
            return;
        }
        
        $this->db->delete('applications', 'id = ?', [$id]);
        
        $this->logActivity('application_deleted', 'applications', $id);
        
        flash('success', 'Application deleted.');
        redirect('/admin?action=applications');
    }
    
    public function export(): void
    {
        $applications = $this->db->select('SELECT * FROM applications ORDER BY created_at DESC');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="applications_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, [
            'ID', 'Type', 'Status', 'Name', 'Email', 'Phone', 'Country', 'City', 
            'Region', 'Org Name', 'Household Size', 'Created At'
        ]);
        
        foreach ($applications as $app) {
            fputcsv($output, [
                $app['id'],
                $app['application_type'],
                $app['status'],
                $app['full_name'],
                $app['email'],
                $app['phone'],
                $app['country'],
                $app['city'],
                $app['region'],
                $app['org_name'],
                $app['household_size'],
                $app['created_at'],
            ]);
        }
        
        fclose($output);
        
        $this->logActivity('applications_exported', null, null);
        exit;
    }
    
    private function logActivity(string $action, ?string $entityType, ?int $entityId): void
    {
        try {
            $this->db->insert('activity_log', [
                'user_id' => $this->auth->id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'ip_address' => client_ip(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}
