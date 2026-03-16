<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db_connect.php';

// Fetch blogs
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة المدونة | أوتو لاين</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .glass-card {
            background: rgba(28, 28, 28, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <div>
                            <h1 class="text-4xl font-black text-white tracking-tight flex items-center gap-3">
                                <span class="material-symbols-outlined text-[#c9a96e] text-4xl">article</span>
                                إدارة المدونة
                            </h1>
                            <p class="text-slate-500 text-sm mt-2 uppercase tracking-[0.1em] font-bold">إدارة المقالات والمحتوى</p>
                        </div>
                    </div>
                    
                    <a href="add_blog.php" class="bg-[#c9a96e] text-[#12110f] px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-[#e1c48f] transition-all shadow-lg hover:shadow-[#c9a96e]/20 hover:-translate-y-1 self-start md:self-auto">
                        <span class="material-symbols-outlined">add</span>
                        إضافة مقال جديد
                    </a>
                </header>

                <!-- Feedback Message -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 p-4 rounded-xl mb-8 flex items-center gap-3 font-bold">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-8 flex items-center gap-3 font-bold">
                        <span class="material-symbols-outlined">error</span>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Blogs Table -->
                <div class="glass-card rounded-[2rem] overflow-hidden border border-white/5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse min-w-[800px]">
                            <thead>
                                <tr class="bg-white/[0.02]">
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 w-24">الصورة</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">العنوان</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">الكاتب</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">تاريخ النشر</th>
                                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-left">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php if (empty($blogs)): ?>
                                    <tr>
                                        <td colspan="5" class="px-8 py-12 text-center text-slate-500">لا توجد مقالات مضافة حتى الآن.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($blogs as $blog): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors">
                                        <td class="px-8 py-4">
                                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-white/5 border border-white/10">
                                                <img src="<?= htmlspecialchars($blog['image_path']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="w-full h-full object-cover">
                                            </div>
                                        </td>
                                        <td class="px-8 py-4">
                                            <p class="font-bold text-white text-lg line-clamp-1"><?= htmlspecialchars($blog['title']) ?></p>
                                        </td>
                                        <td class="px-8 py-4">
                                            <span class="text-slate-400 text-sm font-medium"><?= htmlspecialchars($blog['author']) ?></span>
                                        </td>
                                        <td class="px-8 py-4">
                                            <span class="text-slate-400 text-sm font-medium"><?= date('Y-m-d', strtotime($blog['created_at'])) ?></span>
                                        </td>
                                        <td class="px-8 py-4 text-left">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="edit_blog.php?id=<?= $blog['id'] ?>" class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center hover:bg-blue-500/20 transition-all font-bold" title="تعديل">
                                                    <span class="material-symbols-outlined text-sm">edit</span>
                                                </a>
                                                <a href="delete_blog.php?id=<?= $blog['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف هذا المقال نهائياً؟');" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500/20 transition-all font-bold" title="حذف">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
