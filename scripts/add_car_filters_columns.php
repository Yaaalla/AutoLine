<?php
require_once '../config/db_connect.php';

try {
    // Add color column
    $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'color'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE cars ADD COLUMN color VARCHAR(50) DEFAULT NULL AFTER fuel_type");
        echo "Column 'color' added successfully.<br>";
    } else {
        echo "Column 'color' already exists.<br>";
    }

    // Add year column
    $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE 'year'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE cars ADD COLUMN year INT DEFAULT NULL AFTER model");
        echo "Column 'year' added successfully.<br>";
    } else {
        echo "Column 'year' already exists.<br>";
    }

    echo "Migration completed.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
