# ActFit API — Laravel Setup Guide

## 📁 الملفات اللي هتحطها في مشروع Laravel بتاعك

```
routes/
  api.php                          ← الـ Routes كلها

app/Http/Controllers/
  AuthController.php               ← Register / Login / Logout / Me
  MealController.php               ← عرض الوجبات
  CartController.php               ← العربية
  OrderController.php              ← الطلبات
  PlanController.php               ← الباقات
  SubscriptionController.php       ← الاشتراكات
  PaymentController.php            ← الدفع

app/Models/
  (استخرج كل class من AllModels.php وحطها في ملف منفصل)
  User.php / Meal.php / Cart.php / CartItem.php
  Order.php / OrderItem.php / Plan.php / Subscription.php / Payment.php
```

---

## ⚙️ خطوات التشغيل

### 1. تثبيت Laravel Sanctum (للـ Authentication)
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. إعداد قاعدة البيانات في .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitness_app
DB_USERNAME=root
DB_PASSWORD=
```

### 3. استيراد قاعدة البيانات
```bash
mysql -u root -p fitness_app < DataBase.sql
mysql -u root -p fitness_app < sample-data.sql
```

### 4. إضافة Sanctum للـ User Model
في `app/Models/User.php` — تأكد إن فيه:
```php
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable {
    use HasApiTokens;
    ...
}
```

### 5. في `config/auth.php` — تأكد إن الـ API guard شغال بـ sanctum
```php
'guards' => [
    'api' => [
        'driver'   => 'sanctum',
        'provider' => 'users',
    ],
],
```

### 6. تشغيل المشروع
```bash
php artisan serve
```
الـ API هتكون على: `http://localhost:8000/api`

---

## 🔗 كل الـ Endpoints

### Authentication
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| POST | /api/register | تسجيل حساب جديد | ❌ |
| POST | /api/login | تسجيل الدخول | ❌ |
| POST | /api/logout | تسجيل الخروج | ✅ |
| GET  | /api/me | بيانات المستخدم الحالي | ✅ |

### Meals
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| GET | /api/meals | كل الوجبات | ❌ |
| GET | /api/meals/{id} | وجبة معينة | ❌ |

### Cart
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| GET    | /api/cart | عرض العربية | ✅ |
| POST   | /api/cart | إضافة وجبة | ✅ |
| PUT    | /api/cart/{itemId} | تعديل الكمية | ✅ |
| DELETE | /api/cart/{itemId} | حذف وجبة | ✅ |
| DELETE | /api/cart | مسح العربية كلها | ✅ |

### Orders
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| GET  | /api/orders | كل طلباتي | ✅ |
| GET  | /api/orders/{id} | تفاصيل طلب | ✅ |
| POST | /api/orders | إنشاء طلب من العربية | ✅ |

### Plans
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| GET | /api/plans | كل الباقات | ❌ |
| GET | /api/plans/{id} | باقة معينة | ❌ |

### Subscriptions
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| GET  | /api/subscriptions | اشتراكاتي | ✅ |
| POST | /api/subscriptions | اشتراك في باقة | ✅ |

### Payments
| Method | URL | الوصف | Auth مطلوب؟ |
|--------|-----|-------|-------------|
| POST | /api/payments | دفع طلب | ✅ |

---

## 📤 أمثلة على الـ Requests

### Register
```json
POST /api/register
{
  "name": "Ahmed Ali",
  "email": "ahmed@gmail.com",
  "password": "123456"
}
```

### Login
```json
POST /api/login
{
  "email": "ahmed@gmail.com",
  "password": "123456"
}
// Response: { "token": "1|abc123..." }
```

### استخدام الـ Token في أي Request محتاج Auth
```
Header: Authorization: Bearer 1|abc123...
```

### إضافة وجبة للعربية
```json
POST /api/cart
Headers: Authorization: Bearer {token}
{
  "meal_id": 1,
  "quantity": 2
}
```

### إنشاء طلب (Checkout)
```json
POST /api/orders
Headers: Authorization: Bearer {token}
// مش محتاج body — بياخد من العربية تلقائياً
```

### دفع
```json
POST /api/payments
Headers: Authorization: Bearer {token}
{
  "order_id": 1,
  "payment_method": "Visa"
}
// payment_method: Visa أو Cash أو Wallet
```

### اشتراك في باقة
```json
POST /api/subscriptions
Headers: Authorization: Bearer {token}
{
  "plan_id": 2
}
```

---

## ⚠️ ملاحظة مهمة على Models

ملف `AllModels.php` فيه كل الـ Models مع بعض للمراجعة.
لازم تفصّلهم — كل class في ملف منفصل في `app/Models/`.
مثلاً: المحتوى اللي بدأ بـ `class Meal` → احفظه في `app/Models/Meal.php`.
