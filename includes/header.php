<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= isset($page_title) ? $page_title . " | اوتو لاين" : "اوتو لاين لتاجير السيارات" ?></title>
    <meta name="description" content="<?= isset($meta_desc) ? htmlspecialchars($meta_desc) : "افضل خدمة تاجير سيارات في مصر - اوتو لاين تقدم لك تشكيلة واسعة من السيارات الحديثة والفاخرة بأسعار تنافسية." ?>"/>
    <link rel="canonical" href="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>"/>
    
    <!-- OpenGraph Tags -->
    <meta property="og:title" content="<?= isset($page_title) ? $page_title : "اوتو لاين لتاجير السيارات" ?>"/>
    <meta property="og:description" content="<?= isset($meta_desc) ? htmlspecialchars($meta_desc) : "افضل خدمة تاجير سيارات في مصر" ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cormorant+Garamond:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#c9a96e",
                        "background-light": "#f8f7f6",
                        "background-dark": "#12110f",
                        "accent-dark": "#1A2420",
                        "surface": "#1c1c1c",
                    },
                    fontFamily: {
                        "display": ["Tajawal", "sans-serif"],
                        "serif": ["Cormorant Garamond", "serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .hero-gradient { background: linear-gradient(to bottom, rgba(18, 17, 15, 0.2) 0%, rgba(18, 17, 15, 0.9) 100%); }
        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
            opacity: 0;
        }
        #mobile-menu.open {
            max-height: 600px;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">

    <!-- Main Header -->
    <header class="fixed top-0 w-full z-50 border-b border-white/10 bg-background-dark/90 backdrop-blur-md px-6 lg:px-20 py-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-2">
            <span class="text-primary material-symbols-outlined text-3xl">directions_car</span>
            <span class="font-bold text-2xl tracking-tight text-slate-100">اوتو لاين</span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-8">
            <a class="text-sm font-bold hover:text-primary transition-colors" href="index.php">الرئيسية</a>
            <a class="text-sm font-bold hover:text-primary transition-colors" href="about.php">من نحن</a>
            <a class="text-sm font-bold hover:text-primary transition-colors" href="vehicles_gallery.php">سيارات للايجار</a>
            <a class="text-sm font-bold hover:text-primary transition-colors" href="blog.php">المدونة</a>
            <a class="text-sm font-bold hover:text-primary transition-colors" href="contact.php">تواصل معنا</a>
        </nav>

        <!-- Desktop CTA + Mobile Hamburger -->
        <div class="flex items-center gap-3">
            <a href="booking_flow.php" class="bg-primary text-[#12110f] px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-[#e1c48f] transition-all shadow-lg hidden md:block">
                احجز الآن
            </a>
            <!-- Hamburger Button -->
            <button id="menu-toggle" class="md:hidden w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 transition-colors flex items-center justify-center focus:outline-none" aria-label="القائمة">
                <span id="hamburger-icon" class="material-symbols-outlined text-white text-2xl">menu</span>
            </button>
        </div>
    </header>

    <!-- Mobile Nav Dropdown -->
    <div id="mobile-menu" class="fixed top-[69px] right-0 left-0 z-40 bg-[#12110f]/98 backdrop-blur-xl border-b border-white/10 shadow-2xl px-6 py-0 md:hidden">
        <div class="py-4 flex flex-col gap-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all" href="index.php">
                <span class="material-symbols-outlined text-primary text-xl">home</span> الرئيسية
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all" href="about.php">
                <span class="material-symbols-outlined text-primary text-xl">info</span> من نحن
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all" href="vehicles_gallery.php">
                <span class="material-symbols-outlined text-primary text-xl">directions_car</span> سيارات للايجار
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all" href="blog.php">
                <span class="material-symbols-outlined text-primary text-xl">article</span> المدونة
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all" href="contact.php">
                <span class="material-symbols-outlined text-primary text-xl">mail</span> تواصل معنا
            </a>
            <a href="booking_flow.php" class="mt-3 bg-primary text-[#12110f] px-6 py-3 rounded-xl font-black text-center text-base hover:bg-[#e1c48f] transition-all block">
                احجز الآن
            </a>
        </div>
    </div>

    <script>
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');
        let isOpen = false;

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
    </script>
