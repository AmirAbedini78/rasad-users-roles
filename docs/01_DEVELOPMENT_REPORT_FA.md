# گزارش توسعه - تسک لیست کاربران و نقش‌ها

## هدف
پیاده‌سازی قابلیت نمایش فهرست کاربران و نقش‌های آن‌ها روی پروژه موجود، بدون بازنویسی ساختار اصلی.

## تغییرات Backend

1. مسیر `GET /api/users` اضافه شد.
2. Middleware جدید `RequireApiKey` برای بررسی هدر `X-Api-Key` اضافه شد.
3. کلید API از `config/services.php` و متغیر محیطی `API_KEY` خوانده می‌شود.
4. در صورت نبودن یا اشتباه بودن کلید، پاسخ `401 Unauthorized` برگردانده می‌شود.
5. `UserController@index` اضافه شد تا کاربران را به همراه roleها برگرداند.
6. فقط `id`, `name`, `email` و اطلاعات role برگردانده می‌شود و password در پاسخ وجود ندارد.
7. وضعیت `is_active` روی رابطه user-role در خروجی حفظ شده است.
8. Feature Test برای حالت‌های بدون کلید، کلید اشتباه و پاسخ موفق اضافه شد.

## تغییرات Frontend

1. Store موجود `users.js` تکمیل شد.
2. stateهای `loading`, `error`, `loaded` اضافه شدند.
3. اکشن `fetchUsers()` داده را از `/api/users` دریافت می‌کند.
4. هدر `X-Api-Key` از `VITE_API_KEY` خوانده می‌شود.
5. بعد از load موفق، Pinia داده را نگه می‌دارد و هنگام برگشت مجدد به صفحه request تکراری ارسال نمی‌شود.
6. `UsersRolesView.vue` تکمیل شد و نام، ایمیل و roleها را نمایش می‌دهد.
7. role غیرفعال به‌صورت بصری متفاوت و با برچسب «غیرفعال» نمایش داده می‌شود.
8. Loading، خطای 401 و empty state ساده اضافه شد.
9. Vite proxy برای اجرای محلی و Docker قابل تنظیم شد.

## Docker / Dev Environment

1. Dockerfile برای API و Front اضافه شد.
2. `docker-compose.yml` برای اجرای همزمان دو سرویس اضافه شد.
3. SQLite روی Docker volume نگهداری می‌شود.
4. entrypoint بک‌اند migration و seed را در startup اجرا می‌کند.
5. مشکل مسیر SQLite در Docker اصلاح شد و مسیر نهایی `/data/database.sqlite` است.
6. اسکریپت `run-tests.sh` اضافه شد تا PHPUnit با SQLite `:memory:` اجرا شود و دیتابیس development تغییر نکند.

## فایل‌های کلیدی توسعه‌یافته

- `api/app/Http/Controllers/Api/UserController.php`
- `api/app/Http/Middleware/RequireApiKey.php`
- `api/bootstrap/app.php`
- `api/config/services.php`
- `api/routes/api.php`
- `api/tests/Feature/UsersApiTest.php`
- `front/src/stores/users.js`
- `front/src/views/UsersRolesView.vue`
- `front/vite.config.js`
- `docker-compose.yml`
- `api/Dockerfile`
- `front/Dockerfile`
- `api/docker-entrypoint.sh`
- `api/run-tests.sh`

## مواردی که عمداً تغییر نکردند

- ساختار کلی Laravel/Vue
- متن لینک صفحه اصلی
- عنوان بالای صفحه موجود
- مدل authentication موجود
- Sanctum برای این endpoint استفاده نشده است
