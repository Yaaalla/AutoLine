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
     // Self-healing: Check if 'color' column exists
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'color'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE cars ADD COLUMN color VARCHAR(50) DEFAULT NULL AFTER fuel_type");
         }
     } catch (Exception $e) {}


     // Self-healing: Check if 'mileage' column exists
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'mileage'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE cars ADD COLUMN mileage INT DEFAULT NULL AFTER color");
         }
     } catch (Exception $e) {}

     // Self-healing: Check if 'car_condition' column exists
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'car_condition'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE cars ADD COLUMN car_condition INT DEFAULT NULL AFTER mileage");
         }
     } catch (Exception $e) {}

     // Self-healing: Check if 'tire_condition' column exists
     try {
         $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'tire_condition'");
         if (!$stmt->fetch()) {
             $pdo->exec("ALTER TABLE cars ADD COLUMN tire_condition VARCHAR(100) DEFAULT NULL AFTER car_condition");
         }
     } catch (Exception $e) {}
     
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
