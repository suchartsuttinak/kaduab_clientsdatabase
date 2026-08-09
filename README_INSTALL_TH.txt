Production Security Patch v1 - วิธีติดตั้งอย่างปลอดภัย
====================================================
วันที่: 8 สิงหาคม 2026

แนะนำให้ติดตั้ง patch นี้บนเครื่อง Local/XAMPP ก่อน แล้วทดสอบจนผ่าน จึงค่อยเตรียมขึ้น Host

1) Backup project ปัจจุบันและฐานข้อมูลก่อน
2) แตก ZIP patch แล้ว copy ทับตาม path เดิมของ project
3) ลบไฟล์ stale:
   app/Models/FactFindingDocument.php
4) อย่าลบ/แทนที่ font assets เดิมของคุณ
5) อย่านำ .env จาก ZIP/เครื่อง local ไปใช้ production โดยตรง

จาก root project รัน:

php artisan optimize:clear
php -l app\Http\Middleware\EnforceFormPermission.php
php scripts\production_preflight.php

จากนั้นต้อง update dependency เพราะ lockfiles เดิมยังเก่า:

composer update laravel/framework --with-all-dependencies
composer audit
composer install --no-dev --optimize-autoloader

npm install
npm audit
npm run build

ตรวจต่อ:

php artisan optimize:clear
php artisan route:list
php artisan route:cache
php artisan migrate --pretend

ก่อน migrate production:
- Backup database
- Backup storage/app/private และ uploads ที่ใช้งานจริง
- ตรวจ restore backup อย่างน้อย 1 รอบถ้าเป็นไปได้

Production เท่านั้น:

php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php scripts\production_preflight.php

ต้องได้ 0 FAIL ก่อนเปิดใช้งานจริง

อ่านรายละเอียด:
- docs/production/PRODUCTION_AUDIT_REPORT_TH.md
- docs/production/HOSTATOM_DEPLOYMENT_GUIDE_TH.md
- docs/production/PRODUCTION_CHECKLIST_TH.md
- docs/production/PATCH_MANIFEST.txt
