<?php
require_once '../config/db_connect.php';
try {
    $pdo->exec("ALTER TABLE cars ADD COLUMN discount INT DEFAULT 0;");
    echo "Success: discount column added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
