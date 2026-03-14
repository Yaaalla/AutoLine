<?php 
require_once 'config/db_connect.php'; 
include 'includes/header.php'; 

// Fetch all cars that are available or reserved
$stmt = $pdo->query("SELECT c.*, MAX(b.return_date) as last_return_date 
                    FROM cars c 
                    LEFT JOIN bookings b ON c.id = b.car_id AND b.status = 'confirmed'
                    WHERE c.status IN ('available', 'reserved') 
                    GROUP BY c.id 
                    ORDER BY c.status ASC, c.created_at DESC");
$cars = $stmt->fetchAll();
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
                                <p class="text-lg font-black text-primary">$<?= number_format($car['price_per_day']) ?></p>
                                <p class="text-[10px] font-bold text-slate-500">لكل يوم</p>
                            </div>
                        </div>
                        <div class="my-4 grid grid-cols-3 gap-4 border-y border-primary/5 py-4">
                            <div class="flex flex-col items-center gap-1 text-center">
                                <span class="material-symbols-outlined text-primary/60 text-lg">settings</span>
                                <span class="text-[8px] font-bold uppercase text-slate-500"><?= $car['transmission'] == 'Automatic' ? 'أوتوماتيك' : 'يدوي' ?></span>
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
