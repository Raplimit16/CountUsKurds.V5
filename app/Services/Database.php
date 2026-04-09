<?php
declare(strict_types=1);

namespace CountUsKurds\Services;

use CountUsKurds\Support\Logger;
use mysqli;
use RuntimeException;

final class Database
{
    private static ?mysqli $connection = null;

    public static function connection(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $config = self::resolveConfig();

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
                $config['port']
            );
        } catch (\mysqli_sql_exception $e) {
            Logger::error('Database connection failed', [
                'error' => $e->getMessage(),
                'host' => $config['host'],
                'database' => $config['database'],
            ]);
            throw new RuntimeException('Database connection failed.');
        }

        try {
            $charset = $config['charset'] ?? 'utf8mb4';
            $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';
            $conn->set_charset($charset);
            $conn->query("SET collation_connection = '{$collation}'");
        } catch (\mysqli_sql_exception $e) {
            Logger::error('Failed to set database charset', ['error' => $e->getMessage()]);
        }

        self::$connection = $conn;

        return self::$connection;
    }

    private static function resolveConfig(): array
    {
        $config = require config_path('database.php');

        $localPath = config_path('database.local.php');
        if (is_file($localPath)) {
            $local = require $localPath;
            if (is_array($local)) {
                $config = array_merge($config, array_filter($local, static fn($value) => $value !== null));
            }
        }

        $dsn = env('DATABASE_URL');
        if (is_string($dsn) && $dsn !== '') {
            $config = array_merge($config, self::parseDsn($dsn));
        }

        return [
            'host' => $config['host'] ?? '127.0.0.1',
            'username' => $config['username'] ?? 'root',
            'password' => $config['password'] ?? '',
            'database' => $config['database'] ?? '',
            'port' => (int) ($config['port'] ?? 3306),
            'charset' => $config['charset'] ?? 'utf8mb4',
            'collation' => $config['collation'] ?? 'utf8mb4_unicode_ci',
        ];
    }

    private static function parseDsn(string $dsn): array
    {
        $parts = parse_url($dsn);
        if ($parts === false) {
            return [];
        }

        return array_filter([
            'host' => $parts['host'] ?? null,
            'port' => isset($parts['port']) ? (int) $parts['port'] : null,
            'username' => $parts['user'] ?? null,
            'password' => $parts['pass'] ?? null,
            'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : null,
        ], static fn($value) => $value !== null && $value !== '');
    }
}
