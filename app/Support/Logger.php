<?php
declare(strict_types=1);

namespace CountUsKurds\Support;

use mysqli;
use Throwable;

final class Logger
{
    private static bool $isWriting = false;
    private static bool $tableChecked = false;
    private static ?mysqli $mysql = null;

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function audit(string $message, array $context = []): void
    {
        self::write('AUDIT', $message, $context, 'audit.log');
    }

    private static function write(string $level, string $message, array $context = [], string $fileName = 'app.log'): void
    {
        if (self::$isWriting) {
            self::writeToPhpLog($level, $message, $context, $fileName);
            return;
        }

        self::$isWriting = true;

        try {
            if (self::writeToMysql($level, $message, $context, $fileName)) {
                return;
            }

            $fallback = strtolower((string) env('LOG_FALLBACK', 'php_error'));
            if ($fallback === 'php_error') {
                self::writeToPhpLog($level, $message, $context, $fileName);
                return;
            }

            self::writeToFile($level, $message, $context, $fileName);
        } catch (Throwable $e) {
            self::writeToPhpLog('ERROR', 'Logger failure', [
                'logger_error' => $e->getMessage(),
                'original_level' => $level,
                'original_message' => $message,
            ], 'logger-fallback');
        } finally {
            self::$isWriting = false;
        }
    }

    private static function writeToMysql(string $level, string $message, array $context, string $channel): bool
    {
        $logChannel = strtolower((string) env('LOG_CHANNEL', 'mysql'));
        if ($logChannel !== 'mysql') {
            return false;
        }

        $conn = self::mysqlConnection();
        if (!$conn instanceof mysqli) {
            return false;
        }

        if (!self::ensureLogsTable($conn)) {
            return false;
        }

        $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($contextJson)) {
            $contextJson = '{}';
        }

        $stmt = $conn->prepare(
            'INSERT INTO app_logs (`level`, `message`, `context`, `channel`, `created_at`)
             VALUES (?, ?, ?, ?, NOW())'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ssss', $level, $message, $contextJson, $channel);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    private static function mysqlConnection(): ?mysqli
    {
        if (self::$mysql instanceof mysqli) {
            return self::$mysql;
        }

        $host = (string) env('DB_HOST', '');
        $port = (int) env('DB_PORT', 3306);
        $database = (string) env('DB_DATABASE', '');
        $username = (string) env('DB_USERNAME', '');
        $password = (string) env('DB_PASSWORD', '');

        if ($host === '' || $database === '' || $username === '') {
            return null;
        }

        try {
            $conn = @new mysqli($host, $username, $password, $database, $port);
            if ($conn->connect_errno !== 0) {
                return null;
            }

            $conn->set_charset('utf8mb4');
            self::$mysql = $conn;
            return self::$mysql;
        } catch (Throwable) {
            return null;
        }
    }

    private static function ensureLogsTable(mysqli $conn): bool
    {
        if (self::$tableChecked) {
            return true;
        }

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `app_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` VARCHAR(20) NOT NULL,
  `message` VARCHAR(500) NOT NULL,
  `context` LONGTEXT NULL,
  `channel` VARCHAR(100) NOT NULL DEFAULT 'app.log',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        try {
            $ok = $conn->query($sql);
            self::$tableChecked = ($ok === true);
            return self::$tableChecked;
        } catch (Throwable) {
            return false;
        }
    }

    private static function writeToFile(string $level, string $message, array $context, string $fileName): void
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir) && !@mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            self::writeToPhpLog($level, $message, $context, $fileName);
            return;
        }

        $logFile = $logDir . DIRECTORY_SEPARATOR . $fileName;
        $line = self::formatLine($level, $message, $context);

        if (@file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX) === false) {
            self::writeToPhpLog($level, $message, $context, $fileName);
        }
    }

    private static function writeToPhpLog(string $level, string $message, array $context, string $channel): void
    {
        $line = trim(self::formatLine($level, $message, array_merge($context, ['channel' => $channel])));
        @error_log($line);
    }

    private static function formatLine(string $level, string $message, array $context): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $payload = $context !== [] ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $line = sprintf("[%s] %s: %s", $timestamp, $level, $message);
        if ($payload !== '' && $payload !== '[]') {
            $line .= ' ' . $payload;
        }
        return $line . PHP_EOL;
    }
}
