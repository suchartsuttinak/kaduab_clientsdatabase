# คู่มือนำ Laravel ขึ้น HostAtom อย่างปลอดภัย

เอกสารนี้จัดสำหรับโปรเจกต์ชุดที่ผ่าน Production Security Review วันที่ 8 ส.ค. 2026

## A. ทำบนเครื่องพัฒนาก่อน Upload

### 1. สำรองก่อนอัปเดต dependency

สำรอง source ปัจจุบันและ database ก่อนเสมอ

### 2. อัปเดต Composer lock

```cmd
composer update laravel/framework --with-all-dependencies
composer audit
```

ตรวจว่า Laravel ใน `composer.lock` เป็น 12.64.x หรือใหม่กว่าใน Laravel 12 ที่ compatible

จากนั้น:

```cmd
composer install --no-dev --optimize-autoloader
```

ถ้า HostAtom package ที่ใช้ไม่มี Composer/SSH ให้ทำ `vendor/` บนเครื่องพัฒนาแล้ว upload ไปพร้อม project

### 3. อัปเดต frontend lock และ build

```cmd
npm install
npm audit
npm run build
```

ต้องมี:

```text
public/build/manifest.json
```

และต้องไม่มี:

```text
public/hot
```

Production ไม่ต้อง upload `node_modules/`

### 4. ตรวจ syntax และ preflight

```cmd
php scripts\production_preflight.php
```

บนเครื่อง local อาจ FAIL เรื่อง `.env` เป็น local ซึ่งปกติ ให้รันซ้ำบน staging/host หลังตั้ง production env

---

## B. จัดเตรียม Production `.env`

ใช้ `.env.production.example` เป็นแม่แบบ **อย่า upload `.env` ผ่าน public URL และอย่าเก็บ credential ลง Git/ZIP**

ค่าหลัก:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ชื่อโดเมนจริง
APP_TIMEZONE=Asia/Bangkok

LOG_CHANNEL=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=14

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
FILESYSTEM_LOCAL_SERVE=false
DOMPDF_ENABLE_REMOTE=false
```

### APP_KEY สำคัญมาก

- ระบบใหม่ที่ไม่เคยมีข้อมูลเข้ารหัส: generate 1 ครั้งได้
- ย้ายระบบเดิม: **ใช้ APP_KEY เดิม**
- อย่า generate ใหม่ทุกครั้งที่ deploy เพราะ session/encrypted data/cookies หรือข้อมูลที่ encrypt ไว้อาจอ่านไม่ได้

---

## C. โครงสร้างบน Shared Hosting

แนวทางที่ดีที่สุดคือให้ Document Root ของโดเมนชี้ไปที่โฟลเดอร์ `public/` ของ Laravel

ถ้า control panel/package ไม่อนุญาตเปลี่ยน Document Root และต้องวาง project ใต้ `public_html`/`httpdocs` ให้ใช้แนวทาง rewrite ตามคู่มือ HostAtom แต่ต้องมั่นใจว่า `.env`, `storage/`, `vendor/`, database dump และ source file สำคัญไม่ถูกเปิดตรงผ่านเว็บ

**อย่านำ `.env`, SQL dump, logs หรือ backup ZIP ไปวางใน `public/`**

---

## D. Permission ของไฟล์/โฟลเดอร์

หลักทั่วไปบน Linux hosting:

- directory: 755
- file: 644
- `storage/` และ `bootstrap/cache/` ต้องเขียนได้โดย PHP user
- หลีกเลี่ยง 777 ถ้าไม่จำเป็น

ตรวจ:

```bash
php scripts/production_preflight.php
```

---

## E. Database Migration แบบปลอดภัย

### ก่อน migrate

1. Backup database เต็มชุด
2. Backup private/upload files
3. จดเวลา backup และตรวจว่าไฟล์เปิด/แตกได้
4. ถ้าเป็นไปได้ import backup เข้า database ทดสอบ 1 ครั้ง

ตรวจ SQL ที่ migration จะทำก่อน:

```cmd
php artisan migrate --pretend
```

ถ้ารายการสมเหตุสมผลจึงรัน:

```cmd
php artisan migrate --force
```

**ไม่ใช้ `migrate:fresh`, `db:wipe` หรือ rollback บน Production** เว้นแต่มีแผนกู้คืนชัดเจน

---

## F. Cache / Optimization

หลัง `.env`, vendor, database และ build ถูกต้องแล้ว:

```cmd
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

ถ้า `route:cache` fail ห้ามฝืน deploy ให้แก้ route duplication/closure/error ก่อน

เมื่อแก้ `.env` ภายหลัง ต้อง clear/rebuild config cache ให้ตรงกับค่าใหม่

---

## G. Smoke Test หลัง Deploy

ทดสอบอย่างน้อย:

1. หน้า public เปิดได้
2. login ถูก/ผิด
3. บัญชี status=0 login ไม่ได้ และ session เดิมถูกตัด
4. admin เข้าเมนูครบ
5. role อื่นเห็นเฉพาะบ้าน/ผู้รับบริการที่ได้รับมอบหมาย
6. Form Permission ดู/เพิ่ม/แก้ไข/ลบ/พิมพ์
7. Read-only: เปิดดูได้ แต่ mutation ทำไม่ได้แม้ยิง URL โดยตรง
8. Client image ผ่าน protected route
9. Client documents และ private attachments view/download ได้เฉพาะผู้มีสิทธิ์
10. DELETE ใช้ POST/DELETE + CSRF และ SweetAlert ยังทำงาน
11. Followup PDF export
12. Audit Log มี LOGIN, LOGOUT, CREATE, UPDATE, DELETE, ACCESS_DENIED ที่ควรมี
13. ไม่มี error 500/403 ที่ไม่คาดหวังใน server log

---

## H. Backup / Restore บน HostAtom

HostAtom มีคู่มือสำหรับ DirectAdmin user ที่ใช้เมนู **Backup and Restore** และสามารถ restore Website Data / Databases Data ได้ รวมถึงคู่มือ import/restore database โดยตรง

แนวทางขั้นต่ำสำหรับระบบนี้:

- Daily backup: database + website/private files
- เก็บย้อนหลังหลายรุ่นตามพื้นที่ที่มี
- เก็บสำเนาอย่างน้อยหนึ่งชุดนอก account hosting ถ้านโยบายองค์กรอนุญาต
- ก่อน update ใหญ่: manual backup เพิ่มอีก 1 ชุด
- ทุกระยะให้ทำ restore test บนพื้นที่ทดสอบ

หลัง restore ให้ตรวจ APP_KEY, `.env`, file permission, database connection, `storage/app/private`, permission matrix และ audit log ก่อนเปิดใช้งานจริง

---

## I. Rollback Plan

ถ้า deploy แล้วพบปัญหา:

1. ปิดการใช้งานชั่วคราว/maintenance ตามความเหมาะสม
2. อย่ารัน migration เพิ่มหรือแก้ข้อมูลต่อ
3. เก็บ error log ช่วงเกิดเหตุ
4. restore source version ก่อน deploy
5. restore database **เฉพาะเมื่อ schema/data ถูกเปลี่ยนและจำเป็น**
6. restore private files ให้ตรง snapshot เดียวกับ database
7. clear Laravel cache
8. smoke test ก่อนเปิดระบบอีกครั้ง

