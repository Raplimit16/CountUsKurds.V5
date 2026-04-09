<?php
/**
 * Database Service
 * Simple database wrapper with common operations
 */

namespace App\Services;

class Database
{
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
    
    /**
     * Run a select query
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get single row
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Insert record
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * Update records
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge(array_values($data), $whereParams));
        
        return $stmt->rowCount();
    }
    
    /**
     * Delete records
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Count records
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
        $result = $this->selectOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
