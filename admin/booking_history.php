<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

// Sync bookings first
update_expired_bookings($pdo);

$success_msg = $_SESSION['success'] ?? "";
unset($_SESSION['success']);
$error_msg = $_SESSION['error'] ?? "";
unset($_SESSION['error']);

// Filters Logic
$search = $_GET['search'] ?? "";
$brand_id = $_GET['brand'] ?? "";
$status_filter = $_GET['status'] ?? "";
$date_from = $_GET['date_from'] ?? "";
$date_to = $_GET['date_to'] ?? "";

$query = "SELECT b.*, c.brand, c.model, c.image_path, c.model_year 
          FROM bookings b 
          JOIN cars c ON b.car_id = c.id 
          WHERE b.status IN ('completed', 'cancelled') ";
$params = [];

if (!empty($search)) {
    $query .= " AND (b.customer_name LIKE ? OR b.customer_phone LIKE ? OR c.model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $query .= " AND b.pickup_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND b.return_date <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Get Stats
$stats = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as finished,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue
FROM bookings WHERE status IN ('completed', 'cancelled')")->fetch();

?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>سجل الحجوزات | أوتو لاين</title>
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
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="max-w-[1600px] mx-auto animate-fade-in">
                <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-4xl font-black text-white tracking-tight">سجل الحجوزات</h2>
                        <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold">الأرشيف الكامل للعمليات السابقة</p>
                    </div>
                </header>

                <!-- Filters -->
                <div class="glass-card p-8 rounded-[2.5rem] mb-10 border border-white/5">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">بحث شامل</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm">search</span>
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="اسم العميل، الهاتف، أو الموديل..." class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl pr-12 pl-4 py-3 text-sm focus:border-[#c9a96e]/50 outline-none transition-all"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">من تاريخ</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-3 text-sm focus:border-[#c9a96e]/50 outline-none transition-all"/>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">إلى تاريخ</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-3 text-sm focus:border-[#c9a96e]/50 outline-none transition-all"/>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-[#c9a96e] text-[#12110f] py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white transition-all">تصفية</button>
                            <a href="booking_history.php" class="px-3 bg-white/5 flex items-center justify-center rounded-xl text-slate-400 hover:text-white transition-all">
                                <span class="material-symbols-outlined text-lg">refresh</span>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Bookings Table -->
                <div class="glass-card rounded-[2.5rem] border border-white/5 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-white/[0.02] border-b border-white/5">
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">السيارة</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">العميل</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">الفترة</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">التكلفة</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">الحالة</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">تاريخ التنفيذ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-10 rounded-lg overflow-hidden bg-black/40 border border-white/5 flex-shrink-0">
                                                <img src="../<?= htmlspecialchars($b['image_path']) ?>" class="w-full h-full object-cover"/>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-white"><?= htmlspecialchars($b['brand'] . ' ' . $b['model']) ?></p>
                                                <p class="text-[9px] font-bold text-[#c9a96e] uppercase tracking-widest"><?= $b['model_year'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-sm font-bold text-white"><?= htmlspecialchars($b['customer_name']) ?></p>
                                        <p class="text-[10px] text-slate-500 font-medium"><?= htmlspecialchars($b['customer_phone']) ?></p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2 text-[10px] font-bold">
                                            <span class="text-slate-400 capitalize"><?= date('d M', strtotime($b['pickup_date'])) ?></span>
                                            <span class="material-symbols-outlined text-[12px] text-slate-600 rotate-180">arrow_forward</span>
                                            <span class="text-slate-400 capitalize"><?= date('d M', strtotime($b['return_date'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-sm font-black text-[#c9a96e]"><?= number_format($b['total_price']) ?> ج.م</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if ($b['status'] == 'completed'): ?>
                                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20">تم بنجاح</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-red-500/10 text-red-500 text-[9px] font-black uppercase rounded-lg border border-red-500/20">ملغي</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-[10px] font-bold text-slate-600"><?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></p>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-20 text-center">
                                        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="material-symbols-outlined text-slate-600 text-3xl">inbox</span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">لا توجد حجوزات في السجل حالياً</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
