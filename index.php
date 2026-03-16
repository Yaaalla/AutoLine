<?php 
require_once 'config/db_connect.php'; 
include 'includes/header.php'; 

// Fetch featured fleet
$stmt = $pdo->query("SELECT * FROM cars WHERE status = 'available' LIMIT 6");
$featured_cars = $stmt->fetchAll();

// Fetch latest blogs
$blog_stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 3");
$latest_blogs = $blog_stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="relative h-screen w-full flex flex-col items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0 bg-black">
        <div class="absolute inset-0 bg-black/60 z-10 w-full h-full"></div>
        <img alt="Auto Line Hero" class="w-full h-full object-cover opacity-80" src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&q=80"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-5xl mt-20">
        <h1 class="font-display text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-black text-white mb-6 drop-shadow-lg leading-tight">
            اوتو لاين لتاجير السيارات
        </h1>
        <h2 class="font-display text-2xl sm:text-3xl md:text-5xl text-primary mb-8 font-bold drop-shadow-md">
            أجر سيارتك
        </h2>
        <p class="text-white text-xl md:text-2xl max-w-2xl mx-auto mb-12 font-medium drop-shadow-md">
            توفر اوتو لاين مجموعة واسعة من السيارات
        </p>
        <a href="#about" class="bg-primary text-white px-10 py-4 rounded-xl font-bold text-xl hover:bg-white hover:text-slate-900 transition-all shadow-xl inline-block transform hover:-translate-y-1">
            تعرف على خدمتنا
        </a>
    </div>
</section>

