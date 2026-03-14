<?php 
require_once 'config/db_connect.php'; 
include 'includes/header.php'; 

// Fetch featured fleet (limit to 4 for showcase)
$stmt = $pdo->query("SELECT * FROM cars WHERE status = 'available' LIMIT 4");
$featured_cars = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="relative h-screen w-full flex flex-col items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 hero-gradient z-10"></div>
        <img alt="Luxury Car Hero" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7HcLpgfaE59uEG7K2C1TXDq16kT-TJrRGCaGVnoWbxxdpwjH8O2bE7ucJA71NQMcnulweRX_IsDkA7DB9qba0BVlxI6LOX54VzaeD8oR4meyR6CCoyWEtwiAoowGS5DjC_sSUbbeVb3LenV17qLY9EEyzq9-EUMO9gaU3gU4ejtjm9f5lRZSQkfqX2L8rQNeQRpL3wvzPCgFVb8vfEV618ANjj3ONf02d7c2RiaqpvT52rZbC-z_TH14KEOs4DJ82EU_f0W-26O6j"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-5xl">
        <div class="glass inline-block px-6 py-2 rounded-full mb-8">
            <span class="text-primary font-serif text-xl tracking-wide">تبدأ الأسعار من $400 / يوم</span>
        </div>
        <h1 class="font-serif text-6xl md:text-8xl lg:text-[80px] leading-tight text-white mb-8">
            قد السيارة <br/> <span class="italic text-primary">الاستثنائية</span>
        </h1>
        <p class="text-slate-300 text-lg max-w-2xl mx-auto mb-10 font-light tracking-wide uppercase">
            مجموعة مختارة من أرقى السيارات العالمية للمسافرين المتميزين.
        </p>
    </div>
</section>

<!-- Fleet Showcase -->
<section class="py-24 px-6 lg:px-20 bg-background-dark">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="font-serif text-5xl text-white mb-4">أسطولنا النخبة</h2>
                <p class="text-slate-400 uppercase tracking-widest text-xs font-medium">فخامة منسقة لكل رحلة</p>
            </div>
            <a href="vehicles_gallery.php" class="text-primary hover:underline font-bold uppercase tracking-widest text-xs">عرض كافة الأسطول</a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($featured_cars as $car): ?>
            <div class="group bg-surface rounded-2xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all">
                <div class="h-48 relative overflow-hidden">
                    <img alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                         src="<?= htmlspecialchars($car['image_path']) ?>"/>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-white font-serif text-xl"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h3>
                        <span class="text-primary font-serif text-lg">$<?= number_format($car['price_per_day']) ?></span>
                    </div>
                    <div class="flex gap-4 mb-6">
                        <div class="flex items-center gap-1 text-[10px] text-slate-400 uppercase tracking-widest">
                            <span class="material-symbols-outlined text-sm">airline_seat_recline_extra</span> <?= $car['seats'] ?> مقاعد
                        </div>
                        <div class="flex items-center gap-1 text-[10px] text-slate-400 uppercase tracking-widest">
                            <span class="material-symbols-outlined text-sm">settings</span> <?= $car['transmission'] == 'Automatic' ? 'أوتوماتيك' : 'يدوي' ?>
                        </div>
                    </div>
                    <a href="car_details.php?id=<?= $car['id'] ?>" class="block w-full border border-primary text-primary hover:bg-primary hover:text-background-dark py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-center">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
