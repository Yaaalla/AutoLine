<?php
/**
 * AutoLine - Blog Index
 * 
 * صفحة المدونة - عرض جميع المقالات
 * 
 * @package AutoLine\Modules\Blog
 */

require_once __DIR__ . '/../../Core/init.php';

use AutoLine\Core\Config;
use AutoLine\Core\Database;

// Fetch all blogs
$pdo = Database::getConnection();
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll();

// Set page variables
$page_title = "المدونة";
$meta_desc = "تابع أحدث النصائح والمقالات حول عالم السيارات وتأجيرها في مصر";

require_once __DIR__ . '/../../Includes/Layout/Header.php';
?>

<!-- Hero Section for Blog List -->
<section class="relative pt-32 pb-20 w-full flex flex-col items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/70 z-10 w-full h-full"></div>
        <img alt="Blog Auto Line" class="w-full h-full object-cover opacity-60" src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&q=80"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-4xl mt-10">
        <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 drop-shadow-lg leading-tight">
            المدونة والمقالات
        </h1>
        <p class="text-xl text-white/80 font-medium mb-8 max-w-2xl mx-auto">
            تابع أحدث النصائح، الأخبار، والمقالات حول عالم السيارات وتأجيرها.
        </p>
        <div class="w-24 h-1 bg-primary mx-auto rounded-full"></div>
    </div>
</section>

<!-- Blog List Content -->
<section class="py-20 px-6 lg:px-20 bg-slate-50 dark:bg-background-dark">
    <div class="max-w-7xl mx-auto">
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php if (empty($blogs)): ?>
                <div class="col-span-full text-center py-12 text-slate-500 text-xl font-bold">لا توجد مقالات منشورة بعد.</div>
            <?php else: ?>
                <?php foreach ($blogs as $blog_post): ?>
                <article class="bg-white dark:bg-[#1a1a1a] rounded-3xl overflow-hidden shadow-lg border border-gray-100 dark:border-white/5 group transform hover:-translate-y-2 transition-all duration-300 flex flex-col h-full">
                    <div class="h-64 overflow-hidden relative shrink-0">
                        <img src="<?= htmlspecialchars($blog_post['image_path'] ?? 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&q=80') ?>" alt="<?= htmlspecialchars($blog_post['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 right-4 bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg z-10">
                            مقال مميز
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-1">
                        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4 font-medium">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">calendar_month</span> <?= date('M d, Y', strtotime($blog_post['created_at'])) ?></span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">person</span> <?= htmlspecialchars($blog_post['author']) ?></span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 hover:text-primary transition-colors leading-snug">
                            <a href="detail.php?id=<?= $blog_post['id'] ?>"><?= htmlspecialchars($blog_post['title']) ?></a>
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 line-clamp-3 text-lg flex-1">
                            <?= htmlspecialchars($blog_post['excerpt']) ?>
                        </p>
                        <a href="detail.php?id=<?= $blog_post['id'] ?>" class="inline-flex items-center gap-2 text-primary font-bold hover:text-slate-900 dark:hover:text-white transition-colors group-hover:gap-3 mt-auto pt-4 border-t border-gray-100 dark:border-white/5">
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

    </div>
</section>

<?php
require_once __DIR__ . '/../../Includes/Layout/Footer.php';
