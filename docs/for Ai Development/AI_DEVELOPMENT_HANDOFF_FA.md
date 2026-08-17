# Rasad — AI Development Handoff & Continuation Guide

> **نوع سند:** داخلی / مخصوص ادامه توسعه با کمک AI یا تحویل Context به توسعه‌دهنده بعدی  
> **هدف:** جلوگیری از بازخوانی صفر تا صد پروژه در هر جلسه و حفظ ساختار، قراردادهای فنی، تست‌ها و تصمیم‌های فعلی.

---

## 1) این سند چطور استفاده شود؟

در هر جلسه جدید توسعه با AI، ترتیب پیشنهادی منابع این است:

1. **Repository فعلی پروژه** — منبع اصلی و نهایی حقیقت (Source of Truth).
2. این فایل: `AI_DEVELOPMENT_HANDOFF_FA.md`.
3. شرح کار اولیه کارفرما در `docs/source/شرح کار.pdf` در صورت موجود بودن.
4. گزارش‌های توسعه و تست در `docs/`.
5. `git log` و `git diff` برای تشخیص دقیق تغییرات تاریخی.

اگر بین این سند و سورس فعلی تناقضی وجود داشت، **کد فعلی Repository اولویت دارد** و سند باید به‌روزرسانی شود.

---

## 2) Snapshot فعلی پروژه

پروژه Rasad دو بخش اصلی دارد:

- `api/` → Laravel Backend
- `front/` → Vue 3 Frontend + Pinia + Vite

قابلیت توسعه‌داده‌شده در این مرحله:

**نمایش لیست کاربران به همراه نقش‌ها، دریافت از API محافظت‌شده با API Key، نمایش نقش فعال/غیرفعال و Cache داده در Pinia.**

محیط اجرای قابل تکرار نیز با Docker Compose اضافه شده است.

### وضعیت فعلی تست‌شده

- API بدون کلید → `401` ✅
- API با کلید اشتباه → `401` ✅
- API با کلید معتبر → `200` ✅
- کاربران و Roleها بازگردانده می‌شوند ✅
- Role غیرفعال حذف نمی‌شود و `is_active=false` دارد ✅
- Password در Response وجود ندارد ✅
- `UsersApiTest` → **3 tests / 9 assertions PASS** ✅
- Full Backend Suite → **6 tests / 14 assertions PASS** ✅
- دیتابیس Test از Development ایزوله شده ✅
- صفحه Front کاربران، Loading، Error و Pinia Cache تست شده‌اند ✅

---

## 3) اصل مهم معماری

این پروژه **بازنویسی نشده** و توسعه باید روی همان ساختار موجود ادامه پیدا کند.

قواعد معماری فعلی:

- Laravel در `api/` باقی بماند.
- Vue در `front/` باقی بماند.
- Pinia Store موجود استفاده شود؛ برای Feature کوچک Store/Layer غیرضروری جدید ساخته نشود.
- Modelها، Migrationها و Relationهای فعلی بدون نیاز واقعی Refactor نشوند.
- برای هر Feature جدید ابتدا Pattern موجود پروژه بررسی شود.
- از معماری‌های اضافه مثل Repository/DTO/Service/UseCase فقط وقتی Requirement واقعی وجود دارد استفاده شود.
- تغییرات کوچک با کمترین سطح Abstraction انجام شوند.

### چیزهایی که در این Feature عمداً دست‌نخورده ماندند

- `api/app/Http/Controllers/Api/AuthController.php`
- Modelهای `User`, `Role`, `UserRole`
- Migrationهای موجود
- Seeder اصلی پروژه
- Router موجود Front
- `front/src/views/HomeView.vue`
- متن لینک صفحه اصلی
- Route موجود `/users-roles`

---

## 4) نقشه فایل‌های مهم پروژه

