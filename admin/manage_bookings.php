<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$success_msg = "";
$error_msg = "";
$admin_id = $_SESSION['admin_id'];

// Update Booking Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $booking_id = $_POST['booking_id'];
        $new_status = $_POST['new_status'];
        
        // Get booking info for logging
        $stmt = $pdo->prepare("SELECT b.*, c.brand, c.model FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if ($booking) {
            $car_id = $booking['car_id'];
            if ($new_status === 'cancelled') {
                $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
                $stmt->execute([$booking_id]);
                
                // Reset car to available if cancelled
                $stmt = $pdo->prepare("UPDATE cars SET status = 'available' WHERE id = ?");
                $stmt->execute([$car_id]);
                
                log_activity($pdo, $admin_id, "Deleted (Cancelled) Booking", "Removed booking #$booking_id (" . $booking['customer_name'] . "). Car marked as Available.");
                $_SESSION['success'] = "Booking cancelled and car made available.";
            } else {
                $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $booking_id]);
                
                // Sync car status
                if ($new_status === 'confirmed') {
                    $stmt = $pdo->prepare("UPDATE cars SET status = 'reserved' WHERE id = ?");
                    $stmt->execute([$car_id]);
                    $extra_msg = " Car marked as Reserved.";
                } elseif ($new_status === 'completed') {
                    $stmt = $pdo->prepare("UPDATE cars SET status = 'available' WHERE id = ?");
                    $stmt->execute([$car_id]);
                    $extra_msg = " Car marked as Available.";
                }
                
                log_activity($pdo, $admin_id, "Updated Booking Status", "Changed booking #$booking_id (" . $booking['customer_name'] . ") to $new_status." . ($extra_msg ?? ""));
                $_SESSION['success'] = "Booking status updated to $new_status." . ($extra_msg ?? "");
            }
            header("Location: manage_bookings.php");
            exit;
        }
    } catch (Exception $e) {
        $error_msg = "Error updating status: " . $e->getMessage();
    }
}

