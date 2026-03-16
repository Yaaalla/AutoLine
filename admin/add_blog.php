<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $content = trim($_POST['content']);
    $created_at = !empty($_POST['created_at']) ? date('Y-m-d H:i:s', strtotime($_POST['created_at'])) : date('Y-m-d H:i:s');
    
    // Auto-generate excerpt
    $excerpt = mb_substr(strip_tags($content), 0, 150) . '...';
    
    // Validate
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "يرجى ملء كافة الحقول المطلوبة.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "صورة المقال مطلوبة.";
    } else {
        // Image Upload
        $upload_dir = __DIR__ . '/../uploads/blogs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('blog_') . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            // DB relative path
            $db_image_path = 'uploads/blogs/' . $new_filename;
            
            try {
                $stmt = $pdo->prepare("INSERT INTO blogs (title, author, excerpt, content, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $excerpt, $content, $db_image_path, $created_at]);
                
                $_SESSION['success'] = "تم إضافة المقال بنجاح!";
                header("Location: manage_blogs.php");
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = "حدث خطأ في قاعدة البيانات: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "فشل في رفع الصورة.";
        }
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إضافة مقال جديد | أوتو لاين</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <!-- TinyMCE for rich text editing (Optional, using basic textarea for simplicity here, but recommended for production) -->
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
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <header class="mb-12">
                    <div class="flex items-center gap-4 mb-4">
                        <a href="manage_blogs.php" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all">
                            <span class="material-symbols-outlined text-sm rtl:rotate-180">arrow_back</span>
                        </a>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#c9a96e]">إضافة مقال</p>
                    </div>
                    <h1 class="text-4xl font-black text-white tracking-tight flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500 text-4xl">post_add</span>
                        مقال جديد
                    </h1>
                </header>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-8 flex items-center gap-3 font-bold">
                        <span class="material-symbols-outlined">error</span>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="glass-card rounded-[2rem] p-8 md:p-12">
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Title -->
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">عنوان المقال *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500">
                                        <span class="material-symbols-outlined text-lg">title</span>
                                    </div>
                                    <input type="text" name="title" required
                                        class="w-full bg-[#12110f] border border-white/10 rounded-xl text-white py-4 pl-4 pr-12 focus:outline-none focus:border-[#c9a96e] focus:ring-1 focus:ring-[#c9a96e] transition-all placeholder-slate-600"
                                        placeholder="مثال: دليلك لاختيار السيارة العائلية">
                                </div>
                            </div>
                            
                            <!-- Author -->
                            <div class="space-y-3">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">اسم الكاتب</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500">
                                        <span class="material-symbols-outlined text-lg">person</span>
                                    </div>
                                    <input type="text" name="author" value="Admin"
                                        class="w-full bg-[#12110f] border border-white/10 rounded-xl text-white py-4 pl-4 pr-12 focus:outline-none focus:border-[#c9a96e] focus:ring-1 focus:ring-[#c9a96e] transition-all placeholder-slate-600"
                                        placeholder="اسم الكاتب">
                                </div>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">صورة المقال الرئيسية *</label>
                            <div class="border-2 border-dashed border-white/10 rounded-2xl p-8 text-center hover:border-[#c9a96e]/50 transition-colors bg-[#12110f]">
                                <input type="file" name="image" id="image" accept="image/*" required class="hidden" onchange="previewImage(this)">
                                <label for="image" class="cursor-pointer flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl">add_photo_alternate</span>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-lg mb-1">اضغط لاختيار صورة</p>
                                        <p class="text-sm text-slate-500">JPG, PNG, WebP (يفضل 1200x800)</p>
                                    </div>
                                </label>
                                <div id="image-preview" class="mt-6 hidden">
                                    <img src="" alt="Preview" class="max-h-64 mx-auto rounded-xl border border-white/10 shadow-2xl">
                                </div>
                            </div>
                        </div>

                        <!-- Publish Date -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">تاريخ النشر</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500">
                                    <span class="material-symbols-outlined text-lg">calendar_month</span>
                                </div>
                                <input type="datetime-local" name="created_at" value="<?= date('Y-m-d\TH:i') ?>"
                                    class="w-full bg-[#12110f] border border-white/10 rounded-xl text-white py-4 pl-4 pr-12 focus:outline-none focus:border-[#c9a96e] focus:ring-1 focus:ring-[#c9a96e] transition-all placeholder-slate-600">
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">محتوى المقال كاملاً *</label>
                            <textarea name="content" rows="15" required
                                class="w-full bg-[#12110f] border border-white/10 rounded-xl text-white p-4 focus:outline-none focus:border-[#c9a96e] focus:ring-1 focus:ring-[#c9a96e] transition-all placeholder-slate-600"
                                placeholder="ابدأ كتابة مقالك هنا (يمكنك استخدام HTML لتنسيق النصوص)..."></textarea>
                            <p class="text-xs text-slate-500 mt-2">* يدعم إدخال وسوم HTML مثل &lt;h1&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center gap-4 pt-8 mt-8 border-t border-white/5">
                            <button type="submit" class="bg-[#c9a96e] text-[#12110f] px-10 py-4 rounded-xl font-black flex items-center gap-2 hover:bg-[#e1c48f] transition-all shadow-lg hover:shadow-[#c9a96e]/20 text-lg flex-1 md:flex-none justify-center">
                                <span class="material-symbols-outlined">publish</span>
                                نشر المقال
                            </button>
                            <a href="manage_blogs.php" class="bg-white/5 text-white px-10 py-4 rounded-xl font-bold hover:bg-white/10 transition-all text-center flex-1 md:flex-none">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const img = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
