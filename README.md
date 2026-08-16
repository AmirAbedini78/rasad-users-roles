# Rasad — Users & Roles Module

پیاده‌سازی و تکمیل ماژول **نمایش کاربران به همراه نقش‌ها** در پروژه موجود Rasad، با حفظ ساختار فعلی Backend و Frontend و بدون بازنویسی معماری اصلی پروژه.

این توسعه شامل یک API محافظت‌شده با API Key، نمایش کاربران و نقش‌های فعال/غیرفعال در Vue، کش داده در Pinia، تست‌های خودکار Backend و محیط اجرای Docker شده است.

---

## Overview

پروژه از دو بخش اصلی تشکیل شده است:

- **Backend:** Laravel
- **Frontend:** Vue 3 + Pinia + Vite

در این توسعه، مسیر موجود پروژه برای نمایش کاربران تکمیل شده و ارتباط Frontend و Backend از طریق API برقرار شده است.

### جریان کلی

```mermaid
flowchart LR
    A[Home View] --> B[/users-roles]
    B --> C[UsersRolesView]
    C --> D[Pinia Users Store]
    D --> E[GET /api/users]
    E --> F[API Key Middleware]
    F --> G[UserController]
    G --> H[(Users / Roles)]
    H --> G
    G --> D
    D --> C
```

---

# Features Implemented

## Backend

### Protected Users API

Endpoint جدید:

```http
GET /api/users
```

این Endpoint فقط در صورت ارسال API Key معتبر پاسخ می‌دهد.

Header مورد نیاز:

```http
X-Api-Key: <API_KEY>
```

رفتار API:

| وضعیت | Response |
|---|---:|
| بدون API Key | `401 Unauthorized` |
| API Key اشتباه | `401 Unauthorized` |
| API Key معتبر | `200 OK` |

---

## API Response

نمونه خروجی:

```json
{
  "data": [
    {
      "id": 1,
      "name": "کاربر مدیر",
      "email": "admin@rasad.test",
      "roles": [
        {
          "id": 1,
          "name": "مدیر",
          "slug": "admin",
          "is_active": true
        }
      ]
    }
  ]
}
```

نکات:

- فیلد `password` در خروجی API ارسال نمی‌شود.
- نقش‌های غیرفعال از خروجی حذف نمی‌شوند.
- وضعیت هر نقش با فیلد `is_active` مشخص است.

---

# Frontend

صفحه موجود:

```text
/users-roles
```

تکمیل شده و اطلاعات کاربران را از API دریافت می‌کند.

اطلاعات نمایش داده‌شده:

- نام کاربر
- ایمیل
- نقش یا نقش‌ها
- وضعیت نقش‌های غیرفعال

---

## Pinia Cache

اطلاعات کاربران داخل Pinia Store نگهداری می‌شود.

رفتار Store:

```text
First Visit
    ↓
GET /api/users
    ↓
Store in Pinia
    ↓
Render Users
```

در مراجعه مجدد به صفحه در همان Session:

```text
Users Page
    ↓
Home
    ↓
Users Page
    ↓
Use Pinia Cache
```

بنابراین تا زمانی که اپلیکیشن Refresh نشده باشد، درخواست API غیرضروری دوباره ارسال نمی‌شود.

---

# Error & Loading States

Frontend وضعیت‌های زیر را مدیریت می‌کند:

### Loading

هنگام دریافت اطلاعات:

```text
در حال دریافت اطلاعات...
```

### Unauthorized / API Error

در صورت خطای API مانند `401`، پیام خطای ساده در UI نمایش داده می‌شود و صفحه Crash نمی‌کند.

---

# Project Structure

```text
.
├── api/
│   ├── app/
│   │   └── Http/
│   │       ├── Controllers/
│   │       │   └── Api/
│   │       │       └── UserController.php
│   │       └── Middleware/
│   │           └── RequireApiKey.php
│   │
│   ├── config/
│   ├── database/
│   ├── routes/
│   │   └── api.php
│   ├── tests/
│   │   └── Feature/
│   │       └── UsersApiTest.php
│   │
│   ├── Dockerfile
│   ├── docker-entrypoint.sh
│   └── run-tests.sh
│
├── front/
│   ├── src/
│   │   ├── stores/
│   │   │   └── users.js
│   │   └── views/
│   │       └── UsersRolesView.vue
│   │
│   ├── Dockerfile
│   └── vite.config.js
│
├── docker/
├── docs/
├── docker-compose.yml
└── README.md
```

---

# Main Files Changed / Added

## Backend

### `api/app/Http/Controllers/Api/UserController.php`

مسئول دریافت کاربران و Roleهای مرتبط و تولید Response API.

---

### `api/app/Http/Middleware/RequireApiKey.php`

اعتبارسنجی Header:

```http
X-Api-Key
```

در صورت نبودن یا معتبر نبودن کلید، درخواست با `401` متوقف می‌شود.

---

### `api/routes/api.php`

ثبت Endpoint:

```http
GET /api/users
```

---

### `api/config/services.php`

دریافت API Key از Environment به‌جای Hardcode کردن مقدار داخل Controller یا Middleware.

---

### `api/tests/Feature/UsersApiTest.php`

پوشش تست‌های اصلی API:

- Missing API Key
- Invalid API Key
- Valid API Key
- Active Role
- Inactive Role
- عدم ارسال Password

---

## Frontend

### `front/src/stores/users.js`

تکمیل Pinia Store برای:

- دریافت کاربران
- Loading State
- Error State
- Cache State
- جلوگیری از Request تکراری

