<?php

declare(strict_types=1);

/**
 * ตัวติดตั้งระบบสิทธิ์ส่วนกลาง V6
 * รันจาก root โปรเจกต์: php install_permission_extension_v6.php
 */

$root = __DIR__;
$payload = $root . DIRECTORY_SEPARATOR . 'permission_extension_v6' . DIRECTORY_SEPARATOR . 'files';
$timestamp = date('Ymd-His');
$manifestDir = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'permission-installer';
$manifestPath = $manifestDir . DIRECTORY_SEPARATOR . "v6-{$timestamp}.json";

function failInstall(string $message): never
{
    fwrite(STDERR, "[ERROR] {$message}" . PHP_EOL);
    exit(1);
}

function normalizePath(string $path): string
{
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function ensureDirectory(string $directory): void
{
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        failInstall("ไม่สามารถสร้างโฟลเดอร์ {$directory}");
    }
}

function backupAndWrite(string $source, string $destination, string $timestamp, array &$manifest): void
{
    if (!is_file($source)) {
        failInstall("ไม่พบไฟล์ต้นฉบับ {$source}");
    }

    ensureDirectory(dirname($destination));

    $backup = null;
    if (is_file($destination)) {
        $backup = $destination . ".permission-v6-backup-{$timestamp}";
        if (!copy($destination, $backup)) {
            failInstall("สำรองไฟล์ไม่สำเร็จ {$destination}");
        }
        echo "[BACKUP] {$backup}" . PHP_EOL;
    }

    if (!copy($source, $destination)) {
        failInstall("วางไฟล์ไม่สำเร็จ {$destination}");
    }

    $manifest[] = [
        'destination' => $destination,
        'backup' => $backup,
        'created' => $backup === null,
    ];

    echo "[WRITE]  {$destination}" . PHP_EOL;
}

function backupAndPatch(string $destination, callable $patcher, string $timestamp, array &$manifest): void
{
    if (!is_file($destination)) {
        failInstall("ไม่พบไฟล์ที่ต้องปรับ {$destination}");
    }

    $original = file_get_contents($destination);
    if ($original === false) {
        failInstall("อ่านไฟล์ไม่ได้ {$destination}");
    }

    $updated = $patcher($original);
    if (!is_string($updated) || $updated === '') {
        failInstall("ผลการปรับไฟล์ไม่ถูกต้อง {$destination}");
    }

    if ($updated === $original) {
        echo "[SKIP]   {$destination} (ปรับไว้แล้ว)" . PHP_EOL;
        return;
    }

    $backup = $destination . ".permission-v6-backup-{$timestamp}";
    if (!copy($destination, $backup)) {
        failInstall("สำรองไฟล์ไม่สำเร็จ {$destination}");
    }

    if (file_put_contents($destination, $updated) === false) {
        failInstall("บันทึกไฟล์ไม่สำเร็จ {$destination}");
    }

    $manifest[] = [
        'destination' => $destination,
        'backup' => $backup,
        'created' => false,
    ];

    echo "[BACKUP] {$backup}" . PHP_EOL;
    echo "[PATCH]  {$destination}" . PHP_EOL;
}

if (!is_file($root . DIRECTORY_SEPARATOR . 'artisan')) {
    failInstall('กรุณาวางและรันไฟล์นี้ที่ root โปรเจกต์ ตำแหน่งเดียวกับ artisan');
}

if (!is_dir($payload)) {
    failInstall('ไม่พบโฟลเดอร์ permission_extension_v6/files กรุณาแตก ZIP ให้ครบ');
}

ensureDirectory($manifestDir);
$manifest = [];

$files = [
    'config/user_permissions.php',
    'app/Models/User.php',
    'app/Http/Controllers/UserManagementController.php',
    'app/Http/Middleware/EnforceFormPermission.php',
    'app/Support/FormPermissionMenu.php',
    'app/Support/FormPermissionUi.php',
    'resources/views/components/form_permission_ui.blade.php',
    'resources/views/backend/users/_form_fields.blade.php',
    'resources/views/backend/users/_form_permissions.blade.php',
    'resources/views/backend/users/create.blade.php',
    'resources/views/backend/users/edit.blade.php',
    'resources/views/backend/users/index.blade.php',
    'resources/views/admin/body/sidebar.blade.php',
    'routes/web.php',
    'routes/backend/institution.php',
    'routes/backend/subject.php',
    'routes/backend/house.php',
    'routes/backend/education.php',
    'routes/backend/semester.php',
    'routes/backend/psycho.php',
    'routes/backend/misbehavior.php',
    'routes/backend/outside.php',
    'routes/backend/document.php',
    'routes/backend/income.php',
    'routes/backend/helpType.php',
    'routes/backend/citizenship.php',
    'routes/backend/citizen.php',
    'routes/backend/translate.php',
];

