<?php 
require_once 'config/db_connect.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    header("Location: index.php");
    exit;
}

// Fetch latest 3 for sidebar
$sidebar_stmt = $pdo->query("SELECT * FROM blogs WHERE id != $id ORDER BY created_at DESC LIMIT 3");
$recent_blogs = $sidebar_stmt->fetchAll();

include 'includes/header.php'; 
?>

<!-- Hero Section for Blog Details -->
<section class="relative pt-32 pb-20 w-full flex flex-col items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-black/30 z-10 w-full h-full"></div>
        <img alt="<?= htmlspecialchars($blog['title']) ?>" class="w-full h-full object-cover opacity-50" src="<?= htmlspecialchars($blog['image_path']) ?>"/>
    </div>
    <div class="relative z-20 text-center px-4 max-w-4xl mt-10">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/20 text-primary font-bold mb-6 backdrop-blur-md border border-primary/30">
            تأجير سيارات
        </div>
        <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 drop-shadow-lg leading-tight">
            <?= htmlspecialchars($blog['title']) ?>
        </h1>
        <div class="flex items-center justify-center gap-6 text-white/80 font-medium">
            <span class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">calendar_month</span> <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
            <span class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">person</span> <?= htmlspecialchars($blog['author']) ?></span>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="py-20 px-6 lg:px-20 bg-slate-50 dark:bg-background-dark">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12">
        <div class="lg:w-2/3">
            <div class="bg-white dark:bg-[#1a1a1a] p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100 dark:border-white/5 prose prose-lg dark:prose-invert prose-headings:font-bold prose-headings:text-slate-900 dark:prose-headings:text-white prose-a:text-primary max-w-none">
                <p class="text-xl font-medium text-slate-600 dark:text-slate-300 leading-relaxed mb-8 border-r-4 border-primary pr-4">
                    <?= nl2br(htmlspecialchars($blog['excerpt'])) ?>
                </p>
                
                <div class="text-slate-700 dark:text-slate-300 leading-loose space-y-6">
                    <?= $blog['content'] // Outputs HTML safely if inputted from tinymce or retains formatting. Ensure trusted input only. ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-1/3 space-y-8">
            <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-white/5">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 border-b border-gray-100 dark:border-white/5 pb-4">مواضيع ذات صلة</h3>
                <div class="space-y-6">
                    <?php if(empty($recent_blogs)): ?>
                        <p class="text-slate-500">لا توجد مقالات أخرى.</p>
                    <?php endif; ?>
                    <?php foreach($recent_blogs as $rb): ?>
                    <a href="blog_details.php?id=<?= $rb['id'] ?>" class="flex gap-4 group">
                        <div class="w-24 h-24 rounded-xl overflow-hidden shrink-0">
                            <img src="<?= htmlspecialchars($rb['image_path']) ?>" alt="<?= htmlspecialchars($rb['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary transition-colors text-sm"><?= htmlspecialchars($rb['title']) ?></h4>
                            <span class="text-xs text-slate-500 flex items-center gap-1"><span class="material-symbols-outlined text-[10px]">calendar_month</span> <?= date('M d, Y', strtotime($rb['created_at'])) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="bg-primary p-8 rounded-3xl shadow-xl text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform -translate-y-1/2 translate-x-1/2"></div>
                <h3 class="text-3xl font-black text-[#12110f] mb-4">هل تبحث عن سيارة؟</h3>
                <p class="text-[#12110f]/80 font-medium mb-6">احجز سيارتك المفضلة اليوم بأفضل الأسعار وبكل سهولة.</p>
                <a href="vehicles_gallery.php" class="inline-block bg-[#12110f] text-white px-8 py-3 rounded-xl font-bold hover:bg-slate-800 transition-colors shadow-lg w-full">
                    تصفح أسطول السيارات
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
