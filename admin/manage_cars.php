<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') { header("Location: manage_blogs.php"); exit; }
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$success_msg = "";
$error_msg = "";
$admin_id = $_SESSION['admin_id'];
 
// Delete Car Logic

// Delete Car Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $id = $_POST['delete_id'];
        // Get car details for logging
        $stmt = $pdo->prepare("SELECT brand, model, image_path FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        $car = $stmt->fetch();
        
        if ($car) {
            if (strpos($car['image_path'], 'uploads/cars/') !== false) {
                $full_path = '../' . $car['image_path'];
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
            $stmt->execute([$id]);
            
            log_activity($pdo, $admin_id, "Deleted Vehicle", "Removed " . $car['brand'] . " " . $car['model'] . " from fleet.");
            
            $_SESSION['success'] = "Vehicle removed from fleet.";
            header("Location: manage_cars.php");
            exit;
        }
    } catch (Exception $e) {
        $error_msg = "Error deleting vehicle: " . $e->getMessage();
    }
}

// Add Car Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    try {
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $price = $_POST['price'];
        $seats = $_POST['seats'];
        $transmission = $_POST['transmission'];
        $fuel = $_POST['fuel_type'];
        $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
        
        $image_path = $_POST['image_url']; 

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/cars/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('car_', true) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $new_filename)) {
                $image_path = 'uploads/cars/' . $new_filename;
            }
        }

        if (empty($image_path)) throw new Exception("Please provide an image URL or upload a file.");

        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO cars (brand, model, price_per_day, seats, transmission, fuel_type, image_path, discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $model, $price, $seats, $transmission, $fuel, $image_path, $discount]);
        $new_car_id = $pdo->lastInsertId();

        // Handle Multiple Additional Images
        if (isset($_FILES['additional_images'])) {
            $files = $_FILES['additional_images'];
            $upload_dir = '../uploads/cars/';
            
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $fname = uniqid('gallery_', true) . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $fname)) {
                        $img_path = 'uploads/cars/' . $fname;
                        $stmt_img = $pdo->prepare("INSERT INTO car_images (car_id, image_path) VALUES (?, ?)");
                        $stmt_img->execute([$new_car_id, $img_path]);
                    }
                }
            }
        }

        $pdo->commit();
        log_activity($pdo, $admin_id, "Added Vehicle", "Added new vehicle: $brand $model with multiple images.");

        $_SESSION['success'] = "Vehicle added successfully!";
        header("Location: manage_cars.php");
        exit;
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

