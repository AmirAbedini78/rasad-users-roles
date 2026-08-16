# گزارش تست

## تست‌های API

| تست | انتظار | نتیجه |
|---|---|---|
| `GET /api` | 200 | PASS |
| `/api/users` بدون کلید | 401 | PASS |
| `/api/users` با کلید اشتباه | 401 | PASS |
| `/api/users` با کلید معتبر | 200 + data | PASS |
| عدم وجود password در response | password absent | PASS |
| role فعال | `is_active=true` | PASS |
| role غیرفعال | `is_active=false` | PASS |

## تست‌های PHPUnit

Feature test اختصاصی:

```text
OK (3 tests, 9 assertions)
```

کل مجموعه تست‌ها:

```text
OK (6 tests, 14 assertions)
```

## ایزوله‌سازی دیتابیس تست

در جریان تست مشخص شد اجرای مستقیم `php artisan test` داخل کانتینر می‌تواند environment دیتابیس development را به تست منتقل کند. برای جلوگیری از reset شدن SQLite اصلی، اسکریپت `run-tests.sh` اضافه شد که قبل از boot PHPUnit متغیرهای زیر را تنظیم می‌کند:

- `APP_ENV=testing`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- cache/session آرایه‌ای و queue sync

اعتبارسنجی نهایی:

```text
Development users before tests: 4
Full test suite: OK (6 tests, 14 assertions)
Development users after tests: 4
```

## تست‌های دستی Frontend

تست‌های زیر روی اجرای Docker انجام و تایید شدند:

- باز شدن صفحه اصلی و مسیر `/users-roles`
- نمایش نام و ایمیل کاربران
- نمایش roleهای فعال و غیرفعال
- ارسال `X-Api-Key` در request
- نمایش Loading
- نمایش پیام ساده در خطای 401
- Pinia cache: رفتن به Home و بازگشت مجدد بدون request دوم در همان session
- production build فرانت

## شواهد

لاگ‌های sanitized در `docs/logs/` قرار دارند. نسخه خام لاگ‌ها در پکیج تحویل مستقیم نگهداری شده و به دلیل داشتن API key تستی برای Git عمومی مناسب نیست.
