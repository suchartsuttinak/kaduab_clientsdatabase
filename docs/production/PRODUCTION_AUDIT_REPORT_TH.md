# รายงานตรวจความพร้อมก่อนขึ้น Production

วันที่ตรวจ: 8 สิงหาคม 2026  
โครงการ: Laravel ระบบสารสนเทศผู้รับบริการ  
แนวทาง: ตรวจทั้งโปรเจกต์, รักษา logic เดิมให้มากที่สุด, แก้เฉพาะจุดที่มีความเสี่ยงหรือบั๊กที่ยืนยันได้

## สรุปผล

สถานะปัจจุบัน: **ยังไม่ควรนำขึ้น Production ทันที** จนกว่าจะทำ 3 เรื่องในหัวข้อ “Blocker ก่อนขึ้น Host” ให้เสร็จ เพราะ lockfile และไฟล์ build ที่แนบมายังเป็นเวอร์ชันเก่า/ไม่ครบ แม้ source code ที่ตรวจและ harden แล้วจะพร้อมกว่าของเดิมมาก

ผลตรวจเชิงสถิติล่าสุด:

- PHP lint: **649 ไฟล์ ผ่าน 100% (0 syntax errors)**
- ตรวจ namespace/path/class สำหรับ Linux: **ไม่พบ mismatch**
- ตรวจ duplicate FQCN: **ไม่พบหลังแก้ไฟล์ model ซ้ำ**
- ตรวจ destructive-looking GET route: **ไม่พบหลังแก้**
- ตรวจ direct Blade link ไปโฟลเดอร์ข้อมูลสำคัญที่ย้ายเป็น private: **ไม่พบ**
- ตรวจ `dd()`, `phpinfo()`, shell execution และ TLS verify=false ใน `app/`/`routes/`: **ไม่พบจาก static scan**
- `.env`, SQL dump, log ขนาดใหญ่, `public/hot`, `public/build.zip`, backup code และข้อมูลทดลอง ถูกตัดออกจากชุด sanitized

> ข้อจำกัด: สภาพแวดล้อมตรวจนี้ไม่มี Composer และไม่มี dependency tree (`vendor/`, `node_modules/`) จึงไม่สามารถรัน `artisan test`, `route:list`, `route:cache`, `composer audit`, `npm audit` หรือ build จริงได้ ต้องทำขั้นตอน Runtime/Dependency Validation บนเครื่องพัฒนาที่ต่ออินเทอร์เน็ตก่อน deploy

---

## Blocker ก่อนขึ้น Host

### 1) Composer lock ยังล็อก Laravel เก่า

ไฟล์เดิมล็อก `laravel/framework v12.34.0` ขณะที่ audit วันที่ 8 ส.ค. 2026 พบ security advisories ที่แก้ใน Laravel 12.60.0 และ 12.61.1 แล้ว จึงปรับ `composer.json` ให้ต้องการ `^12.64.0` แต่ **ไม่ได้แก้ `composer.lock` ด้วยมือ** เพราะการแก้ lockfile ด้วยมือไม่ปลอดภัย

ต้องรันบนเครื่องที่มี Composer + Internet:

```bash
composer update laravel/framework --with-all-dependencies
composer audit
```

จากนั้นตรวจว่า `composer.lock` ล็อก Laravel 12.64.x หรือใหม่กว่าในสาย 12.x ที่เข้ากันได้

### 2) npm lock ยังล็อก Axios/Vite เก่า

ไฟล์เดิมล็อก:

- axios `1.12.2`
- vite `7.1.10`

`package.json` ถูกปรับเป็น:

- axios `^1.18.1`
- vite `^7.3.6`

และเอา `@tailwindcss/vite` ออก เพราะโปรเจกต์ใช้ Tailwind 3 + PostCSS และ `vite.config.js` ไม่ได้ใช้ plugin นี้

ต้องรัน:

```bash
npm install
npm audit
npm run build
```

ห้าม upload `public/hot` ขึ้น Production

### 3) ต้องมี Production `.env`, `vendor/` และ `public/build`

ชุด review intentionally ไม่มี `.env` จริงและไม่มี secrets ให้ใช้ `.env.production.example` เป็นแม่แบบ แล้วใส่ค่า production จริงบน Host เท่านั้น

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://...`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_ENCRYPT=true`
- `LOG_CHANNEL=daily`

**ห้ามสั่ง `php artisan key:generate` บน Production ที่มีข้อมูลเดิม/ข้อมูลเข้ารหัสอยู่แล้วโดยไม่วางแผน** ควรใช้ APP_KEY เดิมของระบบเมื่อย้ายระบบเดิม

---

## จุดสำคัญที่แก้ให้แล้ว

### Authentication / Account state

