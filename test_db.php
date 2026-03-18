<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('RUN_MIGRATIONS', true);
try {
    require_once 'config/db_connect.php';
    echo "<h1>Database Connection: SUCCESS</h1>";
    echo "<p>Migrations triggered.</p>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
    echo "<p>Total cars in database: " . $stmt->fetchColumn() . "</p>";
} catch (Exception $e) {
    echo "<h1>Database Connection: FAILED</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
