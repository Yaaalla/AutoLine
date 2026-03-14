<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>أوتو لوكس - لتأجير السيارات الفاخرة</title>
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
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <header class="fixed top-0 w-full z-50 border-b border-white/10 bg-background-dark/80 backdrop-blur-md px-6 lg:px-20 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-primary material-symbols-outlined text-3xl">storm</span>
            <h1 class="font-serif text-3xl font-bold tracking-tight text-slate-100">أوتو لوكس</h1>
        </div>
        <nav class="hidden md:flex items-center gap-10">
            <a class="text-sm font-medium hover:text-primary transition-colors uppercase tracking-widest" href="index.php">الرئيسية</a>
            <a class="text-sm font-medium hover:text-primary transition-colors uppercase tracking-widest" href="vehicles_gallery.php">السيارات</a>
            <a class="text-sm font-medium hover:text-primary transition-colors uppercase tracking-widest" href="#">من نحن</a>
            <a class="text-sm font-medium hover:text-primary transition-colors uppercase tracking-widest" href="#">اتصل بنا</a>
        </nav>
        <a href="booking_flow.php" class="bg-primary text-background-dark px-8 py-2.5 rounded-lg font-bold text-sm uppercase tracking-wider hover:bg-white transition-all shadow-lg text-center">
            احجز الآن
        </a>
    </header>
