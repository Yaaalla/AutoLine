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
        $stmt = $pdo->prepare("INSERT INTO cars (brand, model, price_per_day, seats, transmission, fuel_type, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $model, $price, $seats, $transmission, $fuel, $image_path]);
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

$query .= " GROUP BY c.id ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة الأسطول | أوتو لوكس</title>
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
                    <!-- Toolbar & Search -->
                    <div class="glass-card p-8 rounded-[2.5rem] flex flex-wrap gap-6 items-center justify-between border border-white/5">
                        <form method="GET" class="flex items-center gap-6 flex-1 max-w-2xl">
                            <div class="relative flex-1">
                                <span class="material-symbols-outlined absolute right-5 top-1/2 -translate-y-1/2 text-slate-500 text-lg">search</span>
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="بحث عن موديل أو ماركة في الأسطول..." class="w-full bg-[#12110f]/60 border border-white/5 rounded-2xl pr-14 pl-6 py-4 text-sm text-white focus:border-[#c9a96e]/50 focus:outline-none focus:ring-4 focus:ring-[#c9a96e]/5 transition-all outline-none"/>
                            </div>
                            <button type="submit" class="p-4 bg-white/5 hover:bg-[#c9a96e] hover:text-[#12110f] rounded-2xl transition-all group">
                                <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">tune</span>
                            </button>
                        </form>
                        <?php if (!empty($search) || !empty($max_price)): ?>
                            <a href="manage_cars.php" class="text-[10px] font-black text-[#c9a96e] hover:text-white uppercase tracking-[0.2em] flex items-center gap-2 transition-colors">
                                <span class="material-symbols-outlined text-sm">restart_alt</span>
                                إعادة تعيين البحث
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Add Car Form (Full Width) -->
                    <div class="glass-card p-10 rounded-[3rem] border border-white/5 relative overflow-hidden group">
                        <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#c9a96e]/5 blur-[100px] rounded-full opacity-50 group-hover:opacity-100 transition-opacity duration-1000"></div>
                        
                        <div class="flex flex-col xl:flex-row items-start xl:items-center gap-8 relative z-10">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-2xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] mb-2">
                                    <span class="material-symbols-outlined text-3xl">add_box</span>
                                </div>
                                <h3 class="font-black text-xl text-white">إضافة سيارة</h3>
                                <p class="text-slate-500 text-[10px] uppercase font-bold tracking-widest mt-1">توسيع الأسطول الملكي</p>
                            </div>

                            <form method="POST" enctype="multipart/form-data" class="flex-1 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6 items-end">
                                <input type="hidden" name="add_car" value="1">
                                
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">الماركة</label>
                                    <input type="text" name="brand" placeholder="BMW" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">الموديل</label>
                                    <input type="text" name="model" placeholder="M8 Competition" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">السعر اليومي ($)</label>
                                    <input type="number" name="price" placeholder="450" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-[#c9a96e] font-black focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">الوقود</label>
                                    <input type="text" name="fuel_type" placeholder="بنزين 98" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">المقاعد</label>
                                    <input type="number" name="seats" placeholder="5" required class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mr-1">ناقل الحركة</label>
                                    <select name="transmission" class="w-full input-premium rounded-xl px-5 py-3.5 text-sm text-slate-100 focus:outline-none appearance-none cursor-pointer">
                                        <option value="Automatic">أوتوماتيك</option>
                                        <option value="Manual">يدوي</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:shadow-[0_10px_30px_-10px_rgba(201,169,110,0.4)] transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 active:scale-95">
                                    <span class="material-symbols-outlined text-lg">hotel_class</span>
                                    إدراج السيارة
                                </button>

                                <!-- File Uploads (Hidden but functional or more compact) -->
                                <div class="md:col-span-2 lg:col-span-4 xl:col-span-6 flex flex-wrap gap-4 mt-2">
                                    <div class="flex-1 min-w-[200px] relative">
                                        <input type="file" name="image_file" id="main_image" class="hidden" required/>
                                        <label for="main_image" class="w-full flex items-center justify-center gap-3 px-6 py-3 bg-white/[0.03] border border-dashed border-white/10 rounded-xl cursor-pointer hover:bg-white/[0.05] transition-all text-[10px] font-bold text-slate-400">
                                            <span class="material-symbols-outlined text-sm">upload_file</span>
                                            الصورة الأساسية (مطلوب)
                                        </label>
                                    </div>
                                    <div class="flex-1 min-w-[200px] relative">
                                        <input type="file" name="additional_images[]" id="gallery_images" multiple class="hidden"/>
                                        <label for="gallery_images" class="w-full flex items-center justify-center gap-3 px-6 py-3 bg-white/[0.03] border border-dashed border-white/10 rounded-xl cursor-pointer hover:bg-white/[0.05] transition-all text-[10px] font-bold text-slate-400">
                                            <span class="material-symbols-outlined text-sm">add_photo_alternate</span>
                                            صور المعرض (اختياري)
                                        </label>
                                    </div>
                                    <div class="flex-[2] min-w-[300px]">
                                        <input type="text" name="image_url" placeholder="أو أضف رابط الصورة مباشرة..." class="w-full bg-[#12110f]/40 border border-white/5 rounded-xl px-5 py-3 text-[10px] text-slate-400 focus:outline-none focus:border-[#c9a96e]/30"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Car List (Full Width Grid) -->
                    <div class="flex flex-col gap-8">
                        <?php if (empty($cars)): ?>
                            <div class="glass-card p-24 rounded-[4rem] text-center border-dashed border-2 border-white/5">
                                <div class="w-24 h-24 rounded-[3rem] bg-white/[0.02] flex items-center justify-center mx-auto mb-8">
                                    <span class="material-symbols-outlined text-5xl text-slate-700">inventory_2</span>
                                </div>
                                <h4 class="text-2xl font-black text-slate-500">لا توجد نتائج مطابقة لمهام البحث</h4>
                                <p class="text-xs text-slate-700 mt-3 uppercase tracking-[0.3em] font-black">حاول استخدام كلمات مفتاحية أخرى</p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
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
                                                <span class="text-3xl font-black text-white">$<?= number_format($car['price_per_day']) ?></span>
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
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter group-hover:text-white"><?= $car['transmission'] == 'Automatic' ? 'أوتو' : 'يدوي' ?></span>
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
</body>
</html>
