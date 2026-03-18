<?php
/**
 * AutoLine - Main Layout Header
 * 
 * This file should be included at the beginning of every public page
 * 
 * Usage:
 * require_once __DIR__ . '/../Includes/Layout/Header.php';
 * 
 * Optional variables to set before including:
 * - $page_title: Page title (default: "اوتو لاين لتاجير السيارات")
 * - $meta_desc: Meta description
 * - $meta_keywords: Meta keywords
 * - $og_image: OpenGraph image URL
 */

namespace AutoLine\Includes\Layout;

// Prevent direct access
if (!defined('AUTOLINE_ROOT')) {
    define('AUTOLINE_ROOT', dirname(dirname(__DIR__)));
}

require_once AUTOLINE_ROOT . '/Core/init.php';

use AutoLine\Core\Config;

// Set default values
$page_title = $page_title ?? 'اوتو لاين لتاجير السيارات';
$meta_desc = $meta_desc ?? 'افضل خدمة تاجير سيارات في مصر - اوتو لاين تقدم لك تشكيلة واسعة من السيارات الحديثة والفاخرة بأسعر تنافسية.';
$meta_keywords = $meta_keywords ?? 'تاجير سيارات, اوتو لاين, تاجير سيارات مصر, سيارات فاخرة, حجز سيارة';
$og_image = $og_image ?? Config::getUrls()['base'] . '/Assets/images/og-default.jpg';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($page_title) ?> | اوتو لاين</title>
    <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>"/>
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>"/>
    <meta name="author" content="AutoLine - اوتو لاين"/>
    <meta name="robots" content="index, follow"/>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"/>
    
    <!-- OpenGraph Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>"/>
    <meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"/>
    <meta property="og:image" content="<?= $og_image ?>"/>
    <meta property="og:site_name" content="اوتو لاين"/>
    <meta property="og:locale" content="ar_EG"/>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>"/>
    <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc) ?>"/>
    <meta name="twitter:image" content="<?= $og_image ?>"/>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= Config::getUrls()['base'] ?>/favicon.ico"/>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cormorant+Garamond:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind Config -->
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
                },
            },
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        .glass { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .hero-gradient { 
            background: linear-gradient(to bottom, rgba(18, 17, 15, 0.2) 0%, rgba(18, 17, 15, 0.9) 100%); 
        }
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
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            right: 0;
            width: 0;
            height: 2px;
            background: #c9a96e;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
    </style>
    
    <!-- Page-specific styles -->
    <?php if (isset($page_styles)): ?>
    <style><?= $page_styles ?></style>
    <?php endif; ?>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen flex flex-col">

    <!-- Main Header -->
    <header class="fixed top-0 w-full z-50 border-b border-white/10 bg-background-dark/90 backdrop-blur-md px-6 lg:px-20 py-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="<?= Config::getUrls()['base'] ?>/index.php" class="flex items-center gap-2">
            <span class="text-primary material-symbols-outlined text-3xl">directions_car</span>
            <span class="font-bold text-2xl tracking-tight text-slate-100">اوتو لاين</span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-8">
            <a class="nav-link text-sm font-bold hover:text-primary transition-colors <?= $current_page === 'index' ? 'active text-primary' : '' ?>" href="<?= Config::getUrls()['base'] ?>/index.php">الرئيسية</a>
            <a class="nav-link text-sm font-bold hover:text-primary transition-colors <?= $current_page === 'about' ? 'active text-primary' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Pages/About.php">من نحن</a>
            <a class="nav-link text-sm font-bold hover:text-primary transition-colors <?= in_array($current_page, ['vehicles_gallery', 'car_details']) ? 'active text-primary' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Cars/index.php">سيارات للايجار</a>
            <a class="nav-link text-sm font-bold hover:text-primary transition-colors <?= in_array($current_page, ['blog', 'blog_details']) ? 'active text-primary' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Blog/index.php">المدونة</a>
            <a class="nav-link text-sm font-bold hover:text-primary transition-colors <?= $current_page === 'contact' ? 'active text-primary' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Pages/Contact.php">تواصل معنا</a>
        </nav>

        <!-- Desktop CTA + Mobile Hamburger -->
        <div class="flex items-center gap-3">
            <a href="<?= Config::getUrls()['base'] ?>/Modules/Booking/Flow.php" class="bg-primary text-[#12110f] px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-[#e1c48f] transition-all shadow-lg hidden md:block">
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all <?= $current_page === 'index' ? 'text-primary bg-white/5' : '' ?>" href="<?= Config::getUrls()['base'] ?>/index.php">
                <span class="material-symbols-outlined text-primary text-xl">home</span> الرئيسية
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all <?= $current_page === 'about' ? 'text-primary bg-white/5' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Pages/About.php">
                <span class="material-symbols-outlined text-primary text-xl">info</span> من نحن
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all <?= in_array($current_page, ['vehicles_gallery', 'car_details']) ? 'text-primary bg-white/5' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Cars/index.php">
                <span class="material-symbols-outlined text-primary text-xl">directions_car</span> سيارات للايجار
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all <?= in_array($current_page, ['blog', 'blog_details']) ? 'text-primary bg-white/5' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Blog/index.php">
                <span class="material-symbols-outlined text-primary text-xl">article</span> المدونة
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-bold hover:bg-white/5 hover:text-primary transition-all <?= $current_page === 'contact' ? 'text-primary bg-white/5' : '' ?>" href="<?= Config::getUrls()['base'] ?>/Modules/Pages/Contact.php">
                <span class="material-symbols-outlined text-primary text-xl">mail</span> تواصل معنا
            </a>
            <a href="<?= Config::getUrls()['base'] ?>/Modules/Booking/Flow.php" class="mt-3 bg-primary text-[#12110f] px-6 py-3 rounded-xl font-black text-center text-base hover:bg-[#e1c48f] transition-all block">
                احجز الآن
            </a>
        </div>
    </div>

    <!-- Spacer for fixed header -->
    <div class="h-[72px]"></div>

    <!-- Main Content Wrapper -->
    <main class="flex-1">