---

### `front/src/views/UsersRolesView.vue`

تکمیل UI صفحه کاربران و نمایش Roleها.

---

### `front/vite.config.js`

تنظیم Proxy برای ارتباط Frontend و Backend در محیط Local و Docker.

---

# Docker Environment

برای اجرای یکپارچه پروژه، محیط Docker نیز در پروژه موجود است.

سرویس‌ها:

```text
front
api
```

Backend روی:

```text
http://localhost:8000
```

Frontend روی:

```text
http://localhost:5173
```

---

# Run with Docker

## Requirements

فقط موارد زیر لازم است:

- Docker Desktop
- Docker Compose

بررسی:

```bash
docker version
docker compose version
```

---

## Start Project

در Root پروژه:

```bash
docker compose up -d --build
```

بررسی Containerها:

```bash
docker compose ps
```

---

## Backend Logs

```bash
docker compose logs api
```

---

## Frontend Logs

```bash
docker compose logs front
```

---

# Database

محیط فعلی از SQLite استفاده می‌کند.

در Startup کانتینر API:

1. Database آماده می‌شود.
2. Migrationها اجرا می‌شوند.
3. Seeder اجرا می‌شود.
4. Laravel Server شروع به کار می‌کند.

---

# API Manual Test

## Health Check

```bash
curl -i http://localhost:8000/api
```

Expected:

```http
HTTP/1.1 200 OK
```

---

## Request Without API Key

```bash
curl -i http://localhost:8000/api/users
```

Expected:

```http
HTTP/1.1 401 Unauthorized
```

---

## Invalid API Key

```bash
curl -i \
  -H "X-Api-Key: wrong-secret" \
  http://localhost:8000/api/users
```

Expected:

```http
HTTP/1.1 401 Unauthorized
```

---

## Valid API Key

API Key باید از Environment پروژه دریافت شود.

نمونه:

```bash
curl -i \
  -H "X-Api-Key: <YOUR_API_KEY>" \
  http://localhost:8000/api/users
```

Expected:

```http
HTTP/1.1 200 OK
```

---

# Automated Tests

برای جلوگیری از دست‌کاری دیتابیس Development، تست‌ها با Database ایزوله اجرا می‌شوند.

## Users API Tests

```bash
docker compose exec api ./run-tests.sh --filter=UsersApiTest
```

نتیجه ثبت‌شده:

```text
3 tests
9 assertions
PASS
```

---

## Full Backend Test Suite

```bash
docker compose exec api ./run-tests.sh
```

نتیجه ثبت‌شده:

```text
6 tests
14 assertions
PASS
```

---

# Tested Scenarios

موارد زیر به‌صورت دستی یا خودکار بررسی شده‌اند:

- [x] Laravel container startup
- [x] Vue container startup
- [x] Database migration
- [x] Database seeding
- [x] API health check
- [x] Missing API Key → 401
- [x] Invalid API Key → 401
- [x] Valid API Key → 200
- [x] User list returned
- [x] Active roles returned
- [x] Inactive roles returned
- [x] Password excluded from API response
- [x] Users API Feature Tests
- [x] Full Backend Test Suite
- [x] Test database isolated from development database
- [x] Frontend users page
- [x] Loading state
- [x] Error state
- [x] Pinia cache behavior
- [x] Frontend/API integration

---

# Environment Configuration

مقادیر حساس نباید داخل Source Code قرار گیرند.

فایل‌های واقعی Environment در Git Commit نمی‌شوند.

برای تنظیم محیط، از فایل‌های Example موجود استفاده شود.

نمونه متغیر Backend:

```env
API_KEY=your-api-key
```

نمونه متغیر Frontend:

```env
VITE_API_KEY=your-api-key
```

> مقادیر Environment باید متناسب با محیط اجرا تنظیم شوند.

---

# Security Notes

در این توسعه موارد زیر رعایت شده است:

- API Key در Controller یا Component هاردکد نشده است.
- API Key از Environment خوانده می‌شود.
- Password کاربران در API Response ارسال نمی‌شود.
- Endpoint کاربران بدون API Key معتبر قابل دسترسی نیست.
- Environment files واقعی نباید داخل Repository قرار گیرند.

---

# Development Approach

این قابلیت روی **ساختار موجود پروژه** پیاده‌سازی شده است.

موارد اصلی پروژه از جمله:

- Laravel Backend
- Vue Frontend
- Pinia
- Routing فعلی
- Models
- Database structure

حفظ شده‌اند و توسعه جدید بدون بازنویسی غیرضروری معماری انجام شده است.

هدف اصلی توسعه، تکمیل قابلیت موردنیاز با حداقل تغییر در Core Architecture پروژه بوده است.

---

# Documentation

مستندات تکمیلی پروژه در مسیر زیر قرار دارند:

```text
docs/
```

شامل:

- گزارش توسعه
- گزارش تست
- توضیح تغییرات
- مستند تحویل

---

# Quick Start

برای اجرای سریع:

```bash
git clone <repository-url>
cd <repository-folder>

docker compose up -d --build

docker compose ps
```

Frontend:

```text
http://localhost:5173
```

Backend:

```text
http://localhost:8000
```

اجرای تست‌ها:

```bash
docker compose exec api ./run-tests.sh
```

---

## Delivery Status

**Users & Roles module: Implemented and Tested**

Backend API، Frontend integration، API Key validation، Pinia caching و automated tests همگی پیاده‌سازی و بررسی شده‌اند.
