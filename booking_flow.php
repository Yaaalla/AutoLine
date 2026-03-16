<?php 
require_once 'config/db_connect.php'; 
require_once 'config/whatsapp_config.php';
require_once 'config/email_config.php';
require_once 'includes/functions.php';

$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: vehicles_gallery.php");
    exit;
}

// استخدام رقم WhatsApp من ملف الإعدادات
$whatsapp_number = WHATSAPP_NUMBER;

// Simple submission logic
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pickup = $_POST['pickup_date'] ?? '';
        $return = $_POST['return_date'] ?? '';
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // التحقق من البيانات
        if (empty($pickup) || empty($return) || empty($name) || empty($email) || empty($phone)) {
            throw new Exception("يرجى ملء جميع الحقول");
        }
        
        // Calculate total price
        $p_date = new DateTime($pickup);
        $r_date = new DateTime($return);
        $days = $r_date->diff($p_date)->days ?: 1;
        
        $daily_price = $car['price_per_day'];
        if (isset($car['discount']) && $car['discount'] > 0) {
            $daily_price = $car['price_per_day'] * (1 - ($car['discount'] / 100));
        }
        $total = $days * $daily_price;

        // إنشاء بيانات الحجز
        $booking_data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'pickup_date' => $pickup,
            'return_date' => $return,
            'days' => $days,
            'total_price' => $total
        ];
        
        // حفظ في قاعدة البيانات
        $insert = $pdo->prepare("INSERT INTO bookings (car_id, customer_name, customer_email, customer_phone, pickup_date, return_date, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$car_id, $name, $email, $phone, $pickup, $return, $total]);

        // إرسال البريد الإلكتروني للعميل
        sendBookingEmail($email, $booking_data, $car);
        
        // إرسال إشعار للمسؤول
        sendAdminNotification($booking_data, $car);
        
        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include 'includes/header.php'; 
?>

<main class="pt-24 min-h-screen bg-background-light dark:bg-background-dark">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <?php if (isset($success)): ?>
            <div class="glass-card p-12 rounded-[2.5rem] border border-emerald-500/20 text-center animate-fade-up">
                <div class="w-24 h-24 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-8">
                    <span class="material-symbols-outlined text-5xl text-emerald-500">check_circle</span>
                </div>
                <h2 class="text-4xl font-black text-white mb-4">تم استلام طلبك بنجاح!</h2>
                <p class="text-slate-400 text-lg mb-10 max-w-md mx-auto">
                    شكراً لك على ثقتك بـ AutoLine. تم إرسال تفاصيل الحجز إلى بريدك الإلكتروني وسيتم التواصل معك قريباً.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="vehicles_gallery.php" class="px-8 py-4 bg-primary text-background-dark rounded-xl font-black uppercase tracking-widest hover:bg-white transition-all">
                        العودة للأسطول
                    </a>
                    <a href="index.php" class="px-8 py-4 bg-white/5 text-white rounded-xl font-black uppercase tracking-widest hover:bg-white/10 transition-all">
                        الصفحة الرئيسية
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/20 p-8 rounded-3xl text-center mb-8">
                    <span class="material-symbols-outlined text-6xl text-red-500 mb-4">error</span>
                    <h2 class="text-2xl font-black text-red-600 mb-2">حدث خطأ!</h2>
                    <p class="text-red-600"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Form -->
                <div>
                    <h2 class="text-4xl font-black uppercase mb-8">احجز رحلتك</h2>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">الاسم الكامل</label>
                            <input type="text" name="name" required placeholder="ادخل اسمك بالكامل" class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">البريد الإلكتروني</label>
                            <input type="email" name="email" required placeholder="example@mail.com" class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">رقم الهاتف</label>
                            <input type="tel" name="phone" required placeholder="+20 1..." class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all text-left" dir="ltr"/>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">تاريخ الاستلام</label>
                                <input type="date" name="pickup_date" required class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">تاريخ العودة</label>
                                <input type="date" name="return_date" required class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-primary text-background-dark py-4 rounded-xl font-black uppercase tracking-widest hover:bg-white transition-all shadow-xl shadow-primary/20">
                            تأكيد طلب الحجز
                        </button>
                    </form>
                </div>

                <!-- Summary -->
                <div class="bg-slate-50 dark:bg-[#1c1a17] p-8 rounded-3xl border border-primary/10 h-fit sticky top-32">
                    <h3 class="text-xl font-black uppercase mb-6">ملخص الحجز</h3>
                    <div class="flex gap-4 mb-6 pb-6 border-b border-primary/5">
                        <img src="<?= htmlspecialchars($car['image_path']) ?>" class="w-24 h-16 object-cover rounded-lg"/>
                        <div>
                            <p class="text-xs font-bold text-primary"><?= $car['brand'] ?></p>
                            <p class="font-black"><?= $car['model'] ?></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-500 uppercase">السعر اليومي</span>
                            <span>
                                <?php if (isset($car['discount']) && $car['discount'] > 0): ?>
                                    <?php $discounted_price = $car['price_per_day'] * (1 - ($car['discount'] / 100)); ?>
                                    <span class="line-through text-slate-400 mr-2"><?= number_format($car['price_per_day']) ?></span>
                                    <span><?= number_format($discounted_price) ?> ج.م</span>
                                <?php else: ?>
                                    <?= number_format($car['price_per_day']) ?> ج.م
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-500 uppercase">التأمين</span>
                            <span class="text-emerald-500">مشمول</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
