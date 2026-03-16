<?php
require_once '../config/db_connect.php';

try {
    // Create blogs table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        excerpt TEXT NOT NULL,
        content LONGTEXT NOT NULL,
        image_path TEXT NOT NULL,
        author VARCHAR(100) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Blogs table created successfully.<br>";
    
    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../uploads/blogs';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        echo "Upload directory created successfully.<br>";
    }
    
    echo "Migration completed.";
} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}
?>