- Login บังคับ `status=1` เพื่อไม่ให้บัญชีที่ปิดใช้งาน login ใหม่ได้
- เพิ่ม `EnsureUserIsActive` ใน web middleware เพื่อ terminate session ของผู้ใช้ที่ถูกปิดบัญชีระหว่างที่ session ยังไม่หมดอายุ
- logout เปลี่ยนจาก GET เป็น POST + CSRF
- forgot-password/reset-password POST เพิ่ม rate limit
- password policy กลาง: อย่างน้อย 10 ตัวอักษร + ตัวอักษร + ตัวเลข

### Form Permission / Read-only

- `EnforceFormPermission` เปลี่ยนเป็น fail-closed สำหรับ route ที่มี permission mapping: ถ้ายังไม่ login จะไม่ปล่อยผ่าน
- ไม่แก้ semantics ของ permission matrix เดิมสำหรับผู้ใช้ที่ login แล้ว
- Read-only เดิมยังคงทำงาน: GET หน้าแก้ไขเปิดดูได้เมื่อมี view แต่ไม่มี update และ mutation ยังถูกปฏิเสธ
- เพิ่ม `RequireExplicitFormPermissions` เฉพาะโมดูลความเสี่ยงสูง เช่น User Management / Audit Log เพื่อไม่ให้ legacy fallback เปิดกว้างเกินไป
- admin ยังคง bypass ตามโครงสร้างเดิม

### Audit Log

ระบบเดิมมีแกน Audit Log ที่ดีอยู่แล้ว จึงรักษาไว้ โดย middleware ใหม่บันทึก ACCESS_DENIED เฉพาะ metadata ที่จำเป็นและไม่บันทึก password/token/request payload ที่ละเอียดอ่อน

### HTTP method / CSRF

Route ที่ “ลบ” ซึ่งเดิมใช้ GET ถูกเปลี่ยนเป็น DELETE และ JavaScript SweetAlert สร้าง hidden POST form พร้อม `_method=DELETE` + CSRF แทนการ redirect ไป GET

ช่วยลดความเสี่ยงจาก:

- bot/crawler เปิด URL แล้วลบข้อมูล
- prefetch
- link ถูกแชร์/คลิกโดยไม่ตั้งใจ
- CSRF ผ่าน GET

### User deletion / Referential integrity

ตรวจพบ `operations.user_id` ใช้ cascade delete จึงเพิ่ม guard ไม่ให้ลบผู้ใช้ที่มีประวัติการปฏิบัติงานแบบ hard delete; ให้ปิดใช้งานบัญชีแทน เพื่อรักษาประวัติย้อนหลัง

ปิด self-service account deletion ใน ProfileController เพื่อไม่ให้ bypass guard ฝั่งผู้ดูแล

### Sensitive file privacy

ย้ายการสร้างไฟล์ใหม่จาก public path ไป private storage และให้เปิดผ่าน controller ที่ตรวจ authorization สำหรับ:

- รูปผู้รับบริการ
- รูปผู้สมัครทุน
- เอกสารแนบค่าใช้จ่ายทุน
- PDF ประชาสัมพันธ์ที่เป็นงานหลังบ้าน

เพิ่ม `.htaccess` ปิด direct access ใน legacy public folders เพื่อรองรับไฟล์เก่าระหว่าง migration

ระบบเอกสารผู้รับบริการ, รูปประเมิน, เยี่ยมบ้าน, ตรวจสุขภาพ และเอกสาร refer ที่เดิมใช้ private storage อยู่แล้ว ถูกตรวจและคงแนวทางนั้นไว้

### Upload validation

จุดที่แก้ใหม่ใช้แนวทาง:

- จำกัดประเภทไฟล์
- จำกัดขนาด
- รูปใช้ MIME จริง/นามสกุลที่ derive จาก MIME + UUID
- PDF ตรวจ signature `%PDF-` ในจุดที่เกี่ยวข้อง
- ใช้ basename/real path guard เมื่อรองรับ legacy file
- response ไฟล์สำคัญใช้ no-store และ nosniff

### Migration safety

พบ migration เก่า `2025_12_13_013932_create_scholl_followups_table.php` ที่ `up()` เคย drop `school_followups` ก่อน create ซึ่งเสี่ยงทำข้อมูลเดิมหายถ้า production database มีตารางแต่ migration history ไม่ตรง

แก้ `up()` เป็น:

- ถ้ามีตารางอยู่แล้ว -> return
- ถ้ายังไม่มี -> create

`down()` ยังคง drop ตามธรรมชาติของ rollback ดังนั้น **ห้าม rollback Production โดยไม่มี backup ที่ทดสอบ restore แล้ว**

### Linux / Host portability

แก้ namespace ของ `AuditLogController` ให้ตรงกับ path `app/Http/Controllers/backend/` เพื่อป้องกันกรณี Windows ใช้ได้แต่ Linux case-sensitive หา class ไม่เจอ

