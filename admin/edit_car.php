<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$success_msg = "";
$error_msg = "";

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: manage_cars.php"); exit; }

// Fetch Car Data
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) { header("Location: manage_cars.php"); exit; }

// Fetch Gallery Images
$stmt_gallery = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ?");
$stmt_gallery->execute([$id]);
$gallery = $stmt_gallery->fetchAll();

// Handle Gallery Image Deletion
if (isset($_POST['delete_gallery_id'])) {
    try {
        $img_id = (int)$_POST['delete_gallery_id'];
        $stmt = $pdo->prepare("SELECT image_path FROM car_images WHERE id = ? AND car_id = ?");
        $stmt->execute([$img_id, $id]);
        $img = $stmt->fetch();
        if ($img) {
            if (file_exists('../' . $img['image_path'])) unlink('../' . $img['image_path']);
            $pdo->prepare("DELETE FROM car_images WHERE id = ?")->execute([$img_id]);
            $_SESSION['success'] = "Gallery image removed.";
            header("Location: edit_car.php?id=$id");
            exit;
        }
    } catch (Exception $e) { $error_msg = $e->getMessage(); }
}

// Update Car Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_car'])) {
    try {
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $price = $_POST['price'];
        $seats = $_POST['seats'];
        $transmission = $_POST['transmission'];
        $fuel = $_POST['fuel_type'];
        $status = $_POST['status'];
        
        $image_path = $car['image_path'];

        $pdo->beginTransaction();

        // Check for new URL
        if (!empty($_POST['image_url'])) {
            $image_path = $_POST['image_url'];
        }

        // Check for new local file upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/cars/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (strpos($car['image_path'], 'uploads/cars/') !== false) {
                $old_file = '../' . $car['image_path'];
                if (file_exists($old_file)) unlink($old_file);
            }
            $file_ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('car_', true) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $new_filename)) {
                $image_path = 'uploads/cars/' . $new_filename;
            }
        }

        $stmt = $pdo->prepare("UPDATE cars SET brand=?, model=?, price_per_day=?, seats=?, transmission=?, fuel_type=?, image_path=?, status=? WHERE id=?");
        $stmt->execute([$brand, $model, $price, $seats, $transmission, $fuel, $image_path, $status, $id]);

        // Handle New Gallery Images
        if (isset($_FILES['new_gallery'])) {
            $files = $_FILES['new_gallery'];
            $upload_dir = '../uploads/cars/';
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $fname = uniqid('gallery_', true) . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $fname)) {
                        $img_path = 'uploads/cars/' . $fname;
                        $pdo->prepare("INSERT INTO car_images (car_id, image_path) VALUES (?, ?)")->execute([$id, $img_path]);
                    }
                }
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Vehicle updated successfully!";
        header("Location: manage_cars.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error updating vehicle: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تعديل السيارة | أوتو لوكس</title>
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
            <div class="max-w-6xl mx-auto animate-fade-up">
                <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <!-- Mobile Toggle -->
                        <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div>
                            <a href="manage_cars.php" class="inline-flex items-center gap-2 text-[#c9a96e] hover:text-white transition-colors mb-4 text-[10px] font-black uppercase tracking-[0.2em]">
                                <span class="material-symbols-outlined text-sm rotate-180">arrow_back</span>
                                العودة للأسطول
                            </a>
                            <h2 class="text-4xl font-black text-white tracking-tight">تعديل بيانات السيارة</h2>
                            <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></p>
                        </div>
                    </div>
                </header>

                <?php if ($error_msg): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-6 rounded-[1.5rem] mb-10 flex items-center gap-4">
                        <span class="material-symbols-outlined">error</span>
                        <span class="text-xs font-bold uppercase tracking-widest"><?= $error_msg ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <input type="hidden" name="update_car" value="1">
                    
                    <!-- Left Column: Primary Details -->
                    <div class="lg:col-span-7 space-y-10">
                        <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 space-y-8 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#c9a96e]/5 blur-3xl rounded-full"></div>
                            
                            <h3 class="font-black text-xl text-white flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] material-symbols-outlined text-sm">edit_note</span>
                                المواصفات الأساسية
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">الماركة التجارية</label>
                                    <input type="text" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">الموديل والطراز</label>
                                    <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">السعر اليومي ($)</label>
                                    <input type="number" name="price" value="<?= htmlspecialchars($car['price_per_day']) ?>" required class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-[#c9a96e] font-black focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">عدد المقاعد</label>
                                    <input type="number" name="seats" value="<?= htmlspecialchars($car['seats']) ?>" required class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">نوع الوقود</label>
                                    <input type="text" name="fuel_type" value="<?= htmlspecialchars($car['fuel_type']) ?>" required class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">ناقل الحركة</label>
                                    <select name="transmission" class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none appearance-none cursor-pointer">
                                        <option value="Automatic" <?= $car['transmission'] == 'Automatic' ? 'selected' : '' ?>>أوتوماتيك</option>
                                        <option value="Manual" <?= $car['transmission'] == 'Manual' ? 'selected' : '' ?>>يدوي</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mr-1">حالة التوفر</label>
                                    <select name="status" class="w-full input-premium rounded-2xl px-6 py-4 text-sm text-slate-100 focus:outline-none appearance-none cursor-pointer">
                                        <option value="available" <?= $car['status'] == 'available' ? 'selected' : '' ?>>متاح</option>
                                        <option value="reserved" <?= $car['status'] == 'reserved' ? 'selected' : '' ?>>محجوز</option>
                                        <option value="maintenance" <?= $car['status'] == 'maintenance' ? 'selected' : '' ?>>صيانة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 space-y-8">
                            <h3 class="font-black text-xl text-white flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] material-symbols-outlined text-sm">collections</span>
                                معرض الصور الإضافي
                            </h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                                <?php if (!empty($gallery)): ?>
                                    <?php foreach ($gallery as $img): ?>
                                        <div class="relative group aspect-square rounded-3xl overflow-hidden border border-white/5 bg-[#12110f]/50">
                                            <img src="../<?= htmlspecialchars($img['image_path']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"/>
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center backdrop-blur-[2px]">
                                                <button type="submit" name="delete_gallery_id" value="<?= $img['id'] ?>" class="w-12 h-12 bg-red-500 text-white rounded-2xl hover:bg-red-600 transition-all flex items-center justify-center shadow-xl translate-y-4 group-hover:translate-y-0 duration-300">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <label class="aspect-square rounded-3xl border-2 border-dashed border-white/10 hover:border-[#c9a96e]/30 hover:bg-[#c9a96e]/5 transition-all flex flex-col items-center justify-center gap-3 cursor-pointer group">
                                    <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-[#c9a96e] transition-colors">
                                        <span class="material-symbols-outlined">add_a_photo</span>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">إضافة صور</span>
                                    <input type="file" name="new_gallery[]" multiple class="hidden"/>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Visuals and Action -->
                    <div class="lg:col-span-5 space-y-10">
                        <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 space-y-8">
                            <h3 class="font-black text-xl text-white flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] material-symbols-outlined text-sm">image</span>
                                لمحة مرئية
                            </h3>
                            
                            <div class="aspect-[4/3] w-full bg-[#12110f] rounded-[2rem] overflow-hidden shadow-2xl border border-white/5 relative group">
                                <?php $img_src = (strpos($car['image_path'], 'http') === 0) ? $car['image_path'] : '../' . $car['image_path']; ?>
                                <img src="<?= htmlspecialchars($img_src) ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"/>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-6 right-6">
                                    <span class="px-4 py-2 rounded-full bg-[#c9a96e] text-[#12110f] text-[10px] font-black uppercase tracking-widest shadow-xl">الصورة الحالية</span>
                                </div>
                            </div>

                            <div class="space-y-6 pt-4">
                                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">تغيير الصورة الأساسية</label>
                                    <input type="text" name="image_url" placeholder="رابط صورة خارجي (اختياري)" class="w-full bg-[#12110f]/50 border-0 rounded-xl px-4 py-4 text-xs mb-4 focus:ring-1 focus:ring-[#c9a96e] placeholder:text-slate-700"/>
                                    <div class="flex items-center gap-4">
                                        <div class="h-px flex-1 bg-white/5"></div>
                                        <span class="text-[9px] font-black text-slate-700 uppercase">أو تحميل ملف</span>
                                        <div class="h-px flex-1 bg-white/5"></div>
                                    </div>
                                    <input type="file" name="image_file" class="w-full text-xs text-slate-400 mt-4 file:ml-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-white/10 file:text-white hover:file:bg-white/20 cursor-pointer"/>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] py-6 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] hover:shadow-[0_20px_40px_-15px_rgba(201,169,110,0.4)] transition-all hover:-translate-y-1 flex items-center justify-center gap-3 active:scale-95 duration-300">
                                <span class="material-symbols-outlined">auto_awesome</span>
                                تحديث البيانات الفاخرة
                            </button>
                            <p class="text-center text-[10px] text-slate-600 font-bold uppercase tracking-[0.3em] mt-6">سيتم حفظ جميع التغييرات في قاعدة البيانات فوراً</p>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
