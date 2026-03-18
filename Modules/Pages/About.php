<?php
/**
 * AutoLine - About Page
 * 
 * صفحة من نحن - تعريف بالشركة وخدماتها
 * 
 * @package AutoLine\Modules\Pages
 */

// Set page variables
$page_title = "من نحن";
$meta_desc = "تعرف على اوتو لاين - شركة رائدة في مجال تأجير السيارات في مصر تقدم خدمات متميزة للأفراد والشركات";

require_once __DIR__ . '/../../Includes/Layout/Header.php';

use AutoLine\Core\Config;
?>

<!-- Hero Section for About -->
<section class="relative pt-32 pb-20 w-full flex flex-col items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/70 z-10 w-full h-full"></div>
        <img alt="About Auto Line" class="w-full h-full object-cover opacity-60" src="https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&q=80"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-4xl mt-10">
        <h1 class="font-display text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-lg">
            مرحبًا بكم في اوتو لاين
        </h1>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
    </div>
</section>

<!-- Introduction -->
<section class="py-20 px-6 lg:px-20 bg-white dark:bg-background-dark">
    <div class="max-w-4xl mx-auto text-center">
        <p class="text-xl md:text-2xl text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
            شركة اوتو لاين هي واحدة من الشركات الرائدة في مجال تأجير السيارات، حيث نهدف إلى تقديم تجربة استثنائية لعملائنا من الأفراد والشركات. نحن نؤمن بتقديم حلول مبتكرة تلبي احتياجات عملائنا وتفوق توقعاتهم.
        </p>
    </div>
</section>

<!-- Services -->
<section class="py-20 px-6 lg:px-20 bg-slate-50 dark:bg-[#111]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">خدماتنا</h2>
            <p class="text-lg text-slate-600 dark:text-slate-400">مجموعة متنوعة من السيارات توفر اوتو لاين مجموعة واسعة من السيارات تشمل:</p>
            <div class="w-16 h-1 bg-primary mx-auto rounded-full mt-6"></div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1">
                <span class="material-symbols-outlined text-4xl text-primary mb-4">directions_car</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">السيارات الاقتصادية</h3>
                <p class="text-slate-600 dark:text-slate-400">مثالية للتنقل اليومي وتوفير الوقود.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1">
                <span class="material-symbols-outlined text-4xl text-primary mb-4">family_restroom</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">السيارات العائلية</h3>
                <p class="text-slate-600 dark:text-slate-400">توفر مساحة واسعة وراحة للعائلة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1">
                <span class="material-symbols-outlined text-4xl text-primary mb-4">landscape</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">سيارات الدفع الرباعي</h3>
                <p class="text-slate-600 dark:text-slate-400">مناسبة للمغامرات والطرق الوعرة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1">
                <span class="material-symbols-outlined text-4xl text-primary mb-4">diamond</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">السيارات الفاخرة</h3>
                <p class="text-slate-600 dark:text-slate-400">لمن يبحثون عن تجربة قيادة فاخرة وأنيقة.</p>
            </div>
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 hover:border-primary/50 transition-all hover:-translate-y-1">
                <span class="material-symbols-outlined text-4xl text-primary mb-4">speed</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">السيارات الرياضية</h3>
                <p class="text-slate-600 dark:text-slate-400">لعشاق السرعة والأداء العالي.</p>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission & Values -->
<section class="py-20 px-6 lg:px-20 bg-white dark:bg-background-dark">
    <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-3 gap-10">
            <!-- Vision -->
            <div class="bg-slate-50 dark:bg-surface p-10 rounded-3xl relative overflow-hidden group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">visibility</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">رؤيتنا</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    أن نكون الخيار الأول في تقديم حلول المواصلات المتميزة والمتنوعة في الشرق الأوسط وأفريقيا، مع التركيز على تقديم أفضل التجارب للعملاء من خلال الحفاظ على أعلى معايير الجودة والتميز.
                </p>
            </div>
            
            <!-- Mission -->
            <div class="bg-slate-50 dark:bg-surface p-10 rounded-3xl relative overflow-hidden group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">rocket_launch</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">رسالتنا</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    رسالتنا تكمن في اتباع أعلى معايير الأعمال الأخلاقية من خلال حوكمة فعالة وشفافة مع الالتزام دائمًا بالوفاء بوعودنا للمساهمين والعملاء. نحن نهدف إلى تقديم خدمات تأجير سيارات تتميز بالجودة والاحترافية، مع الحفاظ على رضا العملاء كشعار أساسي لنا.
                </p>
            </div>
            
            <!-- Values -->
            <div class="bg-slate-50 dark:bg-surface p-10 rounded-3xl relative overflow-hidden group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">workspace_premium</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">قيمنا</h3>
                <ul class="space-y-4 text-slate-600 dark:text-slate-400">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                        <span><strong>العميل أولًا ودائمًا:</strong> نضع احتياجات عملائنا في مقدمة أولوياتنا، ونحرص على فهم متطلباتهم وتقديم حلول تتناسب معهم بشكل مثالي.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                        <span><strong>الجودة والاحترافية:</strong> نلتزم بتقديم أعلى مستويات الجودة في كل ما نقدمه.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 px-6 lg:px-20 bg-primary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">لماذا تختار اوتو لاين ؟</h2>
            <div class="w-24 h-1 bg-white mx-auto rounded-full"></div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">category</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">تنوع السيارات</h3>
                <p class="text-white/80">نحرص على تقديم مجموعة متنوعة من السيارات لتناسب جميع الاحتياجات.</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">event_available</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">المرونة في الخيارات</h3>
                <p class="text-white/80">سواء كنت بحاجة لتأجير سيارة ليوم واحد أو لفترة طويلة، فنحن هنا لتلبية احتياجاتك.</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">verified</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">الجودة والموثوقية</h3>
                <p class="text-white/80">جميع سياراتنا تتميز بالجودة العالية وتخضع لصيانة دورية لضمان الأمان والراحة.</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl border border-white/20 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">support_agent</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">دعم العملاء المستمر</h3>
                <p class="text-white/80">فريق الدعم لدينا متاح على مدار الساعة لتقديم المساعدة والإجابة على جميع استفساراتك، مما يضمن لك تجربة تأجير سلسة وخالية من المتاعب.</p>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../../Includes/Layout/Footer.php';
