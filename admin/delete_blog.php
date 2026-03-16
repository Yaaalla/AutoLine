<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "معرف المقال غير صالح.";
    header("Location: manage_blogs.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    // First retrieve image path to delete it
    $stmt = $pdo->prepare("SELECT image_path FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    
    if ($blog) {
        $image_path = $blog['image_path'];
        
        // Delete from DB
        $delete_stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $delete_stmt->execute([$id]);
        
        // Only attempt to delete file if DB deletion was successful and it's a local file
        if (!empty($image_path) && strpos($image_path, 'http') !== 0) {
            $file_abs_path = '../' . $image_path;
            if (file_exists($file_abs_path) && is_file($file_abs_path)) {
                @unlink($file_abs_path);
            }
        }
        
        $_SESSION['success'] = "تم حذف المقال بنجاح.";
    } else {
        $_SESSION['error'] = "لم يتم العثور على المقال.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "حدث خطأ أثناء محاولة الحذف: " . $e->getMessage();
}

header("Location: manage_blogs.php");
exit;
?>
