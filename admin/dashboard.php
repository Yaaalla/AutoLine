<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

// Stats
$total_cars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status != 'cancelled'")->fetchColumn() ?: 0;

// Recent Bookings
$recent_bookings = $pdo->query("SELECT b.*, c.brand, c.model FROM bookings b JOIN cars c ON b.car_id = c.id ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>لوحة التحكم | أوتو لوكس</title>
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
        .stat-gradient-gold {
            background: linear-gradient(135deg, #c9a96e 0%, #e1c48f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="animate-fade-up">
                <header class="flex justify-between items-center mb-12">
                    <div class="flex items-center gap-6">
                        <!-- Mobile Toggle -->
                        <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div>
                            <h2 class="text-4xl font-black text-white tracking-tight">نظرة عامة</h2>
                            <p class="text-slate-500 text-sm mt-1">مرحباً بك مجدداً في مركز التحكم الخاص بك</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2 rtl:space-x-reverse">
                            <div class="w-10 h-10 rounded-full border-2 border-[#12110f] bg-white/5 flex items-center justify-center text-[10px] font-bold">A</div>
                        </div>
                    </div>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-[#c9a96e]/5 blur-3xl group-hover:bg-[#c9a96e]/10 transition-all"></div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e]">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <p class="text-slate-500 text-xs font-black uppercase tracking-[0.2em]">إجمالي الإيرادات</p>
                        </div>
                        <p class="text-5xl font-black stat-gradient-gold">$<?= number_format($revenue) ?></p>
                        <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-emerald-500">
                            <span class="material-symbols-outlined text-sm">trending_up</span>
                            <span>+12% من الشهر الماضي</span>
                        </div>
                    </div>

                    <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 blur-3xl group-hover:bg-white/10 transition-all"></div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white/50">
                                <span class="material-symbols-outlined">directions_car</span>
                            </div>
                            <p class="text-slate-500 text-xs font-black uppercase tracking-[0.2em]">إجمالي الأسطول</p>
                        </div>
                        <p class="text-5xl font-black text-white"><?= $total_cars ?></p>
                        <p class="mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">سيارة فارهة نشطة</p>
                    </div>

                    <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 blur-3xl group-hover:bg-white/10 transition-all"></div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white/50">
                                <span class="material-symbols-outlined">calendar_month</span>
                            </div>
                            <p class="text-slate-500 text-xs font-black uppercase tracking-[0.2em]">الحجوزات</p>
                        </div>
                        <p class="text-5xl font-black text-white"><?= $total_bookings ?></p>
                        <p class="mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">طلب حجز مكتمل</p>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
                    <div class="p-10 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="font-black text-xl text-white">أحدث الحجوزات</h3>
                            <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-widest">مراقبة النشاط المباشر</p>
                        </div>
                        <a href="manage_bookings.php" class="px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-black uppercase tracking-widest transition-all">عرض الكل</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-white/[0.02]">
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">العميل</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المركبة</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">التاريخ</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المبلغ</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($recent_bookings as $b): ?>
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] font-black text-xs">
                                                <?= mb_substr($b['customer_name'], 0, 1) ?>
                                            </div>
                                            <span class="font-bold text-white text-sm"><?= htmlspecialchars($b['customer_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-300"><?= htmlspecialchars($b['brand']) ?></span>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?= htmlspecialchars($b['model']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span class="text-xs font-bold text-slate-400 capitalize"><?= date('d M, Y', strtotime($b['pickup_date'])) ?></span>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span class="text-sm font-black text-[#c9a96e]">$<?= number_format($b['total_price']) ?></span>
                                    </td>
                                    <td class="px-10 py-8">
                                        <?php
                                            $status_map = [
                                                'pending' => ['label' => 'قيد الانتظار', 'color' => 'amber'],
                                                'confirmed' => ['label' => 'مؤكد', 'color' => 'blue'],
                                                'completed' => ['label' => 'مكتمل', 'color' => 'emerald'],
                                                'cancelled' => ['label' => 'ملغي', 'color' => 'red']
                                            ];
                                            $status = isset($status_map[$b['status']]) ? $status_map[$b['status']] : ['label' => $b['status'], 'color' => 'slate'];
                                        ?>
                                        <span class="px-4 py-2 rounded-full bg-<?= $status['color'] ?>-500/10 text-<?= $status['color'] ?>-500 text-[10px] font-black uppercase tracking-widest border border-<?= $status['color'] ?>-500/20">
                                            <?= $status['label'] ?>
                                        </span>
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
</body>
</html>
