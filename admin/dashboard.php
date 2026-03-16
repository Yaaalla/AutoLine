<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    header("Location: manage_blogs.php");
    exit;
}
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

// Get Statistics
$stats = [];

// Total Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
$stats['total_bookings'] = $stmt->fetch()['count'];

// Pending Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$stats['pending'] = $stmt->fetch()['count'];

// Confirmed Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'");
$stats['confirmed'] = $stmt->fetch()['count'];

// Completed Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'");
$stats['completed'] = $stmt->fetch()['count'];

// Total Revenue
$stmt = $pdo->query("SELECT SUM(total_price) as total FROM bookings WHERE status IN ('confirmed', 'completed')");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;

// Average Booking Value
$stmt = $pdo->query("SELECT AVG(total_price) as avg FROM bookings WHERE status IN ('confirmed', 'completed')");
$stats['avg_booking'] = $stmt->fetch()['avg'] ?? 0;

// Total Cars
$stats['total_cars'] = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();

// Recent Bookings
$stmt = $pdo->prepare("
    SELECT b.*, c.brand, c.model, c.image_path 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$stmt->execute();
$recent_bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>لوحة التحكم | أوتو لاين</title>
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
        .stat-card {
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.1) 0%, rgba(201, 169, 110, 0) 100%);
            border: 1px solid rgba(201, 169, 110, 0.2);
        }
        .stat-card:hover {
            border-color: rgba(201, 169, 110, 0.5);
            background: linear-gradient(135deg, rgba(201, 169, 110, 0.15) 0%, rgba(201, 169, 110, 0.05) 100%);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }
        .stat-item {
            animation: fadeUp 0.6s ease-out forwards;
        }
        .stat-item:nth-child(2) { animation-delay: 0.1s; }
        .stat-item:nth-child(3) { animation-delay: 0.2s; }
        .stat-item:nth-child(4) { animation-delay: 0.3s; }
        .stat-item:nth-child(5) { animation-delay: 0.4s; }
        .stat-item:nth-child(6) { animation-delay: 0.5s; }
        .stat-gradient-gold {
            background: linear-gradient(135deg, #c9a96e 0%, #e1c48f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="max-w-[2000px] mx-auto">
                <!-- Header -->
                <header class="mb-12 flex items-center gap-6 animate-fade-up">
                    <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div>
                        <h1 class="text-5xl font-black text-white tracking-tight">لوحة التحكم</h1>
                        <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold">مرحباً بك في نظام إدارة اوتو لاين</p>
                    </div>
                </header>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <!-- Total Bookings -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">إجمالي الحجوزات</p>
                                <p class="text-4xl font-black text-white"><?= $stats['total_bookings'] ?></p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-blue-500/20 to-blue-600/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-blue-500">event</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-4 uppercase tracking-widest">
                            منذ بداية النظام
                        </p>
                    </div>

                    <!-- Pending Bookings -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">قيد الانتظار</p>
                                <p class="text-4xl font-black text-amber-500"><?= $stats['pending'] ?></p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-amber-500/20 to-amber-600/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-amber-500">schedule</span>
                            </div>
                        </div>
                        <a href="manage_bookings.php?status=pending" class="text-[11px] text-amber-500 mt-4 uppercase tracking-widest hover:text-amber-400 font-bold">
                            عرض الحجوزات →
                        </a>
                    </div>

                    <!-- Confirmed Bookings -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">مؤكدة</p>
                                <p class="text-4xl font-black text-blue-500"><?= $stats['confirmed'] ?></p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-blue-500/20 to-blue-600/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-blue-500">verified</span>
                            </div>
                        </div>
                        <a href="manage_bookings.php?status=confirmed" class="text-[11px] text-blue-500 mt-4 uppercase tracking-widest hover:text-blue-400 font-bold">
                            عرض الحجوزات →
                        </a>
                    </div>

                    <!-- Completed Bookings -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">مكتملة</p>
                                <p class="text-4xl font-black text-emerald-500"><?= $stats['completed'] ?></p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-emerald-500">done_all</span>
                            </div>
                        </div>
                        <a href="manage_bookings.php?status=completed" class="text-[11px] text-emerald-500 mt-4 uppercase tracking-widest hover:text-emerald-400 font-bold">
                            عرض الحجوزات →
                        </a>
                    </div>

                    <!-- Total Revenue -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">إجمالي الإيرادات</p>
                                <p class="text-3xl font-black text-[#c9a96e]"><?= number_format($stats['revenue'], 2) ?> ج.م</p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-[#c9a96e]/20 to-[#c9a96e]/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-[#c9a96e]">payments</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-4 uppercase tracking-widest">من الحجوزات المؤكدة</p>
                    </div>

                    <!-- Average Booking -->
                    <div class="stat-card stat-item glass-card p-8 rounded-[2rem] transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-xs font-black uppercase tracking-[0.15em] mb-2">متوسط الحجز</p>
                                <p class="text-3xl font-black text-purple-500"><?= number_format($stats['avg_booking'], 2) ?> ج.م</p>
                            </div>
                            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-purple-500/20 to-purple-600/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-purple-500">trending_up</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-4 uppercase tracking-widest">قيمة الحجز المتوسطة</p>
                    </div>
                </div>



                <!-- Recent Bookings -->
                <div class="glass-card rounded-[2rem] overflow-hidden border border-white/5">
                    <div class="p-8 border-b border-white/5">
                        <h3 class="text-2xl font-black flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#c9a96e]">recent_actors</span>
                            آخر الحجوزات
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-white/[0.02]">
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المعرف</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">العميل</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">السيارة</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المبلغ</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">الحالة</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-left">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="px-8 py-6">
                                        <span class="font-black text-[#c9a96e]">#<?= $booking['id'] ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div>
                                            <p class="font-bold text-white"><?= htmlspecialchars($booking['customer_name']) ?></p>
                                            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($booking['customer_phone']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="font-bold text-white"><?= htmlspecialchars($booking['brand'] . ' ' . $booking['model']) ?></p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="font-black text-[#c9a96e]"><?= number_format($booking['total_price'], 2) ?> ج.م</span>
                                    </td>
                                    <td class="px-8 py-6">
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
                                        <span class="px-3 py-1 rounded-full bg-<?= $color ?>-500/10 text-<?= $color ?>-500 text-xs font-bold border border-<?= $color ?>-500/20">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-left">
                                        <a href="booking_details.php?id=<?= $booking['id'] ?>" class="inline-flex items-center gap-2 text-[#c9a96e] hover:text-white transition-colors font-bold text-sm">
                                            عرض
                                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // No local scripts needed, handled by sidebar.php
    </script>
</body>
</html>
