# AutoLine - Project Structure Guide

## نظرة عامة على هيكل المشروع الجديد

تم إعادة تنظيم مشروع AutoLine ليكون احترافيًا وسهل التطوير (Scalable)، مع الفصل بين طبقة المنطق (Logic) وطبقة العرض (UI).

---

## 📁 الهيكل الجديد

```
/home/abdelrhman/Desktop/autoline2/autoLine/
│
├── Core/                           # 💻 طبقة المنطق (Business Logic)
│   ├── Config.php                 # ⚙️ ملف الإعدادات المركزي
│   ├── Database.php               # 🗄️ فئة الاتصال بقاعدة البيانات
│   ├── init.php                   # 🚀 تهيئة التطبيق (Bootstrap)
│   │
│   ├── Database/                  # مهام قاعدة البيانات
│   │   ├── Migrations/            # ترحيلات قاعدة البيانات
│   │   └── Seeds/                 # بيانات تجريبية
│   │
│   ├── Helpers/                   # مساعدات برمجية
│   │   ├── Validator.php          # التحقق من البيانات
│   │   ├── Session.php            # إدارة الجلسات
│   │   └── Response.php           # استجابات HTTP
│   │
│   └── Utils/                     # أدوات مساعدة
│       └── Functions.php          # الدوال المشتركة
│
├── Includes/                       # 🔧 مكونات واجهة المستخدم
│   └── Layout/
│       ├── Header.php             # 🎨 رأس الصفحة الموحد
│       └── Footer.php             # 🎨 ذيل الصفحة الموحد
│
├── Modules/                        # 📦 الوحدات الوظيفية
│   ├── Cars/
│   │   ├── index.php              # معرض السيارات
│   │   └── detail.php             # تفاصيل السيارة
│   │
│   ├── Blog/
│   │   ├── index.php              # المدونة
│   │   └── detail.php             # تفاصيل المقال
│   │
│   ├── Pages/
│   │   ├── About.php              # من نحن
│   │   └── Contact.php            # تواصل معنا
│   │
│   └── Booking/
│       └── Flow.php               # تدفق الحجز
│
├── Admin/                          # 👨‍💼 لوحة الإدارة (موجودة مسبقاً)
│   ├── login.php
│   ├── dashboard.php
│   ├── manage_cars.php
│   └── ...
│
├── Assets/                         # 🎨 الملفات الثابتة
│   ├── css/                       # ملفات CSS
│   ├── js/                        # ملفات JavaScript
│   ├── images/                    # الصور
│   └── fonts/                     # الخطوط
│
├── Storage/                        # 💾 التخزين
│   ├── Uploads/                   # ملفات مرفوعة
│   ├── Logs/                      # سجلات الأخطاء
│   └── Cache/                     # التخزين المؤقت
│
├── Database/                       # 🗃️ قاعدة البيانات
│   ├── Schema/                    # هيكل الجداول
│   └── Seeds/                     # بيانات أولية
│
├── docs/                          # 📚 التوثيق
│   └── ...
│
├── public/                         # 🌐 نقطة الدخول العامة
│   └── index.php                  # الصفحة الرئيسية
│
├── index.php                       # 🏠 الصفحة الرئيسية
├── .htaccess                       # ⚙️ إعدادات Apache
└── README.md                       # 📖 هذا الملف
```

---

## 🎯 كيفية استخدام Header و Footer في الصفحات

### الطريقة الجديدة (الموصى بها):

```php
<?php
/**
 * AutoLine - [اسم الصفحة]
 * 
 * @package AutoLine\Modules\[المجلد]
 */

require_once __DIR__ . '/../Core/init.php';

use AutoLine\Core\Config;
use AutoLine\Core\Database;

// منطق الصفحة هنا
$pdo = Database::getConnection();
// ...

// تعيين متغيرات الصفحة
$page_title = "عنوان الصفحة";
$meta_desc = "وصف الصفحة للـ SEO";

// تضمين Header
require_once __DIR__ . '/../Includes/Layout/Header.php';
?>

<!-- محتوى الصفحة HTML هنا -->

<?php
// تضمين Footer
require_once __DIR__ . '/../Includes/Layout/Footer.php';
```

### أمثلة على المسارات الصحيحة (Linux):

```php
// في صفحات Modules/Cars/
require_once __DIR__ . '/../../Core/init.php';
require_once __DIR__ . '/../../Includes/Layout/Header.php';

// في صفحات Modules/Pages/
require_once __DIR__ . '/../../Core/init.php';
require_once __DIR__ . '/../../Includes/Layout/Header.php';

// في index.php الموجود في root
require_once __DIR__ . '/Core/init.php';
require_once __DIR__ . '/Includes/Layout/Header.php';
```

---

## 🔌 إعدادات XAMPP (Localhost)

### ملف الإعدادات: `Core/Config.php`

```php
// إعدادات قاعدة البيانات المحلية (XAMPP)
'database'  => 'autoline_db',      // اسم قاعدة البيانات
'username'  => 'root',             // المستخدم الافتراضي
'password'  => '',                 // كلمة المرور (فارغة في XAMPP)
'host'      => 'localhost',
'port'      => 3306,
```

### إنشاء قاعدة البيانات في XAMPP:

1. افتح phpMyAdmin: `http://localhost/phpmyadmin`
2. أنشئ قاعدة بيانات جديدة باسم `autoline_db`
3. استورد ملف `database.sql` الموجود في المجلد الرئيسي

---

## ⚡ الدوال المساعدة المتاحة

