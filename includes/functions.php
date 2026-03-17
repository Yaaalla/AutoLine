<?php
// includes/functions.php

/**
 * Log an administrative action
 */
function log_activity($pdo, $admin_id, $action, $details = "") {
    // Disabled
}

function get_setting($pdo, $key, $default = "") {
    // Reverted to default values
    return $default;
}

function update_setting($pdo, $key, $value) {
    // Disabled
}

/**
 * إنشاء رسالة WhatsApp مع بيانات الحجز
 * Generate WhatsApp message with booking details
 * 
 * @param array $booking_data بيانات الحجز
 * @param array $car_data بيانات السيارة
 * @return string رسالة مشفرة لـ URL
 */
function generateWhatsAppMessage($booking_data, $car_data) {
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
    
    return $message;
}

/**
 * إنشاء رابط WhatsApp
 * Generate WhatsApp URL
 * 
 * @param string $phone_number رقم الهاتف
 * @param string $message الرسالة (غير مشفرة)
 * @return string رابط WhatsApp
 */
function generateWhatsAppURL($phone_number, $message) {
    return "https://wa.me/" . $phone_number . "?text=" . urlencode($message);
}

/**
 * إرسال البريد الإلكتروني بطلب الحجز
 * Send booking confirmation email
 * 
 * @param string $to البريد المستقبل
 * @param array $booking_data بيانات الحجز
 * @param array $car_data بيانات السيارة
 * @return bool نجاح الإرسال
 */
