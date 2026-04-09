<?php
declare(strict_types=1);

namespace CountUsKurds\Http\Controllers;

use CountUsKurds\Services\Database;
use CountUsKurds\Support\Logger;
use CountUsKurds\Support\Translator;
use mysqli;
use Throwable;

class ApiController
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method !== 'POST') {
            $this->respond(false, ['error' => 'Unsupported request method.'], 405);
        }

        $payload = json_decode(file_get_contents('php://input') ?: '[]', true);
        if (!is_array($payload)) {
            $this->respond(false, ['error' => 'Invalid payload.'], 400);
        }

        $action = $payload['action'] ?? null;
        if (!is_string($action)) {
            $this->respond(false, ['error' => 'Missing action.'], 400);
        }

        try {
            $conn = Database::connection();
            switch ($action) {
                case 'get_count':
                    $total = $this->fetchTotalCount($conn);
                    $this->respond(true, ['total_count' => $total]);
                    break;
                case 'register':
                    $countryCity = trim((string) ($payload['country_city'] ?? ''));
                    $count = filter_var($payload['kurdish_count'] ?? null, FILTER_VALIDATE_INT);

                    if ($countryCity === '' || $count === false || $count < 1) {
                        $this->respond(false, ['error' => 'Invalid registration data.'], 400);
                    }

                    $this->storeRegistration($conn, $countryCity, (int) $count);
                    $total = $this->fetchTotalCount($conn);
                    $this->respond(true, [
                        'message' => 'Registration stored.',
                        'total_count' => $total,
                    ]);
                    break;
                default:
                    $this->respond(false, ['error' => 'Unknown action.'], 400);
            }
        } catch (Throwable $e) {
            Logger::error('API request failed', ['exception' => $e->getMessage()]);
            $this->respond(false, ['error' => 'Internal server error.'], 500);
        }
    }

    private function fetchTotalCount(mysqli $conn): int
    {
        $stmt = $conn->prepare('SELECT SUM(kurdish_count) AS total FROM registrations');
        if (!$stmt) {
            Logger::error('Failed to prepare get_count statement', ['error' => $conn->error]);
            return 0;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result?->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    private function storeRegistration(mysqli $conn, string $countryCity, int $count): void
    {
        $stmt = $conn->prepare('INSERT INTO registrations (country_city, kurdish_count, timestamp) VALUES (?, ?, NOW())');
        if (!$stmt) {
            Logger::error('Failed to prepare registration statement', ['error' => $conn->error]);
            throw new \RuntimeException('Database error');
        }

        $stmt->bind_param('si', $countryCity, $count);
        if (!$stmt->execute()) {
            Logger::error('Failed to insert registration', ['error' => $stmt->error]);
            throw new \RuntimeException('Database error');
        }
        $stmt->close();
    }

    private function respond(bool $success, array $data = [], int $status = 200): void
    {
        http_response_code($status);

        $response = array_merge(['success' => $success], $data);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
