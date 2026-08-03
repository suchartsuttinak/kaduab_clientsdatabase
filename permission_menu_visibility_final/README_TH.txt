ชุดแก้ไขซ่อนเมนูตามสิทธิ์รายฟอร์ม V3
=======================================

สาเหตุที่พบ
------------
1) EnforceFormPermission ทำงานแล้ว จึงกัน URL ที่ไม่มีสิทธิ์ได้
2) แต่ Blade Topbar และ Sidebar ที่หน้าเว็บใช้งานจริงยังเป็นไฟล์เดิม
   ซึ่งสร้างทุกหมวดโดยไม่ตรวจ canViewForm()/permission key
3) การนำไฟล์ใหม่ไปเก็บใน resources/views/permission_patch อย่างเดียวไม่มีผล
   เพราะ Layout ไม่ได้ include ไฟล์เหล่านั้น

วิธีติดตั้งแบบอัตโนมัติ (แนะนำ)
--------------------------------
1) สำรองโปรเจกต์ก่อน
2) แตก ZIP ลงที่ root โปรเจกต์ ตำแหน่งเดียวกับไฟล์ artisan
3) รัน:

   php install_permission_menu_fix.php

ตัวติดตั้งจะ:
- ค้นหา Topbar จริงจาก id="appTopbar"
- ค้นหา Sidebar ผู้รับบริการจริงจาก sidebar-client-card-wrap
- ค้นหา Sidebar หลักจริงจาก id="stableMasterSidebar"
- สำรองไฟล์เดิมอัตโนมัติเป็น .permission-backup-วันเวลา
- วางไฟล์ใหม่ลงตำแหน่งจริง
- ล้าง cache ให้

ตรวจสอบหลังติดตั้ง
------------------
findstr /S /N /C:"FORM_PERMISSION_MENU_V3" resources\views\*.blade.php
php -l app\Support\FormPermissionMenu.php
php artisan optimize:clear

จากนั้นออกจากระบบ เข้าสู่ระบบด้วยบัญชีนักสังคมสงเคราะห์ และกด Ctrl+F5

ผลที่ควรได้เมื่อเลือกเพียง 2 ฟอร์มในทะเบียนแรกเข้า
--------------------------------------------------
- Topbar เห็น หน้าหลัก + ทะเบียนแรกเข้า เท่านั้น
- Dropdown ทะเบียนแรกเข้าเห็นเฉพาะ 2 ฟอร์ม
- Sidebar ซ้ายเห็นเฉพาะหมวดทะเบียนแรกเข้าและ 2 ฟอร์ม
- การศึกษา สุขภาพ แบบคัดกรอง และสังคมสงเคราะห์ถูกซ่อนทั้งหมวด
- การเข้าผ่าน URL ที่ไม่มีสิทธิ์ยังถูก Middleware ปฏิเสธ 403

ไม่ต้อง Migration เพิ่ม
-----------------------
ชุดนี้ใช้ตาราง user_form_permissions และคอลัมน์ form_permissions_enabled เดิม
