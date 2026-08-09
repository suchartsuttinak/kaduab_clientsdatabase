# Production Go-Live Checklist

ติ๊กทุกข้อก่อนเปิดระบบจริง

## Dependency / Build

- [ ] `composer update laravel/framework --with-all-dependencies` สำเร็จ
- [ ] `composer audit` ผ่าน/ไม่มี unresolved production advisory
- [ ] `composer.lock` ไม่ใช่ Laravel 12.34.0 เดิม
- [ ] `npm install` สำเร็จ
- [ ] `npm audit` ตรวจแล้ว
- [ ] axios >= 1.16.0 (เป้าหมาย package.json ^1.18.1)
- [ ] vite >= 7.3.5 (เป้าหมาย package.json ^7.3.6)
- [ ] `npm run build` สำเร็จ
- [ ] มี `public/build/manifest.json`
- [ ] ไม่มี `public/hot`

## Environment

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://...`
- [ ] APP_KEY ถูกต้องและไม่ถูก regenerate โดยไม่ตั้งใจ
- [ ] DB credential production ถูกต้อง
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_HTTP_ONLY=true`
- [ ] `SESSION_ENCRYPT=true`
- [ ] `LOG_CHANNEL=daily`
- [ ] `LOG_LEVEL=warning` หรือเข้มกว่า

## Filesystem / Privacy

- [ ] Web root ชี้ไป Laravel `public/` หรือมี rewrite ที่ตรวจแล้ว
- [ ] `.env` ไม่อยู่ใน public
- [ ] ไม่มี SQL dump/backup/log ZIP ใน public
- [ ] `storage` เขียนได้
- [ ] `bootstrap/cache` เขียนได้
- [ ] client images / scholarship attachments / publicize private files เปิดผ่าน controller ได้
- [ ] ผู้ไม่มีสิทธิ์เปิด private URL ไม่ได้

## Database

- [ ] Full database backup ก่อน deploy
- [ ] Private/upload file backup ก่อน deploy
- [ ] `php artisan migrate --pretend` ตรวจแล้ว
- [ ] `php artisan migrate --force` สำเร็จ
- [ ] ไม่มีการใช้ `migrate:fresh` / `db:wipe`

## Laravel

- [ ] `php artisan optimize:clear`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php scripts/production_preflight.php` = 0 FAIL

## Permission / Security Smoke Test

- [ ] Admin
- [ ] Executive
- [ ] Manager
- [ ] Social worker
- [ ] Teacher/caregiver
- [ ] General user
- [ ] House/client scoping ถูกต้อง
- [ ] Form Permission view/create/update/delete/print ถูกต้อง
- [ ] Read-only เปิดดูได้แต่แก้ข้อมูลไม่ได้
- [ ] Direct unauthorized URL = 403 หรือ redirect ตามที่ออกแบบ
- [ ] status=0 login ไม่ได้
- [ ] Logout POST ทำงาน
- [ ] DELETE + SweetAlert + CSRF ทำงาน

## Audit Log

- [ ] LOGIN
- [ ] LOGIN_FAILED
- [ ] LOGOUT
- [ ] CREATE
- [ ] UPDATE
- [ ] DELETE
- [ ] ACCESS_DENIED
- [ ] ไม่มี password/token/request secret ใน log

## Backup / Restore

- [ ] Backup database เปิด/ตรวจได้
- [ ] Backup private files เปิด/ตรวจได้
- [ ] Restore ลงพื้นที่ทดสอบสำเร็จอย่างน้อย 1 ครั้ง
- [ ] หลัง restore login/permission/private file/report/audit ผ่าน
- [ ] มี rollback plan และรู้ตำแหน่ง backup ล่าสุด

