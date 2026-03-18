<?php
/**
 * AutoLine - Utility Functions
 * 
 * Common helper functions used throughout the application
 */

namespace AutoLine\Core\Utils;

/**
 * Generate WhatsApp message with booking details
 * 
 * @param array $booking_data Booking information
 * @param array $car_data Car information
 * @return string URL-encoded message
 */
function generateWhatsAppMessage(array $booking_data, array $car_data): string
{
    $message = "*طلب حجز سيارة جديد* 🚗\n\n";
    $message .= "*بيانات العميل:*\n";
    $message .= "👤 الاسم: " . $booking_data['name'] . "\n";
    $message .= "📧 البريد: " . $booking_data['email'] . "\n";
    $message .= "📱 الهاتف: " . $booking_data['phone'] . "\n\n";
    $message .= "*تفاصيل الحجز:*\n";
    $message .= "🏎️ السيارة: " . $car_data['brand'] . " " . $car_data['model'] . "\n";
    $message .= "📅 الاستلام: " . date('d/m/Y', strtotime($booking_data['pickup_date'])) . "\n";
    $message .= "📅 العودة: " . date('d/m/Y', strtotime($booking_data['return_date'])) . "\n";
    $message .= "⏱️ عدد الأيام: " . $booking_data['days'] . " يوم\n";
    
    $daily_price = $car_data['price_per_day'];
    if (isset($car_data['discount']) && $car_data['discount'] > 0) {
        $daily_price = $car_data['price_per_day'] * (1 - ($car_data['discount'] / 100));
    }
    
    $message .= "💰 السعر اليومي: " . number_format($daily_price, 2) . " ج.م\n";
    $message .= "💵 الإجمالي: " . number_format($booking_data['total_price'], 2) . " ج.م\n";
    
    return urlencode($message);
}

/**
 * Format price in Egyptian Pound
 * 
 * @param float $price
 * @return string
 */
function formatPrice(float $price): string
{
    return number_format($price, 2) . ' ج.م';
}

/**
 * Format date to Arabic format
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Generate SEO-friendly URL slug
 * 
 * @param string $text
 * @return string
 */
function slugify(string $text): string
{
    // Replace non-alphanumeric characters with hyphens
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    
    // Trim
    $text = trim($text, '-');
    
    // Lowercase
    $text = strtolower($text);
    
    return $text ?: 'n-a';
}

/**
 * Validate email address
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate Egyptian phone number
 * 
 * @param string $phone
 * @return bool
 */
function isValidPhone(string $phone): bool
{
    // Remove any non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it starts with 01 and has 11 digits total
    return preg_match('/^01[0-9]{9}$/', $phone) === 1;
}

/**
 * Log admin activity
 * 
 * @param \PDO $pdo Database connection
 * @param int $admin_id Admin ID
 * @param string $action Action performed
 * @param string $details Additional details
 * @return void
 */
function logActivity(\PDO $pdo, int $admin_id, string $action, string $details = ''): void
{
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$admin_id, $action, $details]);
    } catch (\PDOException $e) {
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

/**
 * Upload file with validation
 * 
 * @param array $file $_FILES array element
 * @param string $destination Destination directory
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return string|false Filename on success, false on failure
 */
function uploadFile(
    array $file, 
    string $destination, 
    array $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    int $max_size = 5242880  // 5MB
): string|false {
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    
    // Create destination if not exists
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $filepath = rtrim($destination, '/') . '/' . $filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token
 * @return bool
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Display flash message
 * 
 * @param string $type success|error|warning|info
 * @param string $message
 * @return string HTML
 */
function flashMessage(string $type, string $message): string
{
    $colors = [
        'success' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        'error'   => 'bg-red-500/10 text-red-500 border-red-500/20',
        'warning' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        'info'    => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
    ];
    
    $icons = [
        'success' => 'check_circle',
        'error'   => 'error',
        'warning' => 'warning',
        'info'    => 'info',
    ];
    
    $class = $colors[$type] ?? $colors['info'];
    $icon = $icons[$type] ?? $icons['info'];
    
    return "<div class='{$class} px-4 py-3 rounded-xl border flex items-center gap-2 mb-4'>
        <span class='material-symbols-outlined'>{$icon}</span>
        <span>" . htmlspecialchars($message) . "</span>
    </div>";
}

/**
 * Truncate text to specified length
 * 
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get car status badge HTML
 * 
 * @param string $status
 * @return string
 */
function carStatusBadge(string $status): string
{
    $statuses = [
        'available'   => ['color' => 'emerald', 'label' => 'متاحة'],
        'rented'      => ['color' => 'amber', 'label' => 'مؤجرة'],
        'maintenance' => ['color' => 'red', 'label' => 'صيانة'],
        'reserved'    => ['color' => 'blue', 'label' => 'محجوزة'],
    ];
    
    $status = $statuses[$status] ?? $statuses['available'];
    
    return "<span class='px-3 py-1 rounded-full bg-{$status['color']}-500/10 text-{$status['color']}-500 text-xs font-bold border border-{$status['color']}-500/20'>
        {$status['label']}
    </span>";
}

/**
 * Get booking status badge HTML
 * 
 * @param string $status
 * @return string
 */
function bookingStatusBadge(string $status): string
{
    $statuses = [
        'pending'   => ['color' => 'amber', 'label' => 'قيد الانتظار'],
        'confirmed' => ['color' => 'blue', 'label' => 'مؤكد'],
        'completed' => ['color' => 'emerald', 'label' => 'مكتمل'],
        'cancelled' => ['color' => 'red', 'label' => 'ملغي'],
    ];
    
    $status = $statuses[$status] ?? ['color' => 'slate', 'label' => $status];
    
    return "<span class='px-3 py-1 rounded-full bg-{$status['color']}-500/10 text-{$status['color']}-500 text-xs font-bold border border-{$status['color']}-500/20'>
        {$status['label']}
    </span>";
}