foreach ($files as $relative) {
    backupAndWrite(
        normalizePath($payload . DIRECTORY_SEPARATOR . $relative),
        normalizePath($root . DIRECTORY_SEPARATOR . $relative),
        $timestamp,
        $manifest
    );
}

// ใส่ตัวช่วยซ่อนปุ่มใน Sidebar ฝั่งแฟ้มผู้รับบริการ โดยไม่รื้อโค้ดเดิม
$clientSidebarPath = normalizePath($root . DIRECTORY_SEPARATOR . 'resources/views/admin_client/body/client_sidebar.blade.php');
backupAndPatch($clientSidebarPath, static function (string $content): string {
    if (str_contains($content, "components.form_permission_ui")) {
        return $content;
    }

    return rtrim($content) . PHP_EOL . PHP_EOL
        . "{{-- FORM_PERMISSION_UI_V6 --}}" . PHP_EOL
        . "@include('components.form_permission_ui')" . PHP_EOL;
}, $timestamp, $manifest);

// ปรับ Topbar ครั้งเดียว: เพิ่มตัวช่วย UI และเปลี่ยนปลายทาง Dashboard เป็น /client เมื่อไม่มีสิทธิ์
$headerPath = normalizePath($root . DIRECTORY_SEPARATOR . 'resources/views/admin_client/body/client_header.blade.php');
backupAndPatch($headerPath, static function (string $content): string {
    if (!str_contains($content, "canViewForm('dashboard_overview')")) {
        $replacement = "(auth()->user()?->canViewForm('dashboard_overview') ? route('dashboard') : route('client.show'))";
        $content = preg_replace("/route\\(\\s*['\"]dashboard['\"]\\s*\\)/", $replacement, $content) ?? $content;
    }

    if (!str_contains($content, "components.form_permission_ui")) {
        $content = rtrim($content) . PHP_EOL . PHP_EOL
            . "{{-- FORM_PERMISSION_UI_V6 --}}" . PHP_EOL
            . "@include('components.form_permission_ui')" . PHP_EOL;
    }

    return $content;
}, $timestamp, $manifest);

// ยืนยันว่า Middleware ถูกต่อท้าย web group ใน Laravel 12
$bootstrapPath = normalizePath($root . DIRECTORY_SEPARATOR . 'bootstrap/app.php');
backupAndPatch($bootstrapPath, static function (string $content): string {
    if (str_contains($content, 'EnforceFormPermission::class')) {
        return $content;
    }

    $needle = "->withMiddleware(function (Middleware \\$middleware): void {";
    $position = strpos($content, $needle);
    if ($position === false) {
        failInstall('ไม่พบ withMiddleware ใน bootstrap/app.php');
    }

    $insertAt = $position + strlen($needle);
    $addition = PHP_EOL
        . "        \\$middleware->web(append: [" . PHP_EOL
        . "            \\App\\Http\\Middleware\\EnforceFormPermission::class," . PHP_EOL
        . "        ]);" . PHP_EOL;

    return substr($content, 0, $insertAt) . $addition . substr($content, $insertAt);
}, $timestamp, $manifest);

file_put_contents($manifestPath, json_encode([
    'version' => 'v6',
    'installed_at' => date(DATE_ATOM),
    'files' => $manifest,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo PHP_EOL . "กำลังตรวจ syntax..." . PHP_EOL;
$lintFiles = [
    'config/user_permissions.php',
    'app/Models/User.php',
    'app/Http/Controllers/UserManagementController.php',
    'app/Http/Middleware/EnforceFormPermission.php',
    'app/Support/FormPermissionMenu.php',
    'app/Support/FormPermissionUi.php',
    'bootstrap/app.php',
    'routes/web.php',
];

foreach ($lintFiles as $relative) {
    $path = normalizePath($root . DIRECTORY_SEPARATOR . $relative);
    $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path);
    exec($command, $output, $code);
    if ($code !== 0) {
        failInstall("พบ syntax error ใน {$relative}: " . implode(PHP_EOL, $output));
    }
    echo "[OK]     {$relative}" . PHP_EOL;
    $output = [];
}

echo PHP_EOL . "กำลังล้าง Laravel cache..." . PHP_EOL;
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . DIRECTORY_SEPARATOR . 'artisan') . ' optimize:clear', $artisanCode);

if ($artisanCode !== 0) {
    failInstall('ล้าง cache ไม่สำเร็จ กรุณารัน php artisan optimize:clear ด้วยตนเอง');
}

echo PHP_EOL;
echo "[DONE] ติดตั้งระบบสิทธิ์ส่วนกลาง V6 สำเร็จ" . PHP_EOL;
echo "[INFO] ไม่ต้องรัน Migration เพิ่ม" . PHP_EOL;
echo "[INFO] Manifest สำหรับย้อนกลับ: {$manifestPath}" . PHP_EOL;
echo "[NEXT] ออกจากระบบ เข้าระบบใหม่ แล้วกำหนดสิทธิ์ที่เมนูจัดการผู้ใช้งาน" . PHP_EOL;
