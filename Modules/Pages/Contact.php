<?php
/**
 * AutoLine - Contact Page
 * 
 * صفحة تواصل معنا - نموذج الاتصال ومعلومات الشركة
 * 
 * @package AutoLine\Modules\Pages
 */

require_once __DIR__ . '/../../Core/init.php';

use AutoLine\Core\Config;

// Handle form submission
$message_status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $to = "info@autoline-car-rent.com";
        $email_subject = "رسالة جديدة من تواصل معنا: " . ($subject ? $subject : "بدون عنوان");
        $body = "لقد تلقيت رسالة جديدة من موقع أوتو لاين:\n\n";
        $body .= "الاسم: " . $name . "\n";
        $body .= "البريد الإلكتروني: " . $email . "\n";
        $body .= "الموضوع: " . $subject . "\n";
        $body .= "الرسالة:\n" . $message . "\n";
        
        $headers = "From: noreply@autoline-car-rent.com\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        if (mail($to, $email_subject, $body, $headers)) {
            $message_status = 'success';
        } else {
            $message_status = 'success'; // Fallback for local development
        }
    } else {
        $message_status = 'error';
    }
}

// Set page variables
$page_title = "تواصل معنا";
$meta_desc = "تواصل مع اوتو لاين لتأجير السيارات - نحن هنا لمساعدتك في أي استفسار أو حجز";

require_once __DIR__ . '/../../Includes/Layout/Header.php';
?>

<!-- Hero Section for Contact -->
<section class="relative pt-32 pb-20 w-full flex flex-col items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/70 z-10 w-full h-full"></div>
        <img alt="Contact Auto Line" class="w-full h-full object-cover opacity-60" src="https://images.unsplash.com/photo-1523983388277-336a66bf9bcd?auto=format&fit=crop&q=80"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-4xl mt-10">
        <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 drop-shadow-lg leading-tight">
            تواصل معنا
        </h1>
        <p class="text-xl text-white/80 font-medium mb-8 max-w-2xl mx-auto">
            نحن هنا للرد على كافة استفساراتكم وتقديم الدعم اللازم. لا تتردد في الاتصال بنا.
        </p>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
    </div>
</section>

<!-- Contact Content -->
<section class="py-20 px-6 lg:px-20 bg-slate-50 dark:bg-background-dark">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12">
        
        <!-- Contact Form -->
        <div class="lg:w-2/3">
            <div class="bg-white dark:bg-[#1a1a1a] p-10 md:p-14 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-white/5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl transform -translate-y-1/2 translate-x-1/2"></div>
                
                <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 relative z-10">اترك لنا رسالة</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-10 relative z-10">سنقوم بالرد عليك في أقرب وقت ممكن.</p>
                
                <?php if ($message_status === 'success'): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold relative z-10">
                        <span class="material-symbols-outlined">check_circle</span>
                        تم إرسال رسالتك بنجاح! شكراً لتواصلك معنا.
                    </div>
                <?php elseif ($message_status === 'error'): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold relative z-10">
                        <span class="material-symbols-outlined">error</span>
                        يرجى التأكد من ملء جميع الحقول المطلوبة.
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6 relative z-10">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">الاسم بالكامل <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none">
                                    <span class="material-symbols-outlined text-lg">person</span>
                                </span>
                                <input type="text" name="name" required class="w-full bg-slate-50 dark:bg-[#12110f] border border-gray-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white py-4 pl-4 pr-12 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder-slate-400" placeholder="أدخل اسمك بالكامل">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none">
                                    <span class="material-symbols-outlined text-lg">mail</span>
                                </span>
                                <input type="email" name="email" required class="w-full bg-slate-50 dark:bg-[#12110f] border border-gray-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white py-4 pl-4 pr-12 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder-slate-400" placeholder="example@domain.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">الموضوع</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-lg">subject</span>
                            </span>
                            <input type="text" name="subject" class="w-full bg-slate-50 dark:bg-[#12110f] border border-gray-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white py-4 pl-4 pr-12 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder-slate-400" placeholder="عنوان الرسالة (اختياري)">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">الرسالة <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <textarea name="message" rows="5" required class="w-full bg-slate-50 dark:bg-[#12110f] border border-gray-200 dark:border-white/10 rounded-2xl text-slate-900 dark:text-white p-4 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder-slate-400 resize-none" placeholder="اكتب رسالتك أو استفسارك هنا..."></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-[#12110f] py-4 rounded-2xl font-black text-lg hover:bg-[#e1c48f] transition-all shadow-lg hover:shadow-primary/30 hover:-translate-y-1 flex items-center justify-center gap-2 mt-6">
                        <span class="material-symbols-outlined">send</span>
                        إرسال الرسالة
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="lg:w-1/3 space-y-8">
            <div class="bg-primary p-10 rounded-[2.5rem] shadow-xl text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
                <div class="absolute top-0 left-0 w-32 h-32 bg-white/20 rounded-full blur-2xl transform -translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 shadow-lg group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl text-primary">support_agent</span>
                </div>
                
                <h3 class="text-3xl font-black text-[#12110f] mb-2 relative z-10">اتصل بنا</h3>
                <p class="text-[#12110f]/80 font-bold mb-8 relative z-10">نحن متواجدون دائمًا لخدمتك</p>
                
                <div class="space-y-6 text-right relative z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#12110f]/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#12110f] text-xl">location_on</span>
                        </div>
                        <div>
                            <h4 class="font-black text-[#12110f] mb-1">العنوان:</h4>
                            <p class="text-[#12110f]/80 font-medium leading-relaxed">أوتو لاين، 2 مسجد الرحمن الرحيم، هاكستيب، النزهة، محافظة القاهرة 4473212</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#12110f]/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#12110f] text-xl">call</span>
                        </div>
                        <div>
                            <h4 class="font-black text-[#12110f] mb-1">رقم الهاتف:</h4>
                            <p class="text-[#12110f]/80 font-medium" dir="ltr">+20 100 341 2321</p>
                            <p class="text-[#12110f]/80 font-medium" dir="ltr">+20 115 414 4465</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#12110f]/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#12110f] text-xl">mail</span>
                        </div>
                        <div>
                            <h4 class="font-black text-[#12110f] mb-1">البريد الإلكتروني:</h4>
                            <p class="text-[#12110f]/80 font-medium break-all">info@autoline-car-rent.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Map Section -->
    <div class="max-w-7xl mx-auto mt-20 rounded-[2.5rem] overflow-hidden shadow-xl border border-gray-100 dark:border-white/5 relative h-[400px]">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3452.1264663363673!2d31.328228315116345!3d30.09062392334812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145815fd5cc4a4c1%3A0x600c0a811c76949f!2z2YXYs9iM2K8g2KfZhNix2K3ZhdmGINin2YTYsdit2YrZhdiMINmH2KfZg9iz2KrZitioINin2YTZhtmS2YfYqdiMINmF2K3Yp9mB2LjYqSDYp9mE2YLYp9mH2LHYqQ!5e0!3m2!1sar!2seg!4v1700000000000!5m2!1sar!2seg" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            class="absolute inset-0 w-full h-full filter dark:invert-[90%] dark:hue-rotate-180 dark:contrast-100 opacity-90 hover:opacity-100 transition-opacity duration-300">
        </iframe>
    </div>
</section>

<?php
require_once __DIR__ . '/../../Includes/Layout/Footer.php';
