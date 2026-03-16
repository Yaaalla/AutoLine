<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT b.*, c.brand, c.model, c.image_path, c.transmission, c.seats, c.fuel_type, c.price_per_day
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: manage_bookings.php");
    exit;
}

$pickup = new DateTime($booking['pickup_date']);
$return = new DateTime($booking['return_date']);
$days = $return->diff($pickup)->days ?: 1;
?>

<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تفاصيل الحجز | أوتو لاين</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .glass-card {
            background: rgba(28, 28, 28, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .glass-card { background: white; border: none; }
        }
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8 no-print gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div>
                            <h2 class="text-4xl font-black text-white tracking-tight">تفاصيل الحجز #<?= $booking['id'] ?></h2>
                            <p class="text-slate-500 text-sm mt-1">معلومات كاملة عن الحجز والعميل</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="manage_bookings.php" class="bg-white/5 hover:bg-white/10 text-slate-400 px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                            عودة
                        </a>
                        <button onclick="window.print()" class="bg-[#c9a96e] hover:bg-white text-[#12110f] px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined">print</span>
                            طباعة
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="space-y-6">
                    <!-- Booking Status -->
                    <div class="glass-card p-8 rounded-2xl">
                        <h3 class="text-xl font-black mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">info</span>
                            حالة الحجز
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-slate-500 text-sm mb-2">رقم الحجز</p>
                                <p class="text-2xl font-black text-[#c9a96e]">#<?= $booking['id'] ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">الحالة</p>
                                <?php
                                    $status_colors = [
                                        'pending' => 'amber',
                                        'confirmed' => 'blue',
                                        'completed' => 'emerald',
                                        'cancelled' => 'red'
                                    ];
                                    $status_labels = [
                                        'pending' => 'قيد الانتظار',
                                        'confirmed' => 'مؤكد',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي'
                                    ];
                                    $color = $status_colors[$booking['status']] ?? 'slate';
                                    $label = $status_labels[$booking['status']] ?? $booking['status'];
                                ?>
                                <span class="px-4 py-2 rounded-lg bg-<?= $color ?>-500/10 text-<?= $color ?>-500 text-sm font-bold inline-block border border-<?= $color ?>-500/20">
                                    <?= $label ?>
                                </span>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">تاريخ الحجز</p>
                                <p class="font-bold"><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="glass-card p-8 rounded-2xl">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">person</span>
                            بيانات العميل
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-slate-500 text-sm mb-2">الاسم الكامل</p>
                                <p class="text-lg font-bold text-white"><?= htmlspecialchars($booking['customer_name']) ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">البريد الإلكتروني</p>
                                <p class="text-lg font-bold text-[#c9a96e]"><?= htmlspecialchars($booking['customer_email']) ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">رقم الهاتف</p>
                                <p class="text-lg font-bold text-white"><?= htmlspecialchars($booking['customer_phone']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Car Info -->
                    <div class="glass-card p-8 rounded-2xl">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">directions_car</span>
                            بيانات السيارة
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <?php $img = (strpos($booking['image_path'], 'http') === 0) ? $booking['image_path'] : '../' . $booking['image_path']; ?>
                                <img src="<?= $img ?>" alt="السيارة" class="w-full h-48 object-cover rounded-xl border border-white/5"/>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-slate-500 text-sm mb-1">ماركة السيارة</p>
                                    <p class="text-2xl font-black text-white"><?= htmlspecialchars($booking['brand']) ?></p>
                                </div>
                                <div>
                                    <p class="text-slate-500 text-sm mb-1">الموديل</p>
                                    <p class="text-lg font-bold text-slate-300"><?= htmlspecialchars($booking['model']) ?></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-slate-500 text-xs mb-1 uppercase">ناقل الحركة</p>
                                        <p class="font-bold"><?= $booking['transmission'] == 'Automatic' ? 'أوتوماتيك' : 'يدوي' ?></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 text-xs mb-1 uppercase">عدد المقاعد</p>
                                        <p class="font-bold"><?= $booking['seats'] ?> مقاعد</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 text-xs mb-1 uppercase">نوع الوقود</p>
                                        <p class="font-bold"><?= htmlspecialchars($booking['fuel_type']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 text-xs mb-1 uppercase">السعر اليومي</p>
                                        <p class="font-bold text-[#c9a96e]"><?= number_format($booking['price_per_day'], 2) ?> ج.م</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Period -->
                    <div class="glass-card p-8 rounded-2xl">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">calendar_month</span>
                            فترة الحجز
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-slate-500 text-sm mb-2">تاريخ الاستلام</p>
                                <p class="text-lg font-bold text-white"><?= $pickup->format('d/m/Y') ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?= $pickup->format('l') ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">تاريخ العودة</p>
                                <p class="text-lg font-bold text-white"><?= $return->format('d/m/Y') ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?= $return->format('l') ?></p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-sm mb-2">عدد الأيام</p>
                                <p class="text-3xl font-black text-[#c9a96e]"><?= $days ?></p>
                                <p class="text-xs text-slate-500 mt-1">يوم</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="glass-card p-8 rounded-2xl bg-gradient-to-br from-[#c9a96e]/10 to-transparent border-[#c9a96e]/20">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">payments</span>
                            التسعير والإجمالي
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                <span class="text-slate-400">السعر اليومي</span>
                                <span class="font-bold"><?= number_format($booking['price_per_day'], 2) ?> ج.م</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                <span class="text-slate-400">عدد الأيام</span>
                                <span class="font-bold"><?= $days ?> أيام</span>
                            </div>
                            <div class="flex justify-between items-center py-4 bg-white/5 px-4 rounded-xl border border-[#c9a96e]/20">
                                <span class="text-lg font-black">المبلغ الإجمالي</span>
                                <span class="text-3xl font-black text-[#c9a96e]"><?= number_format($booking['total_price'], 2) ?> ج.م</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 no-print">
                        <a href="manage_bookings.php" class="flex-1 bg-white/10 hover:bg-white/20 text-white px-6 py-4 rounded-xl font-bold text-center transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">arrow_back</span>
                            العودة
                        </a>
                        <button onclick="window.print()" class="flex-1 bg-[#c9a96e] hover:bg-white text-[#12110f] px-6 py-4 rounded-xl font-bold text-center transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">print</span>
                            طباعة
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>


</body>
</html>
