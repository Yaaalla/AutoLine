<?php
session_start();
require_once '../config/db_connect.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin) {
        $authenticated = false;
        if (password_verify($password, $admin['password'])) {
            $authenticated = true;
        } elseif ($password === $admin['password']) {
            // Legacy plain text password - Upgrade to hash
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $upd->execute([$new_hash, $admin['id']]);
            $authenticated = true;
        }

        if ($authenticated) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>اوتو لاين | تسجيل الدخول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .glass-panel {
            background: rgba(28, 28, 28, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .luxury-gradient {
            background: radial-gradient(circle at top right, rgba(201, 169, 110, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(201, 169, 110, 0.05), transparent);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    </style>
</head>
<body class="bg-[#12110f] text-white font-sans h-screen flex items-center justify-center luxury-gradient overflow-hidden relative">
    <!-- Decorative Elements -->
    <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-[#c9a96e]/5 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-[#c9a96e]/5 blur-[120px] rounded-full"></div>

    <div class="w-full max-w-[450px] p-12 glass-panel rounded-[3rem] text-right z-10 animate-fade-in">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-gradient-to-tr from-[#c9a96e] to-[#e1c48f] rounded-[2rem] mx-auto mb-6 flex items-center justify-center shadow-[0_10px_30px_-5px_rgba(201,169,110,0.4)]">
                <span class="material-symbols-outlined text-[#12110f] text-4xl">key</span>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight mb-2">اوتو لاين</h1>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.4em]">بوابة الإدارة الحصرية</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-5 rounded-2xl mb-8 flex items-center gap-4 animate-fade-in text-xs font-bold">
                <span class="material-symbols-outlined text-lg">error_outline</span>
                <?= $error == "Invalid username or password" ? "اسم المستخدم أو كلمة المرور غير صحيحة" : $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <div class="relative group">
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-3 mr-1 transition-colors group-focus-within:text-[#c9a96e]">اسم المستخدم</label>
                <div class="relative">
                    <input type="text" name="username" required class="w-full bg-[#12110f]/60 border border-white/5 rounded-2xl px-6 py-5 text-sm text-white focus:border-[#c9a96e]/50 focus:outline-none focus:ring-4 focus:ring-[#c9a96e]/5 transition-all outline-none"/>
                    <span class="material-symbols-outlined absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-[#c9a96e] transition-colors">person</span>
                </div>
            </div>

            <div class="relative group">
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-3 mr-1 transition-colors group-focus-within:text-[#c9a96e]">كلمة المرور</label>
                <div class="relative">
                    <input type="password" name="password" required class="w-full bg-[#12110f]/60 border border-white/5 rounded-2xl px-6 py-5 text-sm text-white focus:border-[#c9a96e]/50 focus:outline-none focus:ring-4 focus:ring-[#c9a96e]/5 transition-all outline-none"/>
                    <span class="material-symbols-outlined absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-[#c9a96e] transition-colors">lock</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] py-6 rounded-2xl font-black uppercase tracking-[0.2em] hover:shadow-[0_20px_40px_-10px_rgba(201,169,110,0.4)] transition-all flex items-center justify-center gap-3 group active:scale-[0.98] duration-300">
                تسجيل الدخول
                <span class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-[-10px] rotate-180">arrow_forward</span>
            </button>
        </form>

        <div class="mt-12 text-center">
            <p class="text-[9px] text-slate-600 font-bold uppercase tracking-[0.4em] leading-relaxed">
                جميع الحقوق محفوظة &copy; <?= date('Y') ?> اوتو لاين لتاجير السيارات
            </p>
        </div>
    </div>
</body>
</html>