```text
.
├── api/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/
│   │   │   │       ├── AuthController.php
│   │   │   │       └── UserController.php        # اضافه‌شده
│   │   │   └── Middleware/
│   │   │       ├── ForceJsonResponse.php
│   │   │       └── RequireApiKey.php             # اضافه‌شده
│   │   └── Models/
│   │       ├── User.php
│   │       ├── Role.php
│   │       └── UserRole.php
│   ├── bootstrap/app.php                         # alias middleware تکمیل شده
│   ├── config/services.php                       # API key config
│   ├── routes/api.php                            # GET /api/users
│   ├── tests/Feature/UsersApiTest.php            # اضافه‌شده
│   ├── phpunit.xml
│   ├── Dockerfile
│   ├── docker-entrypoint.sh
│   └── run-tests.sh                              # اجرای امن تست‌ها
│
├── front/
│   ├── src/
│   │   ├── router/index.js
│   │   ├── stores/users.js                       # تکمیل شده
│   │   └── views/
│   │       ├── HomeView.vue
│   │       └── UsersRolesView.vue                # تکمیل شده
│   ├── vite.config.js                            # Proxy قابل تنظیم
│   └── Dockerfile
│
├── docker/
│   ├── api.env.example
│   └── front.env.example
├── docker-compose.yml
├── docs/
└── README.md
```

> فایل‌های `.env` واقعی، API Keyها، Secretها و داده‌های محیطی نباید در Git Commit شوند.

---

## 5) Data Model موجود

### User

مدل کاربر از قبل در پروژه وجود داشته است.

### Role

مدل Role از قبل وجود داشته است.

### User ↔ Role

ارتباط User و Role از طریق relation موجود پروژه و Pivot برقرار است. فیلد مهم Pivot:

```text
is_active
```

این فیلد وضعیت Role را برای همان User مشخص می‌کند.

**قاعده:** Role غیرفعال نباید از API حذف شود؛ باید با `is_active: false` بازگردد.

---

## 6) Backend — جریان Request

جریان فعلی Endpoint کاربران:

```text
GET /api/users
      ↓
RequireApiKey Middleware
      ↓
UserController@index
      ↓
User::with('roles')
      ↓
JSON Response
```

### Endpoint

```http
GET /api/users
```

### Header الزامی

```http
X-Api-Key: <API_KEY>
```

### رفتار Security

- Header وجود ندارد → `401 Unauthorized`
- Header اشتباه است → `401 Unauthorized`
- API Key تنظیم نشده است → `401 Unauthorized`
- کلید معتبر است → Request ادامه پیدا می‌کند

API Key از Environment خوانده می‌شود و داخل Controller یا Component هاردکد نشده است.

### Response Contract

```json
{
  "data": [
    {
      "id": 1,
      "name": "...",
      "email": "...",
      "roles": [
        {
          "id": 1,
          "name": "...",
          "slug": "...",
          "is_active": true
        }
      ]
    }
  ]
}
```

### Contractهای غیرقابل شکستن بدون هماهنگی

- `data` آرایه کاربران است.
- User شامل `id`, `name`, `email`, `roles` است.
- Role شامل `id`, `name`, `slug`, `is_active` است.
- `password` نباید در Response اضافه شود.
- Role غیرفعال باید در Response باقی بماند.

---

## 7) Frontend — جریان State و UI

جریان فعلی:

```text
HomeView
   ↓ click
/users-roles
   ↓
UsersRolesView.vue
   ↓ onMounted
usersStore.fetchUsers()
   ↓
GET /api/users
   ↓
Pinia state
   ↓
Render users + roles
```

### Store فعلی

فایل:

```text
front/src/stores/users.js
```

Stateهای مهم:

```text
users
loading
error
loaded
```

### رفتار Cache

`fetchUsers()` در صورتی که `loaded === true` باشد دوباره API را صدا نمی‌زند.

این Cache فقط در حافظه فعلی Pinia است؛ Refresh کامل Browser باعث شروع مجدد Application State می‌شود. این رفتار در Scope فعلی عمدی و مطابق Requirement است.

### Error Handling

