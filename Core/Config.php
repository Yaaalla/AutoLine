<?php
/**
 * AutoLine - Central Configuration File
 * 
 * This file contains all application configurations including:
 * - Database settings (for XAMPP Localhost)
 * - Application paths
 * - Session settings
 * - Email configuration
 * - WhatsApp configuration
 * 
 * Usage:
 * require_once __DIR__ . '/../Core/Config.php';
 * $db = Config::getDatabaseConfig();
 */

namespace AutoLine\Core;

class Config
{
    /**
     * Database configuration for XAMPP
     * Localhost development environment
     */
    public static function getDatabaseConfig(): array
    {
        return [
            'host'      => 'localhost',
            'database'  => 'autoline_db',      // قاعدة البيانات المحلية
            'username'  => 'root',             // المستخدم الافتراضي في XAMPP
            'password'  => '',                 // كلمة المرور الافتراضية (فارغة في XAMPP)
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'port'      => 3306,               // المنفذ الافتراضي لـ MySQL
            'options'   => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::ATTR_PERSISTENT         => true,
            ]
        ];
    }

    /**
     * Production database configuration
     * Uncomment and configure when deploying to production
     */
    public static function getProductionDatabaseConfig(): array
    {
        return [
            'host'      => 'localhost',
            'database'  => 'yaaalla_autolux_db',
            'username'  => 'yaaalla_autolux_db',
            'password'  => 'HJYhmm7I[UapCTNq',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'port'      => 3306,
            'options'   => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::ATTR_PERSISTENT         => true,
            ]
        ];
    }

    /**
     * Application paths
     * Linux-compatible absolute paths
     */
    public static function getPaths(): array
    {
        return [
            'base'        => dirname(__DIR__),          // /home/abdelrhman/Desktop/autoline2/autoLine
            'core'        => dirname(__DIR__) . '/Core',
            'includes'    => dirname(__DIR__) . '/Includes',
            'modules'     => dirname(__DIR__) . '/Modules',
            'assets'      => dirname(__DIR__) . '/Assets',
            'admin'       => dirname(__DIR__) . '/admin',
            'storage'     => dirname(__DIR__) . '/Storage',
            'uploads'     => dirname(__DIR__) . '/Storage/Uploads',
            'logs'        => dirname(__DIR__) . '/Storage/Logs',
            'cache'       => dirname(__DIR__) . '/Storage/Cache',
            'database'    => dirname(__DIR__) . '/Database',
            'docs'        => dirname(__DIR__) . '/docs',
        ];
    }

    /**
     * URL paths for web access
     */
    public static function getUrls(): array
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = "/autoline";  // المسار الأساسي للمشروع
        
        return [
            'base'        => $base,
            'assets'      => $base . '/Assets',
            'css'         => $base . '/Assets/css',
            'js'          => $base . '/Assets/js',
            'images'      => $base . '/Assets/images',
            'uploads'     => $base . '/Storage/Uploads',
            'modules'     => $base . '/Modules',
            'admin'       => $base . '/admin',
        ];
    }

    /**
     * Session configuration
     */
    public static function getSessionConfig(): array
    {
        return [
            'lifetime' => 0,        // حتى إغلاق المتصفح
            'path'     => '/autoline/',
            'domain'   => '',       // نطاق فارغ للسماح لجميع النطاقات الفرعية
            'secure'   => false,    // true في حالة HTTPS
            'httponly' => true,     // لمنع الوصول من JavaScript
            'samesite' => 'Lax',
        ];
    }

    /**
     * Application settings
     */
    public static function getAppConfig(): array
    {
        return [
            'name'          => 'AutoLine',
            'name_ar'       => 'اوتو لاين',
            'version'       => '1.0.0',
            'environment'   => 'development', // development | production
            'timezone'      => 'Africa/Cairo',
            'locale'        => 'ar',
            'debug'         => true,          // false في الإنتاج
            'maintenance'   => false,
        ];
    }

    /**
     * Email configuration
     */
    public static function getEmailConfig(): array
    {
        return [
            'smtp_host'     => 'smtp.gmail.com',
            'smtp_port'     => 587,
            'smtp_user'     => '',
            'smtp_pass'     => '',
            'from_email'    => 'noreply@autoline.com',
            'from_name'     => 'AutoLine - اوتو لاين',
            'encryption'    => 'tls',
        ];
    }

    /**
     * WhatsApp configuration
     */
    public static function getWhatsAppConfig(): array
    {
        return [
            'enabled'       => true,
            'phone'         => '201003412321',
            'api_key'       => '',
            'instance_id'   => '',
        ];
    }

    /**
     * Get a specific configuration value
     * 
     * @param string $key Config key (e.g., 'app.name', 'database.host')
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $method = 'get' . ucfirst($keys[0]) . 'Config';
        
        if (!method_exists(self::class, $method)) {
            return $default;
        }
        
        $config = self::$method();
        
        // Remove first key and traverse nested array
        array_shift($keys);
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                return $default;
            }
            $config = $config[$k];
        }
        
        return $config;
    }
}

// Set timezone
date_default_timezone_set(Config::getAppConfig()['timezone']);
