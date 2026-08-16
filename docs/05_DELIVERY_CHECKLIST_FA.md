# چک‌لیست تحویل

- [x] پروژه روی Docker اجرا شده است
- [x] API health پاسخ 200 می‌دهد
- [x] API users بدون key پاسخ 401 می‌دهد
- [x] API users با key اشتباه پاسخ 401 می‌دهد
- [x] API users با key صحیح پاسخ 200 می‌دهد
- [x] password در API ارسال نمی‌شود
- [x] role فعال/غیرفعال نمایش داده می‌شود
- [x] Feature tests: 3/3, 9 assertions
- [x] Full backend suite: 6/6, 14 assertions
- [x] دیتابیس تست از development جدا شده است
- [x] Frontend به‌صورت دستی تست شده است
- [x] Pinia cache بررسی شده است
- [x] Loading و 401 state بررسی شده‌اند
- [x] Docker files و مستندات در repository قرار گرفته‌اند
- [x] فایل‌های env واقعی در `.gitignore` قرار گرفته‌اند

## قبل از Push

```powershell
git status
git status --ignored
```

مطمئن شوید `api/.env`, `front/.env`, `docker/api.env`, `docker/front.env` staged نشده باشند.
