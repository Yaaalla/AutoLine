<?php
/**
 * AutoLine - Main Layout Footer
 * 
 * This file should be included at the end of every public page
 * 
 * Usage:
 * require_once __DIR__ . '/../Includes/Layout/Footer.php';
 */

namespace AutoLine\Includes\Layout;

use AutoLine\Core\Config;

// Ensure this file is included after Header.php
if (!defined('AUTOLINE_ROOT')) {
    define('AUTOLINE_ROOT', dirname(dirname(__DIR__)));
}

$current_year = date('Y');
$base_url = Config::getUrls()['base'];
?>
    </main><!-- End Main Content -->

    <!-- Footer -->
    <footer class="bg-background-dark border-t border-white/5 pt-20 pb-10 px-6 lg:px-20 mt-auto">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12 mb-20">
            <!-- Company Info -->
            <div class="col-span-1 lg:col-span-1">
                <div class="flex items-center gap-2 mb-8">
                    <span class="text-primary material-symbols-outlined text-3xl">directions_car</span>
                    <h1 class="font-bold text-3xl text-white">اوتو لاين</h1>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-8">
                    أوتو لاين، 2 مسجد الرحمن الرحيم، هاكستيب، النزهة، محافظة القاهرة 4473212
                </p>
                <div class="flex gap-4">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/AutoLineRentCar" target="_blank" rel="noopener noreferrer" 
                       class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-primary transition-colors">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/Auto_line_car_rent/" target="_blank" rel="noopener noreferrer"
                       class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-primary transition-colors">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.975.975 1.247 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.975.975-2.242 1.247-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.975-.975-1.247-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608.975-.975 2.242-1.247 3.608-1.308 1.266-.058 1.646-.07 4.85-.07z"/>
                        </svg>
                    </a>
                    <!-- WhatsApp -->
                    <a href="https://wa.me/201003412321" target="_blank" rel="noopener noreferrer"
                       class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-primary transition-colors">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.017 21.496c-1.602 0-3.193-.43-4.588-1.245L4 21.492l1.261-3.328c-.818-1.398-1.258-3.015-1.258-4.665h.001c.002-5.114 4.156-9.261 9.27-9.261 2.479 0 4.808.966 6.563 2.72 1.751 1.751 2.716 4.08 2.716 6.551.002 5.119-4.154 9.266-9.267 9.267z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h5 class="text-white font-bold text-lg mb-8">روابط سريعة</h5>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li><a class="hover:text-primary transition-colors" href="<?= $base_url ?>/Modules/Pages/About.php">من نحن</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?= $base_url ?>/Modules/Cars/index.php">سيارات للايجار</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?= $base_url ?>/Modules/Blog/index.php">المدونة</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?= $base_url ?>/Modules/Pages/Contact.php">تواصل معنا</a></li>
                    <li><a class="hover:text-primary transition-colors" href="<?= $base_url ?>/admin/login.php">لوحة الإدارة</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h5 class="text-white font-bold text-lg mb-8">تواصل معنا</h5>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li class="flex items-center gap-3" dir="ltr">
                        <span class="material-symbols-outlined text-primary text-lg">call</span>
                        <a href="tel:+201003412321" class="hover:text-primary transition-colors">+20 100 341 2321</a>
                    </li>
                    <li class="flex items-center gap-3" dir="ltr">
                        <span class="material-symbols-outlined text-primary text-lg">call</span>
                        <a href="tel:+201154144465" class="hover:text-primary transition-colors">+20 115 414 4465</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-lg">schedule</span>
                        نعمل 24/7 طوال أيام الأسبوع
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="max-w-7xl mx-auto border-t border-white/5 pt-10 flex flex-col items-center gap-4">
            <p class="text-slate-500 text-sm">
                © <?= $current_year ?> اوتو لاين لتاجير السيارات. جميع الحقوق محفوظة.
            </p>
            <p class="text-slate-500 text-sm">
                Designed by <a href="https://yaaalla.com/" target="_blank" rel="noopener noreferrer" 
                               class="text-primary hover:underline transition-all">Yaaalla</a>
            </p>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('menu-toggle');
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('hamburger-icon');
            let isOpen = false;

            if (toggle && menu && icon) {
                toggle.addEventListener('click', () => {
                    isOpen = !isOpen;
                    menu.classList.toggle('open', isOpen);
                    icon.textContent = isOpen ? 'close' : 'menu';
                });

                menu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        isOpen = false;
                        menu.classList.remove('open');
                        icon.textContent = 'menu';
                    });
                });
            }
        });
    </script>

    <!-- Page-specific scripts -->
    <?php if (isset($page_scripts)): ?>
    <script><?= $page_scripts ?></script>
    <?php endif; ?>

</body>
</html>