### بعد تضمين `Core/init.php`:

```php
// الحصول على إعدادات
Config::getDatabaseConfig();
Config::getUrls();
Config::getAppConfig();

// الاتصال بقاعدة البيانات
$pdo = Database::getConnection();

// استعلامات سريعة
$cars = Database::fetchAll("SELECT * FROM cars");
$car = Database::fetchOne("SELECT * FROM cars WHERE id = ?", [$id]);

// مساعدات URL
base_url('Modules/Cars/index.php');      // /autoline/Modules/Cars/index.php
asset_url('css/style.css');               // /autoline/Assets/css/style.css

// توجيه
redirect(base_url('index.php'));

// تنظيف الإدخال
clean($user_input);
```

---

## 🌐 المسارات في Linux (Nobara)

### المسارات المطلقة (Absolute):
```php
// صحيح في Linux
require_once '/home/abdelrhman/Desktop/autoline2/autoLine/Core/Config.php';
require_once __DIR__ . '/../../Core/Config.php';

// خاطئ (Windows-style)
require_once 'C:\Users\...\Core\Config.php';
```

### المسارات النسبية (Relative):
```php
// دائماً استخدم __DIR__ للحصول على المجلد الحالي
$base_path = __DIR__ . '/../Core/Config.php';
```

---

## 🔒 إعدادات الجلسة (Sessions)

تم تكوين الجلسات تلقائيًا في `Core/init.php`:

```php
session_set_cookie_params([
    'lifetime' => 0,           // حتى إغلاق المتصفح
    'path'     => '/autoline/',
    'domain'   => '',
    'secure'   => false,       // true في HTTPS
    'httponly' => true,        // لمنع XSS
    'samesite' => 'Lax',
]);
```

---

## 📝 هيكل الصفحات الجديد

### الصفحة الرئيسية (index.php):
```
/index.php                              -> الصفحة الرئيسية (root)
/Modules/Cars/index.php                 -> معرض السيارات
/Modules/Cars/detail.php?id=1          -> تفاصيل سيارة
/Modules/Blog/index.php                 -> المدونة
/Modules/Blog/detail.php?id=1          -> تفاصيل مقال
/Modules/Pages/About.php                -> من نحن
/Modules/Pages/Contact.php              -> تواصل معنا
/Modules/Booking/Flow.php?car_id=1     -> حجز سيارة
/admin/dashboard.php                    -> لوحة الإدارة
```

---

## 🧹 الملفات التي تم إعادة تنظيمها

### الملفات القديمة -> الجديدة:
- `about.php` -> `Modules/Pages/About.php`
- `contact.php` -> `Modules/Pages/Contact.php`
- `vehicles_gallery.php` -> `Modules/Cars/index.php`
- `car_details.php` -> `Modules/Cars/detail.php`
- `blog.php` -> `Modules/Blog/index.php`
- `blog_details.php` -> `Modules/Blog/detail.php`
- `booking_flow.php` -> `Modules/Booking/Flow.php`

---

## 🛡️ أفضل الممارسات

### 1. **استخدام الـ Namespace:**
```php
namespace AutoLine\Modules\Cars;
use AutoLine\Core\Config;
use AutoLine\Core\Database;
```

### 2. **منع الوصول المباشر:**
```php
if (!defined('AUTOLINE_ROOT')) {
    define('AUTOLINE_ROOT', dirname(__DIR__));
}
```

### 3. **استخدام prepared statements:**
```php
$stmt = Database::query("SELECT * FROM cars WHERE id = ?", [$id]);
```

### 4. **تنظيف الإخراج:**
```php
echo htmlspecialchars($car['brand']);
// أو
<?= htmlspecialchars($car['brand']) ?>
```

---

## 🔧 نقل المزيد من الملفات

### إذا أردت نقل الملفات المتبقية:

1. **Blog Module:**
   ```bash
   mv blog.php Modules/Blog/index.php
   mv blog_details.php Modules/Blog/detail.php
   ```

2. **Booking Module:**
   ```bash
   mv booking_flow.php Modules/Booking/Flow.php
   ```

3. **Documentation:**
   ```bash
   mv *.md docs/
   mv *.txt docs/
   ```

---

## ⚠️ ملاحظات مهمة

1. **التحديثات التلقائية:** عند تغيير المسارات، تأكد من تحديث جميع الروابط في:
   - Header.php
   - Footer.php
   - جميع صفحات Modules

2. **التصاريح (Permissions):** في Linux، تأكد من صلاحيات المجلدات:
   ```bash
   chmod 755 Storage/
   chmod 755 Storage/Uploads/
   chmod 755 Storage/Logs/
   ```

3. **.htaccess:** تم الحفاظ على ملف `.htaccess` الموجود لتوجيه Apache.

---

## 🚀 للبدء

1. انسخ الملفات الجديدة إلى المشروع
2. تأكد من وجود قاعدة بيانات `autoline_db` في XAMPP
3. اختبر الصفحات الجديدة:
   ```
   http://localhost/autoline/index.php
   http://localhost/autoline/Modules/Cars/index.php
   http://localhost/autoline/Modules/Pages/About.php
   ```

---

## 📞 دعم

لأي استفسارات أو مشاكل في المسارات، تحقق من:
- ملف `Core/Config.php` للإعدادات
- ملف `Core/init.php` للتهيئة
- متغير `__DIR__` للمسارات النسبية

---

**تم إنشاء هذا الدليل تلقائيًا بواسطة AutoLine Refactoring System**
