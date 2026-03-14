<?php 
require_once 'config/db_connect.php'; 
include 'includes/header.php'; 

$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT c.*, MAX(b.return_date) as last_return_date 
                      FROM cars c 
                      LEFT JOIN bookings b ON c.id = b.car_id AND b.status = 'confirmed'
                      WHERE c.id = ?
                      GROUP BY c.id");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: vehicles_gallery.php");
    exit;
}

// Fetch 4 other cars for "You Might Also Like"
$other_stmt = $pdo->prepare("SELECT * FROM cars WHERE id != ? AND status = 'available' LIMIT 4");
$other_stmt->execute([$car_id]);
$other_cars = $other_stmt->fetchAll();
?>

<main class="min-h-screen bg-background-light dark:bg-background-dark pt-20">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left: Gallery -->
            <div class="space-y-6">
                <div class="aspect-[16/10] overflow-hidden rounded-2xl border border-primary/10 shadow-2xl">
                    <img id="main-image" class="w-full h-full object-cover" src="<?= htmlspecialchars($car['image_path']) ?>"/>
                </div>
                <?php 
                $stmt_img = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ?");
                $stmt_img->execute([$car_id]);
                $gallery = $stmt_img->fetchAll();
                ?>
                <div class="grid grid-cols-4 gap-4">
                    <div class="aspect-square rounded-lg border-2 border-primary overflow-hidden cursor-pointer thumb">
                        <img class="w-full h-full object-cover" src="<?= htmlspecialchars($car['image_path']) ?>" onclick="document.getElementById('main-image').src=this.src"/>
                    </div>
                    <?php foreach ($gallery as $img): ?>
                        <div class="aspect-square rounded-lg border border-white/5 overflow-hidden cursor-pointer hover:border-primary transition-all thumb">
                            <img class="w-full h-full object-cover" src="<?= htmlspecialchars($img['image_path']) ?>" onclick="document.getElementById('main-image').src=this.src"/>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Details -->
            <div class="flex flex-col">
                <nav class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">
                    <a href="index.php">الرئيسية</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_left</span>
                    <a href="vehicles_gallery.php">الأسطول</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_left</span>
                    <span class="text-primary"><?= htmlspecialchars($car['brand']) ?></span>
                </nav>

                <h2 class="text-5xl font-black uppercase tracking-tight mb-2"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h2>
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex items-center gap-1 text-primary">
                        <span class="material-symbols-outlined text-sm">star</span>
                        <span class="font-bold">4.9</span>
                        <span class="text-slate-500 font-medium text-xs">(128 تقييم)</span>
                    </div>
                    <div class="h-4 w-px bg-slate-300"></div>
                    <?php if ($car['status'] == 'reserved' && !empty($car['last_return_date'])): ?>
                        <span class="text-amber-500 text-xs font-black uppercase tracking-widest bg-amber-50 dark:bg-amber-500/10 px-3 py-1 rounded">محجوزة حتى <?= date('d M', strtotime($car['last_return_date'])) ?></span>
                    <?php else: ?>
                        <span class="text-emerald-500 text-xs font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded">متاحة الآن</span>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-3 gap-6 mb-10 py-8 border-y border-slate-200 dark:border-primary/5">
                    <div class="text-center group">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2 group-hover:scale-110 transition-transform">settings</span>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">ناقل الحركة</p>
                        <p class="text-sm font-black uppercase"><?= $car['transmission'] == 'Automatic' ? 'أوتوماتيك' : 'يدوي' ?></p>
                    </div>
                    <div class="text-center group">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2 group-hover:scale-110 transition-transform">airline_seat_recline_extra</span>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">المقاعد</p>
                        <p class="text-sm font-black uppercase"><?= $car['seats'] ?> أشخاص</p>
                    </div>
                    <div class="text-center group">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2 group-hover:scale-110 transition-transform">local_gas_station</span>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">الوقود</p>
                        <p class="text-sm font-black uppercase"><?= $car['fuel_type'] == 'Petrol' ? 'بنزين' : ($car['fuel_type'] == 'Diesel' ? 'ديزل' : $car['fuel_type']) ?></p>
                    </div>
                </div>

                <!-- Price & CTA -->
                <div class="mt-auto bg-slate-50 dark:bg-[#1c1a17] p-8 rounded-3xl border border-primary/10">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">السعر لكل يوم</p>
                            <p class="text-4xl font-black">$<?= number_format($car['price_per_day']) ?></p>
                        </div>
                    </div>
                    <a href="booking_flow.php?car_id=<?= $car['id'] ?>" class="w-full bg-primary hover:bg-primary/90 text-background-dark py-4 rounded-xl font-black uppercase tracking-widest transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 mt-4 text-center">
                        احجز هذه السيارة
                        <span class="material-symbols-outlined rotate-180">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- You Might Also Like -->
        <section class="mt-20">
            <h3 class="text-2xl font-extrabold uppercase tracking-tight mb-8">قد يعجبك أيضاً</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($other_cars as $other): ?>
                <div class="group bg-slate-50 dark:bg-[#2a241c] rounded-xl border border-primary/10 hover:border-primary/40 transition-all overflow-hidden">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform" src="<?= htmlspecialchars($other['image_path']) ?>"/>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-sm"><?= htmlspecialchars($other['brand'] . ' ' . $other['model']) ?></h4>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-primary font-bold text-sm">$<?= number_format($other['price_per_day']) ?></span>
                            <a href="car_details.php?id=<?= $other['id'] ?>" class="text-[10px] font-bold uppercase text-slate-500 hover:text-primary underline">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
