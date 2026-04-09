<?php
declare(strict_types=1);

namespace CountUsKurds\Support;

use mysqli;
use SessionHandlerInterface;
use Throwable;

final class DatabaseSessionHandler implements SessionHandlerInterface
{
    private ?mysqli $conn = null;
    private bool $tableChecked = false;
    private int $ttl;

    public function __construct(int $ttl)
    {
        $this->ttl = max(300, $ttl);
    }

    public function isAvailable(): bool
    {
        return $this->connect() && $this->ensureTable();
    }

    public function open(string $savePath, string $sessionName): bool
    {
        return $this->connect();
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        if (!$this->connect() || !$this->ensureTable()) {
            return '';
        }

        try {
            $stmt = $this->conn->prepare(
                'SELECT session_data FROM app_sessions WHERE session_id = ? AND expires_at > NOW() LIMIT 1'
            );
            if (!$stmt) {
                return '';
            }

            $stmt->bind_param('s', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            return isset($row['session_data']) ? (string) $row['session_data'] : '';
        } catch (Throwable) {
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        if (!$this->connect() || !$this->ensureTable()) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO app_sessions (session_id, session_data, expires_at, updated_at)
                 VALUES (?, ?, (NOW() + INTERVAL ? SECOND), NOW())
                 ON DUPLICATE KEY UPDATE
                    session_data = VALUES(session_data),
                    expires_at = VALUES(expires_at),
                    updated_at = VALUES(updated_at)'
            );
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param('ssi', $id, $data, $this->ttl);
            $ok = $stmt->execute();
            $stmt->close();

            return $ok;
        } catch (Throwable) {
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        if (!$this->connect() || !$this->ensureTable()) {
            return true;
        }

        try {
            $stmt = $this->conn->prepare('DELETE FROM app_sessions WHERE session_id = ?');
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param('s', $id);
            $ok = $stmt->execute();
            $stmt->close();

            return $ok;
        } catch (Throwable) {
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        if (!$this->connect() || !$this->ensureTable()) {
            return 0;
        }

        try {
            $stmt = $this->conn->prepare('DELETE FROM app_sessions WHERE expires_at <= NOW()');
            if (!$stmt) {
                return 0;
            }
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            return max(0, $affected);
        } catch (Throwable) {
            return 0;
        }
    }

    private function connect(): bool
    {
        if ($this->conn instanceof mysqli) {
            return true;
        }

        $host = (string) env('DB_HOST', '');
        $port = (int) env('DB_PORT', 3306);
        $database = (string) env('DB_DATABASE', '');
        $username = (string) env('DB_USERNAME', '');
        $password = (string) env('DB_PASSWORD', '');

        if ($host === '' || $database === '' || $username === '') {
            return false;
        }

        try {
            $conn = @new mysqli($host, $username, $password, $database, $port);
            if ($conn->connect_errno !== 0) {
                return false;
            }

            $conn->set_charset('utf8mb4');
            $this->conn = $conn;
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function ensureTable(): bool
    {
        if ($this->tableChecked) {
            return true;
        }

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `app_sessions` (
  `session_id` VARCHAR(128) NOT NULL,
  `session_data` LONGTEXT NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        try {
            $ok = $this->conn->query($sql);
            $this->tableChecked = ($ok === true);
            return $this->tableChecked;
        } catch (Throwable) {
            return false;
        }
    }
}
