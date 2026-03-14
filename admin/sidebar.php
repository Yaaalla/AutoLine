<?php
// admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="fixed inset-y-0 right-0 z-50 w-72 border-l border-white/10 bg-[#12110f]/95 backdrop-blur-2xl p-8 flex flex-col h-full transform transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:h-auto">
    <!-- Mobile Close Toggle -->
    <button onclick="toggleSidebar()" class="lg:hidden absolute top-6 left-6 text-slate-500 hover:text-white transition-colors">
        <span class="material-symbols-outlined">close</span>
    </button>
    <div class="mb-12 relative">
        <div class="absolute -top-4 -right-4 w-20 h-20 bg-[#c9a96e]/10 blur-3xl rounded-full"></div>
        <h1 class="text-3xl font-black text-white tracking-tighter mb-1">
            أوتو<span class="text-[#c9a96e]">لوكس</span>
        </h1>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">بوابة المسئول</p>
        </div>
    </div>
    
    <nav class="flex-1 space-y-2">
        <?php
        $nav_items = [
            ['id' => 'dashboard.php', 'label' => 'نظرة عامة', 'icon' => 'dashboard', 'url' => 'dashboard.php'],
            ['id' => 'manage_cars.php', 'label' => 'إدارة الأسطول', 'icon' => 'directions_car', 'url' => 'manage_cars.php', 'active_on' => ['manage_cars.php', 'edit_car.php']],
            ['id' => 'manage_bookings.php', 'label' => 'الحجوزات', 'icon' => 'calendar_month', 'url' => 'manage_bookings.php'],
        ];

        foreach ($nav_items as $item):
            $is_active = ($current_page == $item['id']) || (isset($item['active_on']) && in_array($current_page, $item['active_on']));
        ?>
            <a href="<?= $item['url'] ?>" class="group flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 relative overflow-hidden <?= $is_active ? 'bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] shadow-[0_10px_20px_-10px_rgba(201,169,110,0.5)]' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                <span class="material-symbols-outlined text-[22px] transition-transform group-hover:scale-110"><?= $item['icon'] ?></span>
                <span class="text-sm font-bold tracking-tight"><?= $item['label'] ?></span>
                
                <?php if ($is_active): ?>
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#12110f]/20"></div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>

        <div class="pt-8 pb-4">
            <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] px-5 mb-4 opacity-50">النظام</p>
        </div>

        <a href="manage_admins.php" class="group flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 <?= $current_page == 'manage_admins.php' ? 'bg-gradient-to-r from-[#c9a96e] to-[#e1c48f] text-[#12110f] shadow-[0_10px_20px_-10px_rgba(201,169,110,0.5)]' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <span class="material-symbols-outlined text-[22px] transition-transform group-hover:scale-110">admin_panel_settings</span>
            <span class="text-sm font-bold tracking-tight">المديرين</span>
        </a>
    </nav>
    
    <div class="pt-8 border-t border-white/5 mt-auto">
        <a href="logout.php" class="group flex items-center gap-4 px-5 py-4 text-red-400/70 hover:text-red-400 transition-all duration-300 rounded-2xl hover:bg-red-500/5">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center group-hover:bg-red-500/20 transition-all">
                <span class="material-symbols-outlined text-xl">logout</span>
            </div>
            <span class="text-xs font-black uppercase tracking-widest">تسجيل الخروج</span>
        </a>
    </div>
</aside>

<!-- Overlay for mobile -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
