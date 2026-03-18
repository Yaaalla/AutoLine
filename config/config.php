<?php
declare(strict_types=1);

// Central configuration for DB + sessions (cPanel-safe)

// 1) Session storage inside the project (avoids server path/permission issues)
$sessionDir = realpath(__DIR__ . '/../var/sessions') ?: (__DIR__ . '/../var/sessions');
if (!is_dir($sessionDir)) {
    @mkdir($sessionDir, 0755, true);
}
if (is_dir($sessionDir)) {
    // Must be set before session_start()
    session_save_path($sessionDir);
}

// Secure cookie defaults (works on local + live)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Validate that PHP can actually write the session file.
 * Returns [bool $ok, string $reason]
 */
function ensure_session_writable(): array
{
    $savePath = session_save_path();
    if (!$savePath) {
        return [false, "session_save_path غير مضبوط."];
    }
    if (!is_dir($savePath)) {
        return [false, "مجلد السيشن غير موجود: {$savePath}"];
    }
    if (!is_writable($savePath)) {
        return [false, "مجلد السيشن غير قابل للكتابة: {$savePath}"];
    }

    // Force creating a session file and verify it appears on disk.
    $sid = session_id();
    if (!$sid) {
        return [false, "تعذر الحصول على session_id."];
    }

    $_SESSION['__write_test'] = time();
    session_write_close();
    // reopen session for the rest of the request
    session_start();

    $sessionFile = rtrim($savePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sess_' . $sid;
    if (!file_exists($sessionFile)) {
        return [false, "PHP لم يتمكن من إنشاء ملف السيشن داخل: {$savePath}"];
    }

    return [true, ""];
}

// 2) Database connection (PDO + prepared statements)
const DB_HOST = 'localhost';
const DB_NAME = 'yaaalla_autolux_db';
const DB_USER = 'yaaalla_autolux_db';
const DB_PASS = 'T_BiAd+y1S)3.pRp';
const DB_CHARSET = 'utf8mb4';

try {
    // Auto-detect local socket (LAMPP/XAMPP) when available; otherwise use host (cPanel/live).
    $socketCandidates = [
        '/opt/lampp/var/mysql/mysql.sock',
        '/var/run/mysqld/mysqld.sock',
        '/tmp/mysql.sock',
    ];
    $socket = null;
    foreach ($socketCandidates as $candidate) {
        if (is_string($candidate) && @file_exists($candidate)) {
            $socket = $candidate;
            break;
        }
    }

    $dsn = $socket
        ? 'mysql:unix_socket=' . $socket . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET
        : 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Throwable $e) {
    // Do not leak details in production
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

