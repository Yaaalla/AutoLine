<?php
/**
 * AutoLine - Database Connection Class
 * 
 * Provides a single point of database access using PDO
 * Implements Singleton pattern for connection reuse
 * 
 * Usage:
 * use AutoLine\Core\Database;
 * $pdo = Database::getInstance()->getConnection();
 */

namespace AutoLine\Core;

require_once __DIR__ . '/Config.php';

use PDO;
use PDOException;

class Database
{
    /**
     * @var PDO|null Connection instance
     */
    private static ?PDO $instance = null;
    
    /**
     * @var array Database configuration
     */
    private static array $config;
    
    /**
     * Get database connection (Singleton pattern)
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::initializeConnection();
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize database connection
     * 
     * @throws PDOException
     */
    private static function initializeConnection(): void
    {
        // Determine environment
        $appConfig = Config::getAppConfig();
        $isProduction = ($appConfig['environment'] === 'production');
        
        // Get appropriate config
        self::$config = $isProduction 
            ? Config::getProductionDatabaseConfig() 
            : Config::getDatabaseConfig();
        
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['database'],
            self::$config['charset']
        );
        
        try {
            self::$instance = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password'],
                self::$config['options']
            );
            
            // Set additional attributes
            self::$instance->exec("SET NAMES '" . self::$config['charset'] . "'");
            self::$instance->exec("SET CHARACTER SET " . self::$config['charset']);
            self::$instance->exec("SET COLLATION_CONNECTION = '" . self::$config['collation'] . "'");
            
        } catch (PDOException $e) {
            error_log("[AutoLine Database] Connection failed: " . $e->getMessage());
            throw new PDOException(
                "فشل الاتصال بقاعدة البيانات. يرجى التحقق من إعدادات XAMPP.", 
                (int)$e->getCode()
            );
        }
    }
    
    /**
     * Close database connection
     */
    public static function closeConnection(): void
    {
        self::$instance = null;
    }
    
    /**
     * Check if connection is active
     * 
     * @return bool
     */
    public static function isConnected(): bool
    {
        if (self::$instance === null) {
            return false;
        }
        
        try {
            self::$instance->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Execute a query with prepared statements
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return \PDOStatement|false
     */
    public static function query(string $sql, array $params = [])
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Fetch all results from a query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    /**
     * Fetch single row from a query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array|null
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    /**
     * Get last insert ID
     * 
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction(): void
    {
        self::getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public static function commit(): void
    {
        self::getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollback(): void
    {
        self::getConnection()->rollBack();
    }
    
    /**
     * Get configuration (for debugging)
     * 
     * @return array (sensitive data removed)
     */
    public static function getConfig(): array
    {
        return [
            'host'     => self::$config['host'],
            'database' => self::$config['database'],
            'charset'  => self::$config['charset'],
            'port'     => self::$config['port'],
        ];
    }
}

// Legacy support - non-namespace version
if (!class_exists('Database')) {
    class_alias('AutoLine\Core\Database', 'Database');
}