if (isset($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Search and Filter Logic
$search = $_GET['search'] ?? "";
$max_price = $_GET['max_price'] ?? "";
$transmission_filter = $_GET['transmission'] ?? "";
$fuel_filter = $_GET['fuel'] ?? "";
$status_filter = $_GET['status'] ?? "";

$query = "SELECT c.*, MAX(b.return_date) as last_return_date 
          FROM cars c 
          LEFT JOIN bookings b ON c.id = b.car_id AND b.status = 'confirmed'
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (c.brand LIKE ? OR c.model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($max_price)) {
    $query .= " AND c.price_per_day <= ?";
    $params[] = $max_price;
}

if (!empty($transmission_filter)) {
    $query .= " AND c.transmission = ?";
    $params[] = $transmission_filter;
}

if (!empty($fuel_filter)) {
    $query .= " AND c.fuel_type LIKE ?";
    $params[] = "%$fuel_filter%";
}

if (!empty($status_filter)) {
    $query .= " AND c.status = ?";
    $params[] = $status_filter;
}

$query .= " GROUP BY c.id ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cars = $stmt->fetchAll();

// Get Fleet Statistics
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM cars");
$stats['total_cars'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as available FROM cars WHERE status = 'available'");
$stats['available'] = $stmt->fetch()['available'];

$stmt = $pdo->query("SELECT COUNT(*) as reserved FROM cars WHERE status = 'reserved'");
$stats['reserved'] = $stmt->fetch()['reserved'];

$stmt = $pdo->query("SELECT COUNT(*) as maintenance FROM cars WHERE status = 'maintenance'");
$stats['maintenance'] = $stmt->fetch()['maintenance'];

$stmt = $pdo->query("SELECT SUM(price_per_day) as total_revenue FROM cars");
$stats['daily_potential'] = $stmt->fetch()['total_revenue'] ?? 0;

$stmt = $pdo->query("SELECT AVG(price_per_day) as avg_price FROM cars");
$stats['avg_price'] = $stmt->fetch()['avg_price'] ?? 0;

// Get unique fuel types
$stmt = $pdo->query("SELECT DISTINCT fuel_type FROM cars ORDER BY fuel_type");
$fuel_types = $stmt->fetchAll();

// Count displayed cars
$displayed_cars = count($cars);
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة الأسطول | أوتو لاين</title>
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
        .input-premium {
            background: rgba(18, 17, 15, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            border-color: #c9a96e;
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1);
            background: rgba(18, 17, 15, 1);
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
                <div class="mb-8 p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3 animate-fade-up">
                    <span class="material-symbols-outlined text-emerald-500 text-xl flex-shrink-0">check_circle</span>
                    <p class="text-emerald-500 font-bold flex-1"><?= htmlspecialchars($success_msg) ?></p>
                    <button onclick="this.parentElement.style.display='none'" class="text-emerald-500 hover:text-emerald-400 flex-shrink-0">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (!empty($error_msg)): ?>
                <div class="mb-8 p-5 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center gap-3 animate-fade-up">
                    <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">error</span>
                    <p class="text-red-500 font-bold flex-1"><?= htmlspecialchars($error_msg) ?></p>
                    <button onclick="this.parentElement.style.display='none'" class="text-red-500 hover:text-red-400 flex-shrink-0">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <!-- Mobile Toggle -->
                        <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div>
                            <h2 class="text-4xl font-black text-white tracking-tight">إدارة الأسطول</h2>
                            <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold">تحكم في مجموعتك الحصرية من السيارات</p>
                        </div>
                    </div>
                </header>

                <div class="space-y-10">
                    <!-- Toolbar & Search with Advanced Filters -->
                    <div class="glass-card p-8 rounded-[2.5rem] space-y-6 border border-white/5">
                        <!-- Live Search -->
                        <div class="relative">
                            <span class="material-symbols-outlined absolute right-5 top-1/2 -translate-y-1/2 text-slate-500 text-lg pointer-events-none">search</span>
                            <input type="text" id="liveSearch" value="<?= htmlspecialchars($search) ?>" placeholder="بحث فوري عن موديل أو ماركة..." autocomplete="off" class="w-full bg-[#12110f]/60 border border-white/5 rounded-2xl pr-14 pl-6 py-4 text-sm text-white focus:border-[#c9a96e]/50 focus:outline-none focus:ring-4 focus:ring-[#c9a96e]/5 transition-all"/>
                            <span id="liveSearchClear" onclick="clearLiveSearch()" class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 hover:text-slate-300 cursor-pointer text-base hidden">close</span>
                        </div>

                        <!-- Advanced Filters Row -->
                        <form method="GET" id="filtersForm" class="flex flex-wrap gap-4 items-end">
                            <!-- Hidden search input to preserve live search -->
                            <input type="hidden" name="search" id="searchHidden" value="<?= htmlspecialchars($search) ?>">
                            
                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">السعر اليومي (الحد الأقصى)</label>
                                <input type="number" name="max_price" value="<?= htmlspecialchars($max_price) ?>" placeholder="مثال: 500" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none transition-all"/>
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">ناقل الحركة</label>
                                <select name="transmission" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none appearance-none cursor-pointer">
                                    <option value="">كل الأنواع</option>
                                    <option value="Auto" <?= $transmission_filter == 'Auto' ? 'selected' : '' ?>>أوتوماتيك</option>
                                    <option value="Manual" <?= $transmission_filter == 'Manual' ? 'selected' : '' ?>>يدوي</option>
                                </select>
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">نوع الوقود</label>
                                <select name="fuel" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none appearance-none cursor-pointer">
                                    <option value="">كل الأنواع</option>
                                    <?php foreach ($fuel_types as $fuel): ?>
                                        <option value="<?= htmlspecialchars($fuel['fuel_type']) ?>" <?= $fuel_filter == $fuel['fuel_type'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fuel['fuel_type']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">الحالة</label>
                                <select name="status" class="w-full bg-[#12110f]/60 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:border-[#c9a96e]/50 focus:outline-none appearance-none cursor-pointer">
                                    <option value="">جميع الحالات</option>
                                    <option value="available" <?= $status_filter == 'available' ? 'selected' : '' ?>>متوفرة</option>
                                    <option value="reserved" <?= $status_filter == 'reserved' ? 'selected' : '' ?>>محجوزة</option>
                                    <option value="maintenance" <?= $status_filter == 'maintenance' ? 'selected' : '' ?>>صيانة</option>
                                </select>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="px-6 py-2.5 bg-[#c9a96e] hover:bg-white text-[#12110f] rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                                    تطبيق
                                </button>
                                <?php if (!empty($search) || !empty($max_price) || !empty($transmission_filter) || !empty($fuel_filter) || !empty($status_filter)): ?>
                                    <a href="manage_cars.php" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-slate-400 rounded-xl font-black text-xs uppercase tracking-widest transition-all inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined">refresh</span>
                                        إعادة تعيين
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Add Car Form (Full Width) -->
                    <div class="glass-card rounded-[2.5rem] border border-white/5 overflow-hidden group relative">
                        <!-- Decorative glow -->
                        <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#c9a96e]/5 blur-[100px] rounded-full opacity-40 group-hover:opacity-80 transition-opacity duration-1000 pointer-events-none"></div>
                        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-purple-500/3 blur-[80px] rounded-full opacity-30 pointer-events-none"></div>

                        <!-- Header -->
                        <div class="flex items-center gap-5 px-10 py-7 border-b border-white/5 bg-white/[0.015]">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#c9a96e]/20 to-[#c9a96e]/5 flex items-center justify-center text-[#c9a96e] flex-shrink-0 border border-[#c9a96e]/20">
                                <span class="material-symbols-outlined text-2xl">directions_car</span>
                            </div>
                            <div>
                                <h3 class="font-black text-lg text-white tracking-tight">إضافة سيارة جديدة</h3>
                                <p class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em] mt-0.5">توسيع الأسطول الملكي</p>
                            </div>
                            <div class="mr-auto flex items-center gap-2 text-[10px] text-slate-600 font-bold uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm text-[#c9a96e]/50">auto_awesome</span>
                                Auto Line Fleet
                            </div>
                        </div>

                        <!-- Form Body -->
                        <form method="POST" enctype="multipart/form-data" class="p-10 relative z-10">
                            <input type="hidden" name="add_car" value="1">

                            <!-- Main Fields Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 mb-6">
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">الماركة</label>
                                    <input type="text" name="brand" placeholder="BMW" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">الموديل</label>
                                    <input type="text" name="model" placeholder="M8 Competition" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">السعر اليومي (ج.م)</label>
                                    <input type="number" name="price" placeholder="450" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-[#c9a96e] font-black focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">نوع الوقود</label>
                                    <input type="text" name="fuel_type" placeholder="بنزين 98" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">المقاعد</label>
                                    <input type="number" name="seats" placeholder="5" min="1" max="20" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">ناقل الحركة</label>
                                    <select name="transmission" class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none appearance-none cursor-pointer">
                                        <option value="Auto">أوتوماتيك</option>
                                        <option value="Manual">يدوي</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">الخصم (%)</label>
                                    <input type="number" name="discount" placeholder="0" min="0" max="100" class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-emerald-500 font-bold focus:outline-none"/>
                                </div>
                            </div>

                            <!-- Upload Section -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <!-- Main Image Upload -->
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">الصورة الأساسية <span class="text-red-400">*</span></label>
                                    <input type="file" name="image_file" id="main_image" class="hidden" onchange="updateFileLabel(this, 'main-file-label')"/>
                                    <label for="main_image" id="main-file-label" class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white/[0.03] border-2 border-dashed border-white/10 rounded-2xl cursor-pointer hover:bg-[#c9a96e]/5 hover:border-[#c9a96e]/30 transition-all text-[10px] font-bold text-slate-400 group/upload">
                                        <span class="material-symbols-outlined text-lg text-slate-500 group-hover/upload:text-[#c9a96e] transition-colors">cloud_upload</span>
                                        <span class="truncate">اختر الصورة الرئيسية</span>
                                    </label>
                                </div>

                                <!-- Gallery Images Upload -->
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">صور المعرض <span class="text-slate-600">(اختياري)</span></label>
                                    <input type="file" name="additional_images[]" id="gallery_images" multiple class="hidden" onchange="updateFileLabel(this, 'gallery-file-label')"/>
                                    <label for="gallery_images" id="gallery-file-label" class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white/[0.03] border-2 border-dashed border-white/10 rounded-2xl cursor-pointer hover:bg-purple-500/5 hover:border-purple-500/30 transition-all text-[10px] font-bold text-slate-400 group/upload">
                                        <span class="material-symbols-outlined text-lg text-slate-500 group-hover/upload:text-purple-400 transition-colors">photo_library</span>
                                        <span class="truncate">صور إضافية متعددة</span>
                                    </label>
                                </div>

                                <!-- URL Input -->
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">أو رابط الصورة</label>
                                    <input type="text" name="image_url" placeholder="https://..." class="w-full bg-[#12110f]/40 border-2 border-white/5 rounded-2xl px-5 py-4 text-[11px] text-slate-400 focus:outline-none focus:border-[#c9a96e]/30 transition-all hover:border-white/10"/>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.25em] hover:shadow-[0_15px_40px_-10px_rgba(201,169,110,0.5)] transition-all hover:-translate-y-0.5 active:scale-95">
                                    <span class="material-symbols-outlined text-lg">hotel_class</span>
                                    إضافة إلى الأسطول
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Car List (Full Width Grid) -->
                    <div class="flex flex-col gap-8">
                        <!-- Counter and Status -->
                        <div class="glass-card px-8 py-5 rounded-[2rem] border border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e]">
                                    <span class="material-symbols-outlined text-xl">segment</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">تحليل العرض</p>
                                    <p class="text-sm font-black text-white">
                                        يتم عرض <span class="text-[#c9a96e] mx-1"><?= $displayed_cars ?></span> من أصل <span class="text-[#c9a96e]/60 mx-1"><?= $stats['total_cars'] ?></span> سيارة فاخرة
                                    </p>
                                </div>
                            </div>
                            <?php if (empty($cars) && !empty($search)): ?>
                                <div class="flex items-center gap-2 text-red-400/80">
                                    <span class="material-symbols-outlined text-sm">search_off</span>
                                    <p class="text-[10px] font-bold uppercase tracking-widest">لا توجد نتائج مطابقة لبحثك</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($cars)): ?>
                            <div class="glass-card p-24 rounded-[4rem] text-center border-dashed border-2 border-white/5">
                                <div class="w-24 h-24 rounded-[3rem] bg-white/[0.02] flex items-center justify-center mx-auto mb-8">
                                    <span class="material-symbols-outlined text-5xl text-slate-700">inventory_2</span>
                                </div>
                                <h4 class="text-2xl font-black text-slate-500">لا توجد نتائج مطابقة لمهام البحث</h4>
                                <p class="text-xs text-slate-700 mt-3 uppercase tracking-[0.3em] font-black">حاول استخدام كلمات مفتاحية أخرى</p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <?php foreach ($cars as $car): ?>
                            <div class="glass-card rounded-[3.5rem] overflow-hidden group hover:border-[#c9a96e]/40 transition-all duration-700 flex flex-col h-full hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.8)] relative">
                                <!-- Premium Watermark -->
                                <div class="absolute right-6 top-1/2 -rotate-90 origin-right text-[40px] font-black text-white/[0.02] pointer-events-none select-none tracking-[0.5em] uppercase whitespace-nowrap z-0">AUTOLUX COLLECTION</div>
                                
                                <div class="h-64 relative overflow-hidden">
                                    <?php $img_src = (strpos($car['image_path'], 'http') === 0) ? $car['image_path'] : '../' . $car['image_path']; ?>
                                    <img src="<?= htmlspecialchars($img_src) ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"/>
                                    
                                    <!-- Dynamic Gradient Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#12110f] via-[#12110f]/20 to-transparent opacity-90 transition-opacity duration-700 group-hover:opacity-60"></div>
                                    <div class="absolute inset-0 bg-gradient-to-tr from-[#c9a96e]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-8 left-8 z-10">
                                        <?php if ($car['status'] == 'reserved' && !empty($car['last_return_date'])): ?>
                                            <div class="bg-amber-500/10 backdrop-blur-2xl text-amber-200 text-[10px] font-black px-5 py-2.5 rounded-full border border-amber-500/30 shadow-2xl flex items-center gap-3">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                </span>
                                                حتى <?= date('d M', strtotime($car['last_return_date'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <?php
                                                $st = $car['status'];
                                                $st_ar = $st == 'available' ? 'جاهز للانطلاق' : ($st == 'maintenance' ? 'تحت العناية' : 'محجوز الآن');
                                                $st_color = $st == 'available' ? 'emerald' : ($st == 'maintenance' ? 'red' : 'amber');
                                            ?>
                                            <div class="bg-<?= $st_color ?>-500/10 backdrop-blur-2xl text-<?= $st_color ?>-200 text-[10px] font-black px-5 py-2.5 rounded-full border border-<?= $st_color ?>-500/30 shadow-2xl flex items-center gap-3">
                                                <div class="w-2 h-2 rounded-full bg-<?= $st_color ?>-500 shadow-[0_0_10px_rgba(var(--tw-color-<?= $st_color ?>-500),0.5)]"></div>
                                                <?= $st_ar ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Bottom Info Reveal -->
                                    <div class="absolute bottom-8 right-10 left-10 z-10">
                                        <div class="overflow-hidden">
                                            <p class="text-[10px] font-black text-[#c9a96e] uppercase tracking-[0.5em] mb-2 italic opacity-0 translate-y-4 group-hover:opacity-80 group-hover:translate-y-0 transition-all duration-500"><?= htmlspecialchars($car['brand']) ?></p>
                                        </div>
                                        <h4 class="text-3xl font-black text-white tracking-tighter transition-all duration-700 group-hover:-translate-y-1"><?= htmlspecialchars($car['model']) ?></h4>
                                    </div>
                                </div>

                                <div class="p-10 flex-1 flex flex-col relative z-10">
                                    <div class="flex items-center justify-between mb-8 pb-8 border-b border-white/5">
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">الاستثمار اليومي</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-3xl font-black text-white"><?= number_format($car['price_per_day']) ?> ج.م</span>
                                                <span class="text-[10px] font-black text-[#c9a96e] uppercase italic">صافي</span>
                                            </div>
                                        </div>
                                        <div class="w-14 h-14 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-center group-hover:bg-[#c9a96e]/10 group-hover:border-[#c9a96e]/20 transition-all duration-500">
                                            <span class="material-symbols-outlined text-[#c9a96e] text-2xl group-hover:rotate-12 transition-transform">auto_awesome</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-6 mb-10">
                                        <div class="flex flex-col items-center gap-3 p-4 rounded-3xl bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-all duration-500">
                                            <span class="material-symbols-outlined text-slate-600 text-xl group-hover:text-[#c9a96e]">settings_input_component</span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-white"><?= $car['transmission'] == 'Auto' ? 'أوتو' : 'يدوي' ?></span>
                                        </div>
                                        <div class="flex flex-col items-center gap-3 p-4 rounded-3xl bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-all duration-500">
                                            <span class="material-symbols-outlined text-slate-600 text-xl group-hover:text-[#c9a96e]">airline_seat_recline_extra</span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-white"><?= $car['seats'] ?> مقعد</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-3 p-4 rounded-3xl bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-all duration-500">
                                            <span class="material-symbols-outlined text-slate-600 text-xl group-hover:text-[#c9a96e]">local_gas_station</span>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-white"><?= mb_substr($car['fuel_type'], 0, 5) ?></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 mt-auto">
                                        <a href="edit_car.php?id=<?= $car['id'] ?>" class="flex-1 bg-white/5 hover:bg-[#c9a96e] hover:text-[#12110f] py-4.5 rounded-2xl flex items-center justify-center gap-3 text-[11px] font-black uppercase tracking-[0.2em] transition-all duration-500 group/btn">
                                            <span class="material-symbols-outlined text-lg group-hover/btn:rotate-12 transition-transform">edit_document</span>
                                            تخصيص
                                        </a>
                                        <form method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الأيقونة من الأسطول؟');" class="flex-none">
                                            <input type="hidden" name="delete_id" value="<?= $car['id'] ?>">
                                            <button type="submit" class="w-14 h-14 rounded-2xl bg-red-500/5 hover:bg-red-500/10 text-red-500/50 hover:text-red-500 flex items-center justify-center transition-all duration-500 group/del">
                                                <span class="material-symbols-outlined text-xl group-hover/del:scale-125 transition-all">delete_outline</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>


        // ── Live Search ──────────────────────────────────────────────────
        (function () {
            const searchInput  = document.getElementById('liveSearch');
            const clearBtn     = document.getElementById('liveSearchClear');
            const searchHidden = document.getElementById('searchHidden');
            const filtersForm  = document.getElementById('filtersForm');
            let debounceTimer  = null;

            if (!searchInput) return;

            // Toggle clear button visibility
            function toggleClear() {
                clearBtn.classList.toggle('hidden', searchInput.value.trim() === '');
            }

            searchInput.addEventListener('input', function () {
                toggleClear();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchHidden.value = searchInput.value.trim();
                    filtersForm.submit();
                }, 400); // 400ms debounce
            });

            // Initialise clear button state on load
            toggleClear();
        })();

        function clearLiveSearch() {
            const searchInput  = document.getElementById('liveSearch');
            const searchHidden = document.getElementById('searchHidden');
            const filtersForm  = document.getElementById('filtersForm');
            searchInput.value  = '';
            searchHidden.value = '';
            document.getElementById('liveSearchClear').classList.add('hidden');
            filtersForm.submit();
        }

        // Update file label with filename
        function updateFileLabel(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files && input.files.length > 0) {
                const count = input.files.length;
                const text  = count === 1 ? input.files[0].name : `${count} صور مختارة`;
                label.innerHTML = `<span class="material-symbols-outlined text-sm">check_circle</span><span class="truncate">${text}</span>`;
                label.classList.add('text-emerald-500', 'border-emerald-500/30', 'bg-emerald-500/5');
                label.classList.remove('text-slate-400', 'border-white/10', 'bg-white/[0.03]', 'border-dashed');
            }
        }

        // Auto-close alerts after 5 seconds
        document.querySelectorAll('[role="alert"]').forEach(alert => {
            setTimeout(() => { alert.style.display = 'none'; }, 5000);
        });
    </script>
</body>
</html>