- `401` → پیام عدم دسترسی
- سایر خطاهای HTTP → پیام خطای دریافت کاربران
- خطا نباید باعث Crash صفحه شود

### UI

صفحه ساده و کم‌تغییر نگه داشته شده است. Role غیرفعال با Style متفاوت و عبارت «غیرفعال» مشخص می‌شود.

---

## 8) Docker Topology

```text
Browser
  │
  ├── localhost:5173
  ▼
front (Vue/Vite)
  │ /api proxy
  ▼
api:8000 (Laravel)
  │
  ▼
SQLite persistent volume
```

### سرویس‌ها

- `api`
- `front`

### Ports

- Front: `5173`
- API: `8000`

### Vite Proxy

در Local بدون Docker، Target پیش‌فرض API:

```text
http://127.0.0.1:8000
```

در Docker مقدار `API_PROXY_TARGET` باید به سرویس API اشاره کند:

```text
http://api:8000
```

---

## 9) Environment و Secret Management

نام متغیرهای مهم:

### Backend

```env
API_KEY=<secret>
DB_CONNECTION=sqlite
DB_DATABASE=<path>
```

### Frontend

```env
VITE_API_KEY=<secret>
```

### قواعد

- Secret واقعی را در Document، Commit، Comment یا Screenshot عمومی قرار نده.
- `.env` واقعی Commit نشود.
- فقط `.env.example` و `*.env.example` به Repository بروند.
- اگر API Key تغییر کرد، Backend و Frontend باید هماهنگ Update شوند.

---

## 10) تست‌ها و قانون مهم Test Database

### تست اصلی Feature

```bash
docker compose exec api ./run-tests.sh --filter=UsersApiTest
```

Expected:

```text
3 tests
9 assertions
PASS
```

### کل Backend Suite

```bash
docker compose exec api ./run-tests.sh
```

Expected فعلی:

```text
6 tests
14 assertions
PASS
```

### چرا `run-tests.sh` مهم است؟

در جریان Dockerization مشخص شد اجرای Test بدون Override قطعی Environment می‌تواند `RefreshDatabase` را روی SQLite Development اجرا کند.

برای جلوگیری از این موضوع، `run-tests.sh` قبل از اجرای PHPUnit این Environmentها را Set می‌کند:

```text
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
DB_URL=
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

**قاعده مهم برای AI بعدی:** در محیط Docker فعلی برای Test از `./run-tests.sh` استفاده کن. اگر روش تست تغییر داده شد، قبل و بعد تست تعداد/داده Development DB را بررسی کن تا Test DB واقعاً ایزوله باشد.

---

## 11) تست دستی استاندارد بعد از هر تغییر مرتبط

### Backend

```bash
curl -i http://localhost:8000/api/users
```

Expected: `401`

```bash
curl -i -H "X-Api-Key: wrong-value" http://localhost:8000/api/users
```

Expected: `401`

```bash
curl -i -H "X-Api-Key: <VALID_KEY>" http://localhost:8000/api/users
```

Expected: `200`

سپس بررسی شود:

- `password` وجود ندارد.
- Role غیرفعال وجود دارد.
- `is_active` صحیح است.

### Frontend

1. `http://localhost:5173` باز شود.
2. لینک موجود کاربران باز شود.
3. صفحه `/users-roles` داده را نمایش دهد.
4. Network فقط Request مورد انتظار را نشان دهد.
5. برگشت Home و ورود مجدد، بدون Refresh، نباید Request دوم ایجاد کند.
6. Loading و Error State بررسی شوند.

---

## 12) فایل‌هایی که در Feature فعلی اضافه یا تغییر داده شدند

### Core Feature — Backend

**اضافه‌شده:**

- `api/app/Http/Controllers/Api/UserController.php`
- `api/app/Http/Middleware/RequireApiKey.php`
- `api/tests/Feature/UsersApiTest.php`

**تغییر داده‌شده:**

- `api/bootstrap/app.php`
- `api/config/services.php`
- `api/routes/api.php`
- `api/phpunit.xml`
- `api/.env.example`

