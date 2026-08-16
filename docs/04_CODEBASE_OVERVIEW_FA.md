# راهنمای سریع کدبیس و تغییرات

## جریان درخواست

```text
HomeView
  -> /users-roles
  -> UsersRolesView
  -> Pinia users store
  -> fetch('/api/users') + X-Api-Key
  -> Vite proxy
  -> Laravel /api/users
  -> RequireApiKey middleware
  -> UserController@index
  -> User + roles relationship
  -> JSON response
  -> Pinia state
  -> UI
```

## Backend

### `RequireApiKey.php`
کلید مورد انتظار را از `config('services.api.key')` و کلید request را از هدر `X-Api-Key` می‌گیرد. برای مقایسه از `hash_equals` استفاده شده و خطای نامعتبر 401 است.

### `UserController.php`
کاربران را با relation `roles` می‌خواند. Query اصلی فقط ستون‌های `id`, `name`, `email` را انتخاب می‌کند و خروجی role شامل `id`, `name`, `slug`, `is_active` است.

### `routes/api.php`
route جدید `/users` را به controller متصل و middleware `api.key` را اعمال می‌کند.

### `bootstrap/app.php`
Alias مربوط به middleware با نام `api.key` ثبت شده است.

### `config/services.php`
`API_KEY` از environment وارد config می‌شود.

## Frontend

### `stores/users.js`
مسئول request، loading/error state و cache داخل Pinia است. اگر `loaded` یا `loading` true باشد request جدید ارسال نمی‌شود.

### `UsersRolesView.vue`
هنگام mount، `fetchUsers()` را فراخوانی می‌کند و بر اساس state یکی از loading/error/list/empty را نشان می‌دهد. role غیرفعال با کلاس `inactive` نمایش داده می‌شود.

### `vite.config.js`
در local به `127.0.0.1:8000` و در Docker با `API_PROXY_TARGET=http://api:8000` به container بک‌اند proxy می‌کند.

## Docker

- `api`: PHP 8.3 + Composer + SQLite extensions
- `front`: Node 22 + Vite
- دیتابیس development: `/data/database.sqlite` روی volume
- تست: SQLite `:memory:` از طریق `run-tests.sh`

## دستورهای روزمره

```powershell
docker compose up -d
docker compose ps
docker compose logs api
docker compose logs front
docker compose exec api ./run-tests.sh
docker compose down
```
