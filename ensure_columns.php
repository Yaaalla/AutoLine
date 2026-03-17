<?php
require_once 'config/db_connect.php';

$columns = [
    'color' => "ALTER TABLE cars ADD COLUMN color VARCHAR(50) AFTER fuel_type;",
    'mileage' => "ALTER TABLE cars ADD COLUMN mileage INT AFTER color;",
    'car_condition' => "ALTER TABLE cars ADD COLUMN car_condition INT AFTER mileage;",
    'tire_condition' => "ALTER TABLE cars ADD COLUMN tire_condition VARCHAR(50) AFTER car_condition;",
    'discount' => "ALTER TABLE cars ADD COLUMN discount INT DEFAULT 0 AFTER status;"
];

echo "<pre>";
foreach ($columns as $column => $sql) {
    try {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM cars LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec($sql);
            echo "Success: Column '$column' added.\n";
        } else {
            echo "Info: Column '$column' already exists.\n";
        }
    } catch (Exception $e) {
        echo "Error adding '$column': " . $e->getMessage() . "\n";
    }
}
echo "Migration complete.";
echo "</pre>";
?>
