<?php 
require_once 'config/db_connect.php'; 
include 'includes/header.php'; 

// Search and Filter Logic
$brand_filter = $_GET['brand'] ?? "";
$color_filter = $_GET['color'] ?? "";
$max_price = $_GET['max_price'] ?? "";

$query = "SELECT c.*, MAX(b.return_date) as last_return_date 
          FROM cars c 
          LEFT JOIN bookings b ON c.id = b.car_id AND b.status = 'confirmed'
          WHERE c.status IN ('available', 'reserved')";
$params = [];

if (!empty($brand_filter)) {
    $query .= " AND c.brand = ?";
    $params[] = $brand_filter;
}

if (!empty($color_filter)) {
    $query .= " AND c.color = ?";
    $params[] = $color_filter;
}


if (!empty($max_price)) {
    $query .= " AND c.price_per_day <= ?";
    $params[] = $max_price;
}

$query .= " GROUP BY c.id ORDER BY c.status ASC, c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cars = $stmt->fetchAll();

// Fetch dynamic options for filters
$brands = $pdo->query("SELECT DISTINCT brand FROM cars ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
$colors = $pdo->query("SELECT DISTINCT color FROM cars WHERE color IS NOT NULL AND color != '' ORDER BY color")->fetchAll(PDO::FETCH_COLUMN);
?>

<main class="pt-24 min-h-screen">
    <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-background-dark to-transparent z-10"></div>
        <div class="container mx-auto relative z-20 h-full flex flex-col justify-center px-6 md:px-20">
            <h2 class="text-4xl font-extrabold text-white">أسطولنا</h2>
            <p class="text-primary font-medium mt-2">اكتشف مجموعتنا من السيارات الفاخرة</p>
        </div>
    </div>

    <div class="container mx-auto px-6 md:px-20 py-12">
        <!-- Filter Section -->
        <div class="mb-12 glass p-8 rounded-2xl border border-primary/10">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                <!-- Brand Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-primary uppercase tracking-widest">الماركة</label>
                    <select name="brand" class="w-full bg-background-dark/50 border border-primary/20 rounded-lg px-4 py-2.5 text-sm text-white focus:border-primary focus:outline-none appearance-none cursor-pointer">
                        <option value="">كل الماركات</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= htmlspecialchars($brand) ?>" <?= $brand_filter == $brand ? 'selected' : '' ?>><?= htmlspecialchars($brand) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Color Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-primary uppercase tracking-widest">اللون</label>
                    <select name="color" class="w-full bg-background-dark/50 border border-primary/20 rounded-lg px-4 py-2.5 text-sm text-white focus:border-primary focus:outline-none appearance-none cursor-pointer">
                        <option value="">كل الألوان</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= htmlspecialchars($color) ?>" <?= $color_filter == $color ? 'selected' : '' ?>><?= htmlspecialchars($color) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <!-- Price Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-primary uppercase tracking-widest">السعر (حد أقصى)</label>
                    <input type="number" name="max_price" value="<?= htmlspecialchars($max_price) ?>" placeholder="مثال: 1000" class="w-full bg-background-dark/50 border border-primary/20 rounded-lg px-4 py-2.5 text-sm text-white focus:border-primary focus:outline-none"/>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-primary text-background-dark font-black py-2.5 rounded-lg hover:bg-primary/90 transition-all text-sm uppercase tracking-widest">بحث</button>
                    <?php if ($brand_filter || $color_filter || $year_filter || $max_price): ?>
                        <a href="vehicles_gallery.php" class="bg-white/5 text-primary border border-primary/20 p-2.5 rounded-lg hover:bg-white/10 transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">refresh</span>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (count($cars) > 0): ?>
                <?php foreach ($cars as $car): ?>
                <div class="group relative flex flex-col overflow-hidden rounded-xl border border-primary/10 bg-background-dark/40 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300">
                    <div class="relative h-56 overflow-hidden">
                        <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                             src="<?= htmlspecialchars($car['image_path']) ?>"/>
                        <div class="absolute top-4 left-4 z-20">
                            <?php if ($car['status'] == 'reserved' && !empty($car['last_return_date'])): ?>
                                <span class="bg-amber-500/90 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded shadow-lg">محجوزة حتى <?= date('d M', strtotime($car['last_return_date'])) ?></span>
                            <?php else: ?>
                                <span class="bg-emerald-500/90 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded shadow-lg">متاحة الآن</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-bold text-primary uppercase tracking-widest"><?= htmlspecialchars($car['brand']) ?></p>
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($car['model']) ?></h3>
                            </div>
                            <div class="text-right">
                                <?php if (isset($car['discount']) && $car['discount'] > 0): ?>
                                    <?php $discounted_price = $car['price_per_day'] * (1 - ($car['discount'] / 100)); ?>
                                    <p class="text-xs font-bold text-slate-500 line-through"><?= number_format($car['price_per_day']) ?> ج.م</p>
                                    <p class="text-lg font-black text-primary"><?= number_format($discounted_price) ?> ج.م</p>
                                <?php else: ?>
                                    <p class="text-lg font-black text-primary"><?= number_format($car['price_per_day']) ?> ج.م</p>
                                <?php endif; ?>
                                <p class="text-[10px] font-bold text-slate-500">لكل يوم</p>
                            </div>
                        </div>
                        <div class="my-4 grid grid-cols-3 gap-4 border-y border-primary/5 py-4">
                            <div class="flex flex-col items-center gap-1 text-center">
                                <span class="material-symbols-outlined text-primary/60 text-lg">settings</span>
                                <span class="text-[8px] font-bold uppercase text-slate-500"><?= $car['transmission'] == 'Auto' ? 'أوتوماتيك' : 'يدوي' ?></span>
                            </div>
                            <div class="flex flex-col items-center gap-1 text-center">
                                <span class="material-symbols-outlined text-primary/60 text-lg">local_gas_station</span>
                                <span class="text-[8px] font-bold uppercase text-slate-500"><?= $car['fuel_type'] == 'Petrol' ? 'بنزين' : ($car['fuel_type'] == 'Diesel' ? 'ديزل' : $car['fuel_type']) ?></span>
                            </div>
                            <div class="flex flex-col items-center gap-1 text-center">
                                <span class="material-symbols-outlined text-primary/60 text-lg">airline_seat_recline_extra</span>
                                <span class="text-[8px] font-bold uppercase text-slate-500"><?= $car['seats'] ?> مقاعد</span>
                            </div>
                        </div>
                        <a href="car_details.php?id=<?= $car['id'] ?>" class="block mt-auto w-full rounded-lg bg-primary py-3.5 text-sm font-black uppercase tracking-widest text-background-dark transition-all hover:bg-primary/90 active:scale-95 text-center">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20">
                    <p class="text-slate-500 uppercase tracking-widest">لم يتم العثور على سيارات في معرضنا.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
