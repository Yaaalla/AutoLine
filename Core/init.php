<?php
/**
 * AutoLine - Application Bootstrap
 * 
 * This file should be included at the beginning of every PHP page
 * It initializes the application environment, loads configurations,
 * starts the session, and sets up the database connection.
 * 
 * Usage at the top of every PHP file:
 * require_once __DIR__ . '/../Core/init.php';
 */

// Prevent direct access
if (!defined('AUTOLINE_ROOT')) {
    define('AUTOLINE_ROOT', dirname(__DIR__));
}

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'AutoLine\\';
    $base_dir = AUTOLINE_ROOT . '/Core/';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace prefix with base directory, replace namespace
    // separators with directory separators in the relative class name
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
require_once AUTOLINE_ROOT . '/Core/Config.php';

use AutoLine\Core\Config;

// Load application configuration
$appConfig = Config::getAppConfig();

// Set error reporting based on environment
if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', AUTOLINE_ROOT . '/Storage/Logs/error.log');
}

// Initialize session with proper configuration
if (session_status() === PHP_SESSION_NONE) {
    $sessionConfig = Config::getSessionConfig();
    session_set_cookie_params($sessionConfig);
    session_start();
}

// Set locale
setlocale(LC_ALL, $appConfig['locale'] . '_' . strtoupper($appConfig['locale']) . '.UTF-8');

// Load utility functions
require_once AUTOLINE_ROOT . '/Core/Utils/Functions.php';

// Load database if needed
// $pdo = AutoLine\Core\Database::getConnection();

/**
 * Helper function to get base URL
 */
function base_url(string $path = ''): string
{
    $urls = AutoLine\Core\Config::getUrls();
    return $urls['base'] . '/' . ltrim($path, '/');
}

/**
 * Helper function to get asset URL
 */
function asset_url(string $path): string
{
    $urls = AutoLine\Core\Config::getUrls();
    return $urls['assets'] . '/' . ltrim($path, '/');
}

/**
 * Helper function to redirect
 */
function redirect(string $url, int $status = 302): void
{
    header("Location: $url", true, $status);
    exit;
}

/**
 * Helper function to sanitize input
 */
function clean(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