### Core Feature — Frontend

**تغییر داده‌شده:**

- `front/src/stores/users.js`
- `front/src/views/UsersRolesView.vue`
- `front/vite.config.js`
- `front/.env.example`

### Tooling / Reproducibility

**اضافه‌شده:**

- `docker-compose.yml`
- `api/Dockerfile`
- `front/Dockerfile`
- `api/docker-entrypoint.sh`
- `api/run-tests.sh`
- `.dockerignore`ها
- `docker/*.env.example`
- مستندات Docker و Handoff

---

## 13) قواعد اجباری برای AI یا توسعه‌دهنده بعدی

قبل از هر تغییر این قوانین را رعایت کن:

1. **اول سورس موجود را بخوان؛ از روی حدس فایل یا معماری جدید نساز.**
2. ساختار فعلی Laravel/Vue را حفظ کن مگر Requirement خلاف آن باشد.
3. Featureهای کوچک را Over-engineer نکن.
4. Route `/users-roles` را بدون Requirement تغییر نده.
5. متن Link و عنوان‌های موجود را بدون Requirement تغییر نده.
6. API Key یا Secret را هاردکد نکن.
7. Password یا اطلاعات حساس User را وارد Response نکن.
8. Role غیرفعال را Filter نکن مگر Requirement جدید صریحاً بخواهد.
9. Pinia Store موجود را در اولویت قرار بده.
10. Commentهایی مثل «AI generated»، توضیح فرآیند AI یا Commentهای آموزشی غیرضروری داخل Production Code نگذار.
11. Naming و Formatting را با فایل‌های هم‌جوار هماهنگ نگه دار.
12. تغییرات unrelated را همراه Feature جدید انجام نده.
13. قبل از Commit، `git diff` را بررسی کن.
14. Secretها و `.env` واقعی را Commit نکن.
15. بعد از هر تغییر Backend، Tests را اجرا کن.
16. بعد از هر تغییر Front، UI و Network behavior را بررسی کن.
17. اگر Test tooling را تغییر می‌دهی، Isolation دیتابیس Test را دوباره ثابت کن.
18. اگر Contract API تغییر می‌کند، Backend Test و Front Store را همزمان Update کن.

---

## 14) Workflow پیشنهادی برای هر Task جدید با AI

### Phase A — Context

AI باید ابتدا این‌ها را بخواند:

```text
README.md
AI_DEVELOPMENT_HANDOFF_FA.md
مرتبط‌ترین فایل‌های api/front
git log --oneline
git status
```

در صورت نیاز:

```text
docs/01_DEVELOPMENT_REPORT_FA.md
docs/02_TEST_REPORT_FA.md
شرح کار جدید کارفرما
```

### Phase B — تحلیل قبل از Edit

AI باید مشخص کند:

- Requirement دقیق چیست؟
- کدام فایل‌ها واقعاً نیاز به Edit دارند؟
- آیا Pattern مشابه در پروژه وجود دارد؟
- چه Contract یا Testی ممکن است بشکند؟
- آیا DB migration لازم است یا خیر؟

### Phase C — Implementation

- کمترین تعداد فایل لازم تغییر کند.
- تغییر unrelated انجام نشود.
- Secret هاردکد نشود.
- Existing style رعایت شود.

### Phase D — Verification

حداقل:

```bash
docker compose up -d --build
docker compose exec api ./run-tests.sh
```

و Test دستی Feature مربوطه.

### Phase E — Handoff

بعد از Task:

- `README.md` فقط اگر رفتار عمومی پروژه تغییر کرده Update شود.
- این AI Handoff در صورت تغییر معماری/Contract Update شود.
- Test report Update شود.
- Changed files و دلیل هر تغییر ثبت شود.
- Commitها موضوعی و قابل بررسی باشند.

---

## 15) Roadmap برای حفظ Context در توسعه‌های آینده

این Roadmap Requirement تجاری جدید ایجاد نمی‌کند؛ فقط روش ادامه امن توسعه است.