function sendBookingEmail($to, $booking_data, $car_data) {
    $subject = "تأكيد طلب الحجز - AutoLine";
    
    // بناء الرسالة بصيغة HTML
    $message = "
    <html dir='rtl'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background: #f8f7f6; padding: 20px; border-radius: 10px; }
            .header { background: #1e1a14; color: #c9a96e; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: white; padding: 20px; }
            .section { margin: 20px 0; padding: 15px; border-left: 4px solid #c9a96e; }
            .section h3 { color: #c9a96e; margin-top: 0; }
            .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
            .label { font-weight: bold; color: #666; }
            .value { color: #333; }
            .footer { background: #2d281f; color: #999; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
            .price-total { font-size: 18px; font-weight: bold; color: #c9a96e; padding: 10px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🚗 شكراً لاختيارك AutoLine</h1>
                <p>تأكيد طلب الحجز</p>
            </div>
            
            <div class='content'>
                <p>مرحباً " . htmlspecialchars($booking_data['name']) . ",</p>
                <p>شكراً على اختيارك خدمات AutoLine. نحن متحمسون لخدمتك!</p>
                
                <div class='section'>
                    <h3>📋 بيانات العميل</h3>
                    <div class='info-row'>
                        <span class='label'>الاسم:</span>
                        <span class='value'>" . htmlspecialchars($booking_data['name']) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>البريد الإلكتروني:</span>
                        <span class='value'>" . htmlspecialchars($booking_data['email']) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>رقم الهاتف:</span>
                        <span class='value'>" . htmlspecialchars($booking_data['phone']) . "</span>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>🏎️ تفاصيل السيارة</h3>
                    <div class='info-row'>
                        <span class='label'>النوع:</span>
                        <span class='value'>" . htmlspecialchars($car_data['brand']) . ' ' . htmlspecialchars($car_data['model']) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>ناقل الحركة:</span>
                        <span class='value'>" . ($car_data['transmission'] == 'Auto' ? 'أوتوماتيك' : 'يدوي') . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>عدد المقاعد:</span>
                        <span class='value'>" . htmlspecialchars($car_data['seats']) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>نوع الوقود:</span>
                        <span class='value'>" . htmlspecialchars($car_data['fuel_type']) . "</span>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>📅 فترة الحجز</h3>
                    <div class='info-row'>
                        <span class='label'>تاريخ الاستلام:</span>
                        <span class='value'>" . date('d/m/Y', strtotime($booking_data['pickup_date'])) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>تاريخ العودة:</span>
                        <span class='value'>" . date('d/m/Y', strtotime($booking_data['return_date'])) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>عدد الأيام:</span>
                        <span class='value'>" . $booking_data['days'] . " يوم</span>
                    </div>
                </div>
                
                    <div class='info-row'>
                        <span class='label'>السعر اليومي:</span>
                        <span class='value'>
                            " . (isset($car_data['discount']) && $car_data['discount'] > 0 ? 
                                "<span style='text-decoration: line-through; color: #999;'>" . number_format($car_data['price_per_day'], 2) . "</span> " . number_format($car_data['price_per_day'] * (1 - ($car_data['discount'] / 100)), 2) : 
                                number_format($car_data['price_per_day'], 2)) . " ج.م
                        </span>
                    </div>
                    <div class='info-row'>
                        <span class='label'>عدد الأيام:</span>
                        <span class='value'>" . $booking_data['days'] . " أيام</span>
                    </div>
                    <div class='info-row price-total'>
                        <span class='label'>الإجمالي:</span>
                        <span class='value'>" . number_format($booking_data['total_price'], 2) . " ج.م</span>
                    </div>
                </div>
                
                <div class='section'>
                    <h3>📞 تواصل معنا</h3>
                    <p>للمزيد من المعلومات أو إذا كان لديك أي استفسارات، يرجى التواصل معنا:</p>
                    <p>📱 WhatsApp: " . CONTACT_PHONE . "</p>
                    <p>📧 البريد الإلكتروني: " . FROM_EMAIL . "</p>
                </div>
            </div>
            
            <div class='footer'>
                <p>© 2026 AutoLine. جميع الحقوق محفوظة.</p>
                <p>نحن نتطلع لخدمتك قريباً!</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // إعدادات البريد
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
    
    // إرسال البريد
    return mail($to, $subject, $message, $headers);
}

/**
 * إرسال بريد إلكتروني للمسؤول بالحجز الجديد
 * Send admin notification email
 * 
 * @param array $booking_data بيانات الحجز
 * @param array $car_data بيانات السيارة
 * @return bool نجاح الإرسال
 */
function sendAdminNotification($booking_data, $car_data) {
    $subject = "طلب حجز جديد - " . htmlspecialchars($car_data['brand'] . ' ' . $car_data['model']);
    
    $message = '
    <html dir="rtl">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .admin-container { max-width: 600px; margin: 0 auto; background: #fff; border: 2px solid #c9a96e; }
            .admin-header { background: #1e1a14; color: white; padding: 20px; text-align: center; }
            .admin-content { padding: 20px; }
            .admin-section { margin: 15px 0; padding: 10px; background: #f8f7f6; border-radius: 5px; }
            .admin-row { padding: 5px 0; }
            .admin-row strong { color: #c9a96e; }
        </style>
    </head>
    <body>
        <div class="admin-container">
            <div class="admin-header">
                <h2>⚠️ طلب حجز جديد</h2>
            </div>
            <div class="admin-content">
                <div class="admin-section">
                    <h3>👤 بيانات العميل</h3>
                    <div class="admin-row"><strong>الاسم:</strong> ' . htmlspecialchars($booking_data['name']) . '</div>
                    <div class="admin-row"><strong>البريد:</strong> ' . htmlspecialchars($booking_data['email']) . '</div>
                    <div class="admin-row"><strong>الهاتف:</strong> ' . htmlspecialchars($booking_data['phone']) . '</div>
                </div>
                
                <div class="admin-section">
                    <h3>🏎️ السيارة المطلوبة</h3>
                    <div class="admin-row"><strong>النوع:</strong> ' . htmlspecialchars($car_data['brand']) . ' ' . htmlspecialchars($car_data['model']) . '</div>
                </div>
                
                <div class="admin-section">
                    <h3>📅 فترة الحجز</h3>
                    <div class="admin-row"><strong>من:</strong> ' . date('d/m/Y', strtotime($booking_data['pickup_date'])) . '</div>
                    <div class="admin-row"><strong>إلى:</strong> ' . date('d/m/Y', strtotime($booking_data['return_date'])) . '</div>
                    <div class="admin-row"><strong>الأيام:</strong> ' . $booking_data['days'] . ' يوم</div>
                </div>
                
                <div class="admin-section">
                    <h3>💰 المبلغ</h3>
                    <div class="admin-row"><strong>الإجمالي:</strong> ' . number_format($booking_data['total_price'], 2) . ' ج.م</div>
                </div>
                
                <p style="color: #999; font-size: 12px; margin-top: 20px;">
                    تم إنشاء هذا الطلب في: ' . date('d/m/Y H:i:s') . '
                </p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    
    return mail(ADMIN_EMAIL, $subject, $message, $headers);
}

/**
 * CSRF Protection Helpers
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Secure File Upload Helper
 */
function handle_secure_upload($file_array, $upload_dir, $prefix = 'file_') {
    if (!isset($file_array) || $file_array['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 10 * 1024 * 1024; // 10MB

    // 1. Check size
    if ($file_array['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds 5MB limit.'];
    }

    // 2. Validate MIME type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file_array['tmp_name']);
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and WebP are allowed.'];
    }

    // 3. Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // 4. Generate random filename
    $extension = pathinfo($file_array['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid($prefix, true) . '.' . $extension;
    $target_path = $upload_dir . $new_filename;

    // 5. Move file
    if (move_uploaded_file($file_array['tmp_name'], $target_path)) {
        return ['success' => true, 'path' => $new_filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

/**
 * Check if a car is available for a given period
 */
function is_car_available($pdo, $car_id, $pickup_date, $return_date, $exclude_booking_id = null) {
    $query = "SELECT COUNT(*) FROM bookings 
              WHERE car_id = ? 
              AND status IN ('confirmed', 'pending') 
              AND (
                  (pickup_date BETWEEN ? AND ?) OR 
                  (return_date BETWEEN ? AND ?) OR 
                  (? BETWEEN pickup_date AND return_date)
              )";
    
    $params = [$car_id, $pickup_date, $return_date, $pickup_date, $return_date, $pickup_date];
    
    if ($exclude_booking_id) {
        $query .= " AND id != ?";
        $params[] = $exclude_booking_id;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn() == 0;
}

/**
 * Update car and booking status based on current time
 */
function update_expired_bookings($pdo) {
    $now = date('Y-m-d H:i:s');
    
    // 1. Mark completed bookings
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE status = 'confirmed' AND return_date < ?");
    $stmt->execute([$now]);
    
    // 2. Identify cars that should be available again
    // Logic: Car is available if it has NO confirmed/pending bookings at this moment
    $pdo->query("UPDATE cars SET status = 'available' WHERE status = 'reserved'");
    
    $pdo->query("UPDATE cars c 
                 INNER JOIN bookings b ON c.id = b.car_id 
                 SET c.status = 'reserved' 
                 WHERE b.status IN ('confirmed', 'pending') 
                 AND NOW() BETWEEN b.pickup_date AND b.return_date");
}
?>

