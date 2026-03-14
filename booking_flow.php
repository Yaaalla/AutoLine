<?php 
require_once 'config/db_connect.php'; 

$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: vehicles_gallery.php");
    exit;
}

// Simple submission logic (in real world, this would be a separate POST request)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup = $_POST['pickup_date'];
    $return = $_POST['return_date'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Calculate total price (simplified for demo)
    $p_date = new DateTime($pickup);
    $r_date = new DateTime($return);
    $days = $r_date->diff($p_date)->days ?: 1;
    $total = $days * $car['price_per_day'];

    $insert = $pdo->prepare("INSERT INTO bookings (car_id, customer_name, customer_email, customer_phone, pickup_date, return_date, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($insert->execute([$car_id, $name, $email, $phone, $pickup, $return, $total])) {
        $message = "success";
    }
}

include 'includes/header.php'; 
?>

<main class="pt-24 min-h-screen bg-background-light dark:bg-background-dark">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <?php if ($message === "success"): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-12 rounded-3xl text-center">
                <span class="material-symbols-outlined text-6xl text-emerald-500 mb-6 font-black scale-150">check_circle</span>
                <h2 class="text-4xl font-black uppercase text-white mb-4">تم إرسال طلبك بنجاح!</h2>
                <p class="text-slate-400 text-lg mb-8">لقد استلمنا طلب حجز سيارة <span class="text-white font-bold"><?= $car['brand'] ?></span>. سيتصل بك فريقنا قريباً لتأكيد التفاصيل.</p>
                <a href="index.php" class="inline-block bg-primary text-background-dark px-10 py-4 rounded-xl font-black uppercase tracking-widest hover:scale-105 transition-all">العودة للرئيسية</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Form -->
                <div>
                    <h2 class="text-4xl font-black uppercase mb-8">احجز رحلتك</h2>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">الاسم الكامل</label>
                            <input type="text" name="name" required placeholder="ادخل اسمك بالكامل" class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">البريد الإلكتروني</label>
                            <input type="email" name="email" required placeholder="example@mail.com" class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">رقم الهاتف</label>
                            <input type="tel" name="phone" required placeholder="+20 1..." class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all text-left" dir="ltr"/>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">تاريخ الاستلام</label>
                                <input type="date" name="pickup_date" required class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2">تاريخ العودة</label>
                                <input type="date" name="return_date" required class="w-full bg-slate-100 dark:bg-surface border-0 rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary transition-all"/>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-primary text-background-dark py-4 rounded-xl font-black uppercase tracking-widest hover:bg-white transition-all shadow-xl shadow-primary/20">
                            تأكيد طلب الحجز
                        </button>
                    </form>
                </div>

                <!-- Summary -->
                <div class="bg-slate-50 dark:bg-[#1c1a17] p-8 rounded-3xl border border-primary/10 h-fit sticky top-32">
                    <h3 class="text-xl font-black uppercase mb-6">ملخص الحجز</h3>
                    <div class="flex gap-4 mb-6 pb-6 border-b border-primary/5">
                        <img src="<?= htmlspecialchars($car['image_path']) ?>" class="w-24 h-16 object-cover rounded-lg"/>
                        <div>
                            <p class="text-xs font-bold text-primary"><?= $car['brand'] ?></p>
                            <p class="font-black"><?= $car['model'] ?></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-500 uppercase">السعر اليومي</span>
                            <span>$<?= number_format($car['price_per_day']) ?></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-500 uppercase">التأمين</span>
                            <span class="text-emerald-500">مشمول</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