พบ model `FactFindingDocument.php` ที่ประกาศ class `Factfinding` ซ้ำกับ model จริง จึงนำ stale file นี้ออก

แก้ duplicate route name ของ Education Record โดยรักษา URL legacy เดิมไว้ แต่ตั้งชื่อ legacy ให้ไม่ชน route ใหม่ และ mapping permission ให้เท่ากัน

ตัด duplicate route ของ `scholarship.children.public_report` ให้มี canonical route เดียว

### Operation GET side effect

เอาการ reorder sequence แบบเขียนฐานข้อมูลออกจาก GET `OperationController::index()` และ `dailyReport()` เพื่อให้หน้าอ่านข้อมูลไม่เกิด mutation โดยไม่จำเป็น; create/update/delete ยังจัดลำดับใน flow ที่เหมาะสม

### DomPDF

- ปิด JavaScript
- ปิด remote content โดยค่า production
- ปิด warnings โดยค่า production
- เพิ่ม view `frontend.client.followup.pdf` ที่ controller เรียกแต่เดิมไม่มีจริง ซึ่งเดิมมีโอกาส 500 ตอน export

หมายเหตุ: ต้องทดสอบภาษาไทยบนเครื่องจริงด้วย font assets ของเจ้าของระบบเอง

### Apache / Public directory

`public/.htaccess` เพิ่ม header ที่เสี่ยงต่ำและเข้ากันได้กับ UI ปัจจุบัน เช่น nosniff, SAMEORIGIN, referrer-policy และ permissions-policy

ยัง **ไม่เปิด HSTS** จนกว่าจะยืนยัน HTTPS end-to-end และยัง **ไม่บังคับ CSP** เพราะหน้าเดิมมี inline script/CDN หลายจุด การเปิด CSP ตอนนี้มีโอกาสทำ UI พัง

---

## จุดที่ตั้งใจ “ยังไม่เปลี่ยน”

### Public Scholarship Report

route รายงานทุนสาธารณะยังคง public ตาม intent เดิม แต่ข้อมูลชุด “เพศ + อายุ + การศึกษา + สถานศึกษา + เหตุผล” อาจทำให้ระบุตัวบุคคลได้ในชุมชนขนาดเล็ก จึงควรให้เจ้าของระบบตัดสินใจด้านนโยบาย/consent ก่อน Production

### GET ที่ mark notification/read state

บางหน้าหลังบ้าน mark รายการว่าอ่านแล้วตอนเปิดหน้า GET ซึ่งไม่ใช่ช่องโหว่ระดับ blocker แต่เป็น HTTP semantics ที่ควร refactor ภายหลังถ้าต้องการ strict design โดยรอบนี้ไม่เปลี่ยนเพื่อไม่กระทบ badge/notification UX เดิม

### External CDN / CSP

ยังมี assets จาก CDN ใน Blade หลายหน้า ไม่พบ plain `http://` ที่ชัดเจนในการ scan แต่ถ้าต้องการ hardening ระดับสูงภายหลังควร self-host critical JS/CSS หรือเพิ่ม SRI และออกแบบ CSP โดยทดสอบทั้งระบบ

---

## Backup / Restore ที่ต้องพร้อมก่อน Go-Live

ต้อง backup อย่างน้อย 2 ส่วนพร้อมกัน:

1. MySQL database
2. `storage/app/private` และไฟล์ upload ที่ระบบใช้งานจริง

รวมทั้ง source/version ที่ deploy และ `.env`/APP_KEY ควรเก็บในช่องทาง secret backup ที่ปลอดภัยแยกจาก public web root

การมี backup อย่างเดียวไม่พอ ต้องทำ **restore test** ใน database/พื้นที่ทดสอบ แล้วตรวจ login, permission, client scope, view/download private file, report, create/update/delete และ audit log

---

## เกณฑ์อนุมัติขึ้น Production

อนุมัติได้เมื่อครบทั้งหมด:

- `php scripts/production_preflight.php` => 0 FAIL
- `composer audit` ไม่มี unresolved advisory ที่มีผลกับ production dependency
- `npm audit` ประเมินและแก้ high/critical ที่เกี่ยวข้อง
- `npm run build` สำเร็จ และมี `public/build/manifest.json`
- `php artisan migrate --pretend` ถูกตรวจ review แล้ว
- มี full database backup + file backup ก่อน migrate
- `php artisan migrate --force` สำเร็จ
- `php artisan optimize` / cache commands สำเร็จ
- smoke test ทุก role สำคัญและ read-only permission
- ทดสอบ direct URL access สำหรับ user ที่ไม่มีสิทธิ์
- ทดสอบ upload/view/download private file
- ทดสอบ Audit Log CREATE/UPDATE/DELETE/ACCESS_DENIED/Login/Logout
- ทดสอบ backup restore อย่างน้อย 1 รอบ

