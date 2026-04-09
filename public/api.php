<?php
/**
 * API Endpoint
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = preg_replace('/^api\//', '', $path);

// Handle preflight
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    switch ($path) {
        case 'statistics':
            $database = new App\Services\Database($db);
            
            $stats = [
                'total_applications' => $database->count('applications'),
                'total_individuals' => $database->count('applications', "application_type = 'individual'"),
                'total_organizations' => $database->count('applications', "application_type = 'organization'"),
                'countries' => $database->select('SELECT country, COUNT(*) as count FROM applications GROUP BY country ORDER BY count DESC LIMIT 10'),
            ];
            
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'register':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate
            if (empty($data['full_name']) || empty($data['email']) || empty($data['country'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                break;
            }
            
            $database = new App\Services\Database($db);
            
            $id = $database->insert('applications', [
                'application_type' => $data['application_type'] ?? 'individual',
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'country' => $data['country'],
                'city' => $data['city'] ?? null,
                'region' => $data['region'] ?? null,
                'household_size' => $data['household_size'] ?? 1,
                'gdpr_consent' => !empty($data['gdpr_consent']) ? 1 : 0,
                'ip_address' => client_ip(),
                'locale' => $data['locale'] ?? 'en',
            ]);
            
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
}
