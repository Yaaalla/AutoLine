<?php
// config/db_connect.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$db   = 'autolux_db';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

// Use a static variable to hold the connection for reuse within the same request
static $pdo = null;

if ($pdo === null) {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => true, // Enable persistent connections as requested
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // SELF-HEALING: Only run if explicitly requested or in a specific environment
        // To run migrations, you can define 'RUN_MIGRATIONS' or access a specific script.
        if (defined('RUN_MIGRATIONS') && RUN_MIGRATIONS === true) {
            // Color column
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'color'");
                if (!$stmt->fetch()) {
                    $pdo->exec("ALTER TABLE cars ADD COLUMN color VARCHAR(50) DEFAULT NULL AFTER fuel_type");
                }
            } catch (Exception $e) {}

            // Mileage column
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'mileage'");
                if (!$stmt->fetch()) {
                    $pdo->exec("ALTER TABLE cars ADD COLUMN mileage INT DEFAULT NULL AFTER color");
                }
            } catch (Exception $e) {}

            // Car Condition column
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'car_condition'");
                if (!$stmt->fetch()) {
                    $pdo->exec("ALTER TABLE cars ADD COLUMN car_condition INT DEFAULT NULL AFTER mileage");
                }
            } catch (Exception $e) {}

            // Tire Condition column
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'tire_condition'");
                if (!$stmt->fetch()) {
                    $pdo->exec("ALTER TABLE cars ADD COLUMN tire_condition VARCHAR(100) DEFAULT NULL AFTER car_condition");
                }
            } catch (Exception $e) {}
            
            // Blogs table
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS blogs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    excerpt TEXT NOT NULL,
                    content LONGTEXT NOT NULL,
                    image_path TEXT NOT NULL,
                    author VARCHAR(100) DEFAULT 'admin',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
            } catch (Exception $e) {}
            
            // Role column for admins
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'");
                if (!$stmt->fetch()) {
                    $pdo->exec("ALTER TABLE admins ADD COLUMN role ENUM('admin','manager') DEFAULT 'admin'");
                }
            } catch (Exception $e) {}
        }
    } catch (\PDOException $e) {
        // Log error in production instead of direct echo
        error_log("Database Connection Error: " . $e->getMessage());
        die("Could not connect to the database.");
    }
}
?>