### Step 1 — Baseline Verification

قبل از Feature بعدی:

- Repo clean باشد.
- Containers سالم بالا بیایند.
- Full tests PASS باشد.
- `/api/users` و `/users-roles` Regression نداشته باشند.

### Step 2 — Requirement Isolation

هر درخواست جدید کارفرما به این دسته‌ها تفکیک شود:

- Backend-only
- Frontend-only
- Full-stack
- Database/schema
- Infrastructure/Docker

### Step 3 — Impact Map

قبل از Edit، AI یک Impact Map کوتاه بسازد:

```text
Requirement
→ files touched
→ API/DB impact
→ frontend state impact
→ tests required
```

### Step 4 — Small Commits

Commitها ترجیحاً به تفکیک:

```text
feat(api): ...
feat(front): ...
test: ...
chore(docker): ...
docs: ...
```

### Step 5 — Regression Gate

هیچ Feature کامل تلقی نشود مگر:

- Tests پاس شوند.
- Existing users/roles behavior شکسته نشده باشد.
- Secret جدید Commit نشده باشد.
- Git diff فقط تغییرات مرتبط را داشته باشد.

---

## 16) Prompt آماده برای شروع جلسه جدید با AI

متن زیر را می‌توان همراه Repository و این سند به AI بعدی داد:

```text
این Repository پروژه Rasad است. قبل از هر Edit، ابتدا README.md و
AI_DEVELOPMENT_HANDOFF_FA.md را کامل بخوان و سپس فایل‌های مرتبط با Task را بررسی کن.

ساختار اصلی پروژه را تغییر نده و معماری جدید غیرضروری اضافه نکن. Backend در api/ با
Laravel و Frontend در front/ با Vue 3 + Pinia + Vite است.

قابلیت users/roles در حال حاضر تست‌شده و پایدار است. API آن GET /api/users است و با
X-Api-Key محافظت می‌شود. نقش‌های غیرفعال باید در Response باقی بمانند و Password نباید
ارسال شود. Front داده را در Pinia نگه می‌دارد و در همان session از Request تکراری جلوگیری می‌کند.

برای تست Backend داخل Docker از ./run-tests.sh استفاده کن تا دیتابیس تست از Development
ایزوله بماند. هیچ Secret یا .env واقعی را Commit نکن.

قبل از تغییر، به من بگو Requirement روی چه فایل‌هایی اثر دارد. بعد فقط فایل‌های لازم را
Edit کن، git diff را بررسی کن و تست‌های مرتبط را اجرا کن. Comment یا متن غیرضروری درباره AI
داخل Production Code اضافه نکن.

Task جدید:
[شرح درخواست جدید کارفرما اینجا قرار گیرد]
```

---

## 17) Checklist بروزرسانی این سند

این فایل باید Update شود اگر یکی از موارد زیر تغییر کرد:

- Framework یا ساختار پوشه‌ها
- API Contract
- Authentication / Authorization mechanism
- Database schema مرتبط
- State management Frontend
- Docker topology
- روش اجرای Tests
- Environment variableهای ضروری
- Routeهای اصلی Feature
- Known issue مهم یا Workaround جدید

اگر فقط Style یا متن UI کوچک تغییر کرد، معمولاً نیازی به Update این سند نیست.

---

## 18) خلاصه یک‌دقیقه‌ای برای AI بعدی

```text
Project: Rasad
Backend: Laravel → api/
Frontend: Vue 3 + Pinia + Vite → front/
Feature stable: users + roles
API: GET /api/users
Auth for endpoint: X-Api-Key from environment
Inactive roles: must be returned with is_active=false
Password: never return
Frontend route: /users-roles
State: existing Pinia users store
Cache: in-memory for current app session
Docker: api + front + persistent SQLite
Tests in Docker: ./run-tests.sh
Current backend test status: 6 tests / 14 assertions PASS
Core rule: preserve existing project structure and make minimal scoped changes
```
