# اجرای پروژه Rasad با Docker

## سرویس‌ها

- Laravel API: http://localhost:8000
- Vue/Vite Frontend: http://localhost:5173
- SQLite: داخل volume با نام `rasad_sqlite`

## اجرای اولیه

از پوشه اصلی پروژه، جایی که `docker-compose.yml` قرار دارد:

```powershell
docker version
docker compose version
docker compose build --no-cache
docker compose up -d
docker compose ps
```

برای دیدن لاگ‌ها:

```powershell
docker compose logs -f
```

یا جداگانه:

```powershell
docker compose logs -f api
docker compose logs -f front
```

## تست API

بدون کلید باید 401 باشد:

```powershell
curl.exe -i http://localhost:8000/api/users
```

با کلید اشتباه باید 401 باشد:

```powershell
curl.exe -i -H "X-Api-Key: wrong-secret" http://localhost:8000/api/users
```

با کلید درست باید 200 و JSON کاربران را برگرداند:

```powershell
curl.exe -i -H "X-Api-Key: rasad-local-secret" http://localhost:8000/api/users
```

## تست Laravel داخل کانتینر

```powershell
docker compose exec api php artisan route:list --path=api/users
docker compose exec api php artisan test --filter=UsersApiTest
docker compose exec api php artisan test
```

## تست فرانت

مرورگر را باز کنید:

```text
http://localhost:5173
```

روی «لیست کاربران به همراه نقش ها» کلیک کنید. صفحه `/users-roles` باید نام، ایمیل و نقش‌ها را نمایش دهد و نقش غیرفعال را مشخص کند.

برای تست Pinia در DevTools > Network، بار اول ورود به صفحه باید یک درخواست `/api/users` دیده شود. به Home برگردید و دوباره وارد صفحه شوید؛ بدون refresh کامل مرورگر نباید درخواست دوم ارسال شود.

## خاموش کردن

```powershell
docker compose down
```

برای خاموش کردن و حذف دیتابیس Docker:

```powershell
docker compose down -v
```

## بازسازی پس از تغییر کد

```powershell
docker compose down
docker compose up -d --build
```

## کلید API

کلید بک‌اند در `docker/api.env` و کلید فرانت در `docker/front.env` قرار دارد. برای تست 401 می‌توان کلید فرانت را موقتاً اشتباه کرد و سرویس front را recreate کرد:

```powershell
docker compose up -d --force-recreate front
```

## SQLite path
The Docker API uses `/data/database.sqlite`. The same value is written into Laravel's `.env` at container startup so migrations and HTTP requests always use the same SQLite file.
