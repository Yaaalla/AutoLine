<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
require_once '../config/db_connect.php';
require_once '../includes/functions.php';

$success_msg = "";
$error_msg = "";
$admin_id = $_SESSION['admin_id'];

// Add Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    try {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        
        log_activity($pdo, $admin_id, "Added Admin", "Created new admin account: $username");
        
        $_SESSION['success'] = "Admin added successfully.";
        header("Location: manage_admins.php");
        exit;
    } catch (Exception $e) {
        $error_msg = "Error adding admin: " . $e->getMessage();
    }
}

// Delete Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $del_id = $_POST['delete_id'];
        if ($del_id == $admin_id) throw new Exception("You cannot delete yourself.");
        
        $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
        $stmt->execute([$del_id]);
        $target = $stmt->fetch();
        
        if ($target) {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$del_id]);
            log_activity($pdo, $admin_id, "Deleted Admin", "Removed admin account: " . $target['username']);
            $_SESSION['success'] = "Admin removed.";
            header("Location: manage_admins.php");
            exit;
        }
    } catch (Exception $e) {
        $error_msg = $e->getMessage();
    }
}

if (isset($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html class="dark" lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة المسؤولين | أوتو لوكس</title>
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
        .input-premium {
            background: rgba(18, 17, 15, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            border-color: #c9a96e;
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1);
            background: rgba(18, 17, 15, 1);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-[#12110f] text-slate-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto p-8 lg:p-12">
            <div class="animate-fade-up max-w-[1600px] mx-auto">
                <header class="mb-12 flex items-center gap-6">
                    <!-- Mobile Toggle -->
                    <button onclick="toggleSidebar()" class="lg:hidden w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div>
                        <h2 class="text-4xl font-black text-white tracking-tight">إدارة المسؤولين</h2>
                        <p class="text-slate-500 text-sm mt-1 uppercase tracking-[0.2em] font-bold">إدارة طاقم العمل والتحكم في الصلاحيات</p>
                    </div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Admin List -->
                    <div class="lg:col-span-8 flex flex-col gap-6 order-2 lg:order-1">
                        <?php if ($success_msg): ?>
                            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 p-6 rounded-[1.5rem] flex items-center gap-4 animate-fade-up">
                                <span class="material-symbols-outlined">verified</span>
                                <span class="text-xs font-bold uppercase tracking-widest"><?= $success_msg ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_msg): ?>
                            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-6 rounded-[1.5rem] flex items-center gap-4 animate-fade-up">
                                <span class="material-symbols-outlined">error</span>
                                <span class="text-xs font-bold uppercase tracking-widest"><?= $error_msg ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="glass-card rounded-[3rem] overflow-hidden border border-white/5 shadow-2xl">
                            <div class="p-10 border-b border-white/5">
                                <h3 class="font-black text-xl text-white">طاقم الإدارة الحالي</h3>
                                <p class="text-slate-500 text-[10px] mt-1 font-bold uppercase tracking-widest">المسؤولون الذين لديهم صلاحية الوصول للنظام</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-right border-collapse">
                                    <thead>
                                        <tr class="bg-white/[0.02]">
                                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">اسم المستخدم</th>
                                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">تاريخ البدء</th>
                                            <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-left">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <?php if (empty($admins)): ?>
                                            <tr>
                                                <td colspan="3" class="px-10 py-20 text-center">
                                                    <p class="text-slate-500 font-bold uppercase tracking-widest">لم يتم العثور على مسؤولين</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        
                                        <?php foreach ($admins as $a): ?>
                                        <tr class="group hover:bg-white/[0.02] transition-colors">
                                            <td class="px-10 py-8">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e] font-black text-xs">
                                                        <?= mb_substr($a['username'], 0, 1) ?>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-white text-sm"><?= htmlspecialchars($a['username']) ?></span>
                                                        <?php if ($a['id'] == $admin_id): ?>
                                                            <span class="text-[9px] text-[#c9a96e] font-black uppercase tracking-widest mt-0.5">أنت</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-10 py-8 text-xs font-bold text-slate-400 capitalize">
                                                <?= date('d M, Y', strtotime($a['created_at'])) ?>
                                            </td>
                                            <td class="px-10 py-8 text-left">
                                                <?php if ($a['id'] != $admin_id): ?>
                                                    <form method="POST" onsubmit="return confirm('إزالة هذا المسؤول من النظام؟');" class="inline-block">
                                                        <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-500/5 hover:bg-red-500/10 text-red-500 flex items-center justify-center transition-all border border-red-500/20 group/del" title="حذف المسؤول">
                                                            <span class="material-symbols-outlined text-sm group-hover/del:scale-110 transition-transform">delete</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Add Admin Form -->
                    <div class="lg:col-span-4 h-fit sticky top-0 order-1 lg:order-2">
                        <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#c9a96e]/5 blur-3xl opacity-50"></div>
                            
                            <h3 class="font-black text-xl mb-8 text-white flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#c9a96e]/10 flex items-center justify-center text-[#c9a96e]">
                                    <span class="material-symbols-outlined">person_add</span>
                                </div>
                                إضافة مساعد جديد
                            </h3>

                            <form method="POST" class="space-y-6">
                                <input type="hidden" name="add_admin" value="1">
                                
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 mr-1">اسم المستخدم</label>
                                    <input type="text" name="username" placeholder="مثال: ahmed_admin" required class="w-full input-premium rounded-2xl px-5 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 mr-1">كلمة المرور</label>
                                    <input type="password" name="password" placeholder="••••••••" required class="w-full input-premium rounded-2xl px-5 py-4 text-sm text-slate-100 focus:outline-none"/>
                                </div>

                                <button type="submit" class="w-full bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] py-5 rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:shadow-[0_10px_30px_-10px_rgba(201,169,110,0.4)] transition-all flex items-center justify-center gap-3 mt-4">
                                    <span class="material-symbols-outlined text-sm">shield_person</span>
                                    منح الصلاحية
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
