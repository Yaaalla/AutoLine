<?php
// config/db_connect.php

$host = 'localhost';
$db   = 'autolux_db';
$user = 'root'; // Change if necessary
$pass = '';     // Change if necessary
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Self-healing: Check if 'discount' column exists in 'cars' table
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'discount'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE cars ADD COLUMN discount INT DEFAULT 0");
         }
     } catch (Exception $e) {
         // Silently fail if table doesn't exist yet or other issues
     }
     
     // Self-healing: Create 'blogs' table if it doesn't exist
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
     } catch (Exception $e) {
         // Silently fail if permissions are missing etc
     }
     
     // Self-healing: Add 'role' column to 'admins' table
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE admins ADD COLUMN role ENUM('admin','manager') DEFAULT 'admin'");
         }
     } catch (Exception $e) {
         // Silently fail
     }
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