if (isset($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Filtering
$status_filter = $_GET['status'] ?? "";
$date_filter = $_GET['date'] ?? "";

$query = "SELECT b.*, c.brand, c.model, c.image_path FROM bookings b JOIN cars c ON b.car_id = c.id WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_filter)) {
    $query .= " AND DATE(b.pickup_date) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة الحجوزات | أوتو لاين</title>
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
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="animate-fade-up max-w-[1600px] mx-auto">
                
                <!-- Success Message -->
                <?php if (!empty($success_msg)): ?>
                <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3 animate-fade-up">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <p class="text-emerald-500 font-bold"><?= htmlspecialchars($success_msg) ?></p>
                    <button onclick="this.parentElement.style.display='none'" class="mr-auto text-emerald-500 hover:text-emerald-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (!empty($error_msg)): ?>
                <div class="mb-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center gap-3 animate-fade-up">
                    <span class="material-symbols-outlined text-red-500">error</span>
                    <p class="text-red-500 font-bold"><?= htmlspecialchars($error_msg) ?></p>
                    <button onclick="this.parentElement.style.display='none'" class="mr-auto text-red-500 hover:text-red-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <header class="mb-12 flex items-center gap-6">
                    <!-- Mobile Toggle -->
                    <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div>
                        <h2 class="text-4xl font-black text-white tracking-tight">إدارة الحجوزات</h2>
                        <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold">تتبع وإدارة طلبات الحجز المتميزة</p>
                    </div>
                </header>

                <!-- Filters -->
                <div class="glass-card p-8 rounded-[2.5rem] border border-white/5 mb-10">
                    <form method="GET" class="flex flex-wrap gap-6 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 mr-1">تصفية حسب الحالة</label>
                            <select name="status" class="w-full bg-[#12110f]/50 border border-white/5 rounded-2xl px-6 py-4 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none transition-all appearance-none cursor-pointer">
                                <option value="">كل الحالات</option>
                                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                                <option value="confirmed" <?= $status_filter == 'confirmed' ? 'selected' : '' ?>>مؤكد</option>
                                <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>مكتمل</option>
                                <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 mr-1">تاريخ الاستلام</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>" class="w-full bg-[#12110f]/50 border border-white/5 rounded-2xl px-6 py-4 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none transition-all cursor-pointer"/>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-[#c9a96e] hover:bg-white text-[#12110f] px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all">تطبيق</button>
                            <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                                <a href="manage_bookings.php" class="bg-white/5 hover:bg-white/10 text-slate-400 px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">refresh</span>
                                    إعادة ضبط
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="glass-card rounded-[3rem] overflow-hidden border border-white/5 shadow-2xl">
                    <div class="p-8 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-black text-white">قائمة الحجوزات</h3>
                            <p class="text-slate-500 text-xs mt-1 font-bold uppercase tracking-widest">
                                عدد الحجوزات: <span class="text-[#c9a96e]"><?= count($bookings) ?></span>
                            </p>
                        </div>
                        <a href="../booking_flow.html" class="px-6 py-3 rounded-xl bg-[#c9a96e] hover:bg-white text-[#12110f] font-black text-xs uppercase tracking-widest transition-all inline-flex items-center gap-2">
                            <span class="material-symbols-outlined">add</span>
                            حجز جديد
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-white/[0.02]">
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المعرف والتاريخ</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">تفاصيل العميل</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">السيارة المختارة</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">المبلغ الإجمالي</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">الحالة</th>
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-left">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="6" class="px-10 py-20 text-center">
                                            <div class="w-20 h-20 rounded-[2.5rem] bg-white/5 flex items-center justify-center mx-auto mb-6">
                                                <span class="material-symbols-outlined text-4xl text-slate-600">event_busy</span>
                                            </div>
                                            <p class="text-slate-500 font-bold uppercase tracking-widest">لا توجد حجوزات متاحة حالياً</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                
                                <?php foreach ($bookings as $b): ?>
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="px-10 py-8">
                                        <div class="flex flex-col">
                                            <span class="font-black text-white text-sm">#<?= $b['id'] ?></span>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1"><?= date('d M, Y', strtotime($b['created_at'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-white text-sm"><?= htmlspecialchars($b['customer_name'] ?? 'غير محدد') ?></span>
                                            <span class="text-xs text-slate-400 mt-1"><?= htmlspecialchars($b['customer_email'] ?? '') ?></span>
                                            <span class="text-[10px] text-[#c9a96e] font-black mt-2 tracking-widest"><?= htmlspecialchars($b['customer_phone'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <?php $img = (strpos($b['image_path'], 'http') === 0) ? $b['image_path'] : '../' . $b['image_path']; ?>
                                            <div class="w-16 h-12 rounded-xl overflow-hidden border border-white/5 shadow-xl bg-[#12110f]">
                                                <img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-200"><?= htmlspecialchars($b['brand']) ?></span>
                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?= htmlspecialchars($b['model']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 font-black text-xl text-[#c9a96e]">
                                        <?= number_format($b['total_price']) ?> ج.م
                                    </td>
                                    <td class="px-10 py-8">
                                        <?php
                                            $status_map = [
                                                'pending' => ['label' => 'قيد الانتظار', 'color' => 'amber'],
                                                'confirmed' => ['label' => 'مؤكد', 'color' => 'blue'],
                                                'completed' => ['label' => 'مكتمل', 'color' => 'emerald'],
                                                'cancelled' => ['label' => 'ملغي', 'color' => 'red']
                                            ];
                                            $st = isset($status_map[$b['status']]) ? $status_map[$b['status']] : ['label' => $b['status'], 'color' => 'slate'];
                                        ?>
                                        <span class="px-4 py-2 rounded-full bg-<?= $st['color'] ?>-500/10 text-<?= $st['color'] ?>-500 text-[9px] font-black uppercase tracking-[0.1em] border border-<?= $st['color'] ?>-500/20">
                                            <?= $st['label'] ?>
                                        </span>
                                    </td>
                                    <td class="px-10 py-8 text-left">
                                        <div class="flex gap-2 items-center">
                                            <a href="booking_details.php?id=<?= $b['id'] ?>" class="w-10 h-10 rounded-xl bg-[#c9a96e]/10 text-[#c9a96e] hover:bg-[#c9a96e] hover:text-[#12110f] transition-all flex items-center justify-center border border-[#c9a96e]/20" title="عرض التفاصيل">
                                                <span class="material-symbols-outlined text-lg">open_in_new</span>
                                            </a>
                                            
                                            <form method="POST" class="inline-flex gap-2">
                                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                                <input type="hidden" name="update_status" value="1">
                                                
                                                <?php if ($b['status'] == 'pending'): ?>
                                                    <button name="new_status" value="confirmed" class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center border border-blue-500/20" title="تأكيد">
                                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($b['status'] == 'confirmed'): ?>
                                                    <button name="new_status" value="completed" class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center border border-emerald-500/20" title="اكتمال">
                                                        <span class="material-symbols-outlined text-lg">done_all</span>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($b['status'] != 'cancelled' && $b['status'] != 'completed'): ?>
                                                    <button name="new_status" value="cancelled" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-500/20" title="إلغاء">
                                                        <span class="material-symbols-outlined text-lg">close</span>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
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
