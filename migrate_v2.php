<?php
require_once 'config/db_connect.php';

try {
    $pdo->exec("ALTER TABLE cars ADD COLUMN model_year INT AFTER model;");
    echo "Successfully added model_year column.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column model_year already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