<!-- Categories Section -->
<section class="py-24 px-6 lg:px-20 bg-slate-50 dark:bg-[#111]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">فئات السيارات</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">directions_car</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">السيارات الاقتصادية</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">مثالية للتنقل اليومي وتوفير الوقود.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">family_restroom</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">السيارات العائلية</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">توفر مساحة واسعة وراحة للعائلة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">landscape</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">سيارات الدفع الرباعي</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">مناسبة للمغامرات والطرق الوعرة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">diamond</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">السيارات الفاخرة</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">لمن يبحثون عن تجربة قيادة فاخرة وأنيقة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">speed</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">السيارات الرياضية</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">لعشاق السرعة والأداء العالي.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white">celebration</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-slate-800 dark:text-white">تأجير للمناسبات الخاصة</h3>
                <p class="text-slate-600 dark:text-slate-400 text-lg">أجير السيارات لحفلات الزفاف أو الاحتفالات الخاصة</p>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="py-24 px-6 lg:px-20 bg-white dark:bg-background-dark">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary font-bold mb-6">
                <span class="material-symbols-outlined text-sm">info</span>
                من نحن
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-8 leading-tight">ماذا تعرف عن اوتو لاين</h2>
            <div class="space-y-6 text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                <p>
                    مرحبًا بكم في اوتو لاين. شركة اوتو لاين هي واحدة من الشركات الرائدة في مجال تأجير السيارات، حيث نهدف إلى تقديم تجربة استثنائية لعملائنا من الأفراد والشركات. نحن نؤمن بتقديم حلول مبتكرة تلبي احتياجات عملائنا وتفوق توقعاتهم.
                </p>
                <p>
                    تُعتبر اوتو لاين شركة رائدة في مجال تأجير السيارات، تقدم خدماتها لتلبية احتياجات العملاء المتنوعة من خلال أسطول واسع من السيارات الحديثة والمجهزة بأحدث التقنيات. نحن ملتزمون بتوفير تجربة استئجار مريحة وسهلة من خلال نظام حجز إلكتروني بسيط وسريع، وفريق دعم متاح على مدار الساعة لضمان رضا العملاء بفضل أسعارنا التنافسية وخدماتنا الإضافية المميزة، نسعى لتوفير أفضل قيمة لعملائنا وجعل كل رحلة تجربة مميزة ومريحة.
                </p>
            </div>
            <div class="mt-10 flex gap-4">
                <div class="bg-slate-50 dark:bg-[#1a1a1a] p-4 rounded-xl border-r-4 border-primary shadow-sm">
                    <h4 class="text-3xl font-black text-slate-900 dark:text-white mb-1">+10</h4>
                    <span class="text-slate-500 font-medium">سنوات خبرة</span>
                </div>
                <div class="bg-slate-50 dark:bg-[#1a1a1a] p-4 rounded-xl border-r-4 border-primary shadow-sm">
                    <h4 class="text-3xl font-black text-slate-900 dark:text-white mb-1">+500</h4>
                    <span class="text-slate-500 font-medium">سيارة متاحة</span>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="rounded-3xl overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="About Auto Line" class="w-full h-full object-cover rounded-3xl transform hover:scale-105 transition-transform duration-700">
            </div>
            <div class="absolute -bottom-8 -right-8 bg-white dark:bg-surface p-6 rounded-2xl shadow-xl w-64 border border-gray-100 dark:border-white/5 hidden md:block">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-2xl">support_agent</span>
                    </div>
                    <div>
                        <h5 class="font-bold text-slate-900 dark:text-white">دعم متواصل</h5>
                        <p class="text-sm text-slate-500">على مدار الساعة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-24 px-6 lg:px-20 bg-slate-50 dark:bg-[#111]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">مميزات اوتو لاين</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
        <div class="grid md:grid-cols-3 gap-10">
            <div class="bg-white dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-full h-1 bg-gradient-to-l from-primary to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-24 h-24 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-8 transform rotate-3 group-hover:rotate-6 transition-transform">
                    <span class="material-symbols-outlined text-5xl text-primary">airline_seat_recline_extra</span>
                </div>
                <h3 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">راحة استثنائية</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                    في اوتو لاين، نضع راحة عملائنا في مقدمة أولوياتنا. نوفر سيارات حديثة ومجهزة بأحدث التقنيات لتضمن تجربة قيادة مريحة وآمنة. بالإضافة إلى ذلك، نقدم خيارات تأجير مرنة وخدمات إضافية مثل التوصيل والاستلام من مواقع مريحة، مما يجعل عملية استئجار السيارة سلسة وخالية من المتاعب.
                </p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-full h-1 bg-gradient-to-l from-primary to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-24 h-24 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-8 transform rotate-3 group-hover:rotate-6 transition-transform">
                    <span class="material-symbols-outlined text-5xl text-primary">timer</span>
                </div>
                <h3 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">وقت أقل</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                    في اوتو لاين نسعى لتوفير تجربة سريعة وسهلة لعملائنا. مع نظام الحجز الإلكتروني البسيط، يمكنك العثور على السيارة المناسبة لك في وقت قياسي. نعرض لك خيارات متنوعة ومفصلة لتختار من بينها بسهولة، مما يوفر عليك الوقت والجهد في البحث عن السيارة المثالية التلبية احتياجاتك.
                </p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-lg border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-full h-1 bg-gradient-to-l from-primary to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="w-24 h-24 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-8 transform rotate-3 group-hover:rotate-6 transition-transform">
                    <span class="material-symbols-outlined text-5xl text-primary">payments</span>
                </div>
                <h3 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">تكلفة تنافسية</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                    تقدم اوتو لاين أسعارا تنافسية تناسب جميع الميزانيات مع الحفاظ على جودة الخدمة. نوفر عروض وخصومات خاصة على فترات الإيجار الطويلة، بالإضافة إلى خدمات إضافية مثل التأمين الشامل وخدمة التوصيل لتعزيز راحتك وتوفير الوقت والجهد.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Rent a Car CTA Section -->
<section class="py-24 px-6 lg:px-20 bg-primary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-black/10 to-transparent"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-8 drop-shadow-md">استأجر سيارة</h2>
        <p class="text-white/90 text-xl leading-relaxed mb-12 font-medium drop-shadow">
            مع اوتو لاين، يمكنك استئجار السيارة المثالية بسهولة وسرعة. نقدم أسطولاً متنوعاً من السيارات الحديثة والمجهزة بأحدث التقنيات لتلبية احتياجاتك بفضل نظام الحجز الإلكتروني البسيط والدعم المتاح على مدار الساعة نضمن لك تجربة استئجار سلسة ومريحة مع أسعار تنافسية وخدمات إضافية لتعزيز راحتك.
        </p>
        <a href="vehicles_gallery.php" class="bg-white text-primary hover:bg-slate-900 hover:text-white px-12 py-5 rounded-xl font-black text-xl transition-all shadow-2xl inline-flex items-center gap-3 transform hover:-translate-y-1">
            <span>ابدأ الحجز الآن</span>
            <span class="material-symbols-outlined rtl:rotate-180">arrow_forward</span>
        </a>
    </div>
</section>

<!-- Fleet Showcase -->
<section id="fleet" class="py-24 px-6 lg:px-20 bg-white dark:bg-background-dark">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">سيارات للايجار</h2>
            <p class="text-slate-600 dark:text-slate-400 text-xl">اختار اللي يناسبك من سيارتنا افضل سيارات للايجار</p>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full mt-6"></div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($featured_cars as $car): ?>
            <div class="group bg-slate-50 dark:bg-surface rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:shadow-2xl dark:hover:shadow-primary/5">
                <div class="h-64 relative overflow-hidden bg-white dark:bg-[#1a1a1a] p-6 flex flex-col justify-between">
                    <div class="flex justify-between items-start w-full z-10 relative">
                        <?php if (isset($car['discount']) && $car['discount'] > 0): ?>
                            <div class="bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg">
                                خصم <?= $car['discount'] ?>%
                            </div>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                        
                        <div class="bg-white/80 dark:bg-black/50 backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 dark:text-white">
                            <?= htmlspecialchars($car['category'] ?? 'فاخرة') ?>
                        </div>
                    </div>
                    
                    <div class="absolute inset-0 flex items-center justify-center p-8">
                        <img alt="<?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?>" 
                            class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-700 drop-shadow-2xl" 
                            src="<?= htmlspecialchars($car['image_path']) ?>"/>
                    </div>
                </div>
                
                <div class="p-8">
                    <h3 class="text-slate-900 dark:text-white font-black text-2xl mb-2 text-center" dir="ltr"><?= htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']) ?></h3>
                    
                    <div class="flex justify-center gap-6 mb-8 pt-4 pb-8 border-b border-gray-200 dark:border-white/10">
                        <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-2xl text-primary">local_gas_station</span>
                            <span class="text-sm font-bold">بنزين</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-2xl text-primary">airline_seat_recline_extra</span>
                            <span class="text-sm font-bold"><?= $car['seats'] ?> مقاعد</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-2xl text-primary">speed</span>
                            <span class="text-sm font-bold">130 KM</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-2xl text-primary">settings</span>
                            <span class="text-sm font-bold"><?= $car['transmission'] == 'Auto' ? 'اتوماتيك' : 'يدوي' ?></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-end mb-8">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">السعر / يوم</span>
                        <div class="text-left" dir="ltr">
                            <?php if (isset($car['discount']) && $car['discount'] > 0): ?>
                                <?php $discounted_price = $car['price_per_day'] * (1 - ($car['discount'] / 100)); ?>
                                <span class="text-slate-400 line-through text-sm block font-medium"><?= number_format($car['price_per_day']) ?> EGP</span>
                                <span class="text-slate-900 dark:text-white font-black text-2xl"><?= number_format($discounted_price) ?> <span class="text-primary text-xl">EGP</span></span>
                            <?php else: ?>
                                <span class="text-slate-900 dark:text-white font-black text-2xl"><?= number_format($car['price_per_day']) ?> <span class="text-primary text-xl">EGP</span></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <a href="booking_flow.php?car_id=<?= $car['id'] ?>" class="block w-full bg-slate-100 dark:bg-[#1a1a1a] text-slate-900 dark:text-white hover:bg-primary hover:text-white dark:hover:bg-primary py-4 rounded-xl text-xl font-bold transition-all text-center shadow-sm">
                        احجز الان
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-16">
            <a href="vehicles_gallery.php" class="inline-flex items-center gap-3 border-2 border-primary text-primary hover:bg-primary hover:text-white px-10 py-4 rounded-xl font-bold text-lg transition-all">
                <span>عرض جميع السيارات</span>
                <span class="material-symbols-outlined rtl:rotate-180">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<!-- Blog / Articles Section -->
<section id="blog" class="py-24 px-6 lg:px-20 bg-slate-50 dark:bg-[#111]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">احدث المقالات لدينا</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-10">
            <?php if (empty($latest_blogs)): ?>
                <div class="col-span-3 text-center py-12 text-slate-500">لا توجد مقالات منشورة بعد.</div>
            <?php else: ?>
                <?php foreach ($latest_blogs as $blog_post): ?>
                <article class="bg-white dark:bg-[#1a1a1a] rounded-3xl overflow-hidden shadow-lg border border-gray-100 dark:border-white/5 group transform hover:-translate-y-2 transition-all duration-300">
                    <div class="h-64 overflow-hidden relative">
                        <img src="<?= htmlspecialchars($blog_post['image_path'] ?? 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&q=80') ?>" alt="<?= htmlspecialchars($blog_post['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 right-4 bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg z-10">
                            تأجير سيارات
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4 font-medium">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">calendar_month</span> <?= date('M d, Y', strtotime($blog_post['created_at'])) ?></span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">person</span> <?= htmlspecialchars($blog_post['author']) ?></span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 hover:text-primary transition-colors leading-snug">
                            <a href="blog_details.php?id=<?= $blog_post['id'] ?>"><?= htmlspecialchars($blog_post['title']) ?></a>
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 line-clamp-3 text-lg">
                            <?= htmlspecialchars($blog_post['excerpt']) ?>
                        </p>
                        <a href="blog_details.php?id=<?= $blog_post['id'] ?>" class="inline-flex items-center gap-2 text-primary font-bold hover:text-slate-900 dark:hover:text-white transition-colors group-hover:gap-3">
                            اقرأ المزيد 
                            <span class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm rtl:rotate-180">arrow_forward</span>
                            </span>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($latest_blogs)): ?>
        <div class="text-center mt-12">
            <a href="blog.php" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl border border-primary text-primary hover:bg-primary hover:text-[#12110f] transition-all font-bold">
                عرض المزيد من المقالات
                <span class="material-symbols-outlined rtl:rotate-180">arrow_forward</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact CTA -->
<section id="contact" class="py-24 px-6 lg:px-20 bg-primary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <h2 class="text-4xl font-black text-slate-900 mb-10">للحجز تقدر تتواصل معنا على</h2>
        <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
            <a href="tel:+201003412321" class="flex items-center justify-center gap-4 bg-white text-slate-900 px-10 py-5 rounded-2xl hover:bg-slate-900 hover:text-white transition-all text-2xl font-black shadow-xl" dir="ltr">
                <div class="p-2 bg-primary/20 rounded-full">
                    <span class="material-symbols-outlined text-2xl">call</span>
                </div>
                +20 100 341 2321
            </a>
            <a href="tel:+201154144465" class="flex items-center justify-center gap-4 bg-white text-slate-900 px-10 py-5 rounded-2xl hover:bg-slate-900 hover:text-white transition-all text-2xl font-black shadow-xl" dir="ltr">
                <div class="p-2 bg-primary/20 rounded-full">
                    <span class="material-symbols-outlined text-2xl">call</span>
                </div>
                +20 115 414 4465
            </a>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="py-24 px-6 lg:px-20 bg-white dark:bg-background-dark">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h4 class="text-primary font-bold mb-3 text-lg tracking-wider">تقيمتنا</h4>
            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">ماذا يقول عملاؤنا</h2>
            <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-slate-50 dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-sm border border-gray-100 dark:border-white/5 relative">
                <span class="material-symbols-outlined text-6xl text-primary/10 absolute top-6 right-6 font-serif">format_quote</span>
                <div class="flex text-amber-500 mb-6 relative z-10">
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 italic mb-8 text-lg leading-relaxed relative z-10">"تجربة رائعة مع اوتو لاين. السيارات نظيفة جداً والخدمة سريعة وممتازة. أنصح الجميع بالتعامل معهم."</p>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-white/10">
                    <div class="w-14 h-14 bg-gray-200 rounded-full overflow-hidden border-2 border-primary">
                        <img src="https://ui-avatars.com/api/?name=Ahmed+Hassan&background=c9a96e&color=fff" alt="Ahmed" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-lg">أحمد حسن</h4>
                        <p class="text-sm text-slate-500">عميل مميز</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-sm border border-gray-100 dark:border-white/5 relative">
                <span class="material-symbols-outlined text-6xl text-primary/10 absolute top-6 right-6 font-serif">format_quote</span>
                <div class="flex text-amber-500 mb-6 relative z-10">
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 italic mb-8 text-lg leading-relaxed relative z-10">"أفضل شركة تأجير تعاملت معها، احترام للمواعيد وأسعار تنافسية. شكراً لفريق العمل."</p>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-white/10">
                    <div class="w-14 h-14 bg-gray-200 rounded-full overflow-hidden border-2 border-primary">
                        <img src="https://ui-avatars.com/api/?name=Sara+Ali&background=c9a96e&color=fff" alt="Sara" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-lg">سارة علي</h4>
                        <p class="text-sm text-slate-500">عميل</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-[#1a1a1a] p-10 rounded-3xl shadow-sm border border-gray-100 dark:border-white/5 relative md:hidden lg:block">
                <span class="material-symbols-outlined text-6xl text-primary/10 absolute top-6 right-6 font-serif">format_quote</span>
                <div class="flex text-amber-500 mb-6 relative z-10">
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                    <span class="material-symbols-outlined fill-current text-2xl">star</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 italic mb-8 text-lg leading-relaxed relative z-10">"السيارة كانت حديثة واستلام وتسليم السيارة كان سلساً جداً. تجربة مريحة وسأكررها بالتأكيد."</p>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-white/10">
                    <div class="w-14 h-14 bg-gray-200 rounded-full overflow-hidden border-2 border-primary">
                        <img src="https://ui-avatars.com/api/?name=Mohamed+Omar&background=c9a96e&color=fff" alt="Mohamed" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-lg">محمد عمر</h4>
                        <p class="text-sm text-slate-500">عميل منتظم</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
