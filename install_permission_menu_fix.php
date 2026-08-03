<?php

declare(strict_types=1);

/**
 * ตัวติดตั้งชุดซ่อนเมนูตามสิทธิ์รายฟอร์ม V3
 * วิธีใช้: แตก ZIP ไว้ที่ root โปรเจกต์ แล้วรัน
 * php install_permission_menu_fix.php
 */

$root = __DIR__;

if (!is_file($root . DIRECTORY_SEPARATOR . 'artisan')) {
    fwrite(STDERR, "[ERROR] กรุณาวางไฟล์นี้ไว้ที่ root โปรเจกต์ Laravel (ตำแหน่งเดียวกับ artisan)\n");
    exit(1);
}

$sourceRoot = $root . DIRECTORY_SEPARATOR . 'permission_menu_visibility_final';
if (!is_dir($sourceRoot)) {
    // รองรับกรณีไฟล์ทั้งหมดถูกแตกตรง root
    $sourceRoot = $root;
}

$supportSource = $sourceRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'FormPermissionMenu.php';
$templateDir = $sourceRoot . DIRECTORY_SEPARATOR . 'templates';

if (!is_file($supportSource) || !is_dir($templateDir)) {
    fwrite(STDERR, "[ERROR] ไม่พบไฟล์ประกอบของชุดติดตั้ง กรุณาแตก ZIP ให้ครบทุกไฟล์\n");
    exit(1);
}

function copyWithBackup(string $source, string $destination): void
{
    $directory = dirname($destination);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException("สร้างโฟลเดอร์ไม่ได้: {$directory}");
    }

    if (is_file($destination)) {
        $backup = $destination . '.permission-backup-' . date('Ymd-His');
        if (!copy($destination, $backup)) {
            throw new RuntimeException("สำรองไฟล์ไม่ได้: {$destination}");
        }
        echo "[BACKUP] {$backup}\n";
    }

    if (!copy($source, $destination)) {
        throw new RuntimeException("คัดลอกไฟล์ไม่ได้: {$destination}");
    }

    echo "[WRITE]  {$destination}\n";
}

function findBladeFiles(string $viewsRoot, string $marker): array
{
    $matches = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsRoot, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $path = $file->getPathname();
        $lower = strtolower($path);

        // ข้ามไฟล์สำรองและโฟลเดอร์ patch เก่า
        if (str_contains($lower, 'permission-backup') || str_contains($lower, 'permission_patch') || str_contains($lower, 'permission-template')) {
            continue;
        }

        $content = file_get_contents($path);
        if ($content !== false && str_contains($content, $marker)) {
            $matches[] = $path;
        }
    }

    return array_values(array_unique($matches));
}

try {
    copyWithBackup(
        $supportSource,
        $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'FormPermissionMenu.php'
    );

    $viewsRoot = $root . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
    if (!is_dir($viewsRoot)) {
        throw new RuntimeException('ไม่พบ resources/views');
    }

    $targets = [
        [
            'marker' => 'id="appTopbar"',
            'template' => $templateDir . DIRECTORY_SEPARATOR . 'topbar.blade.php',
            'label' => 'Topbar',
        ],
        [
            'marker' => 'sidebar-client-card-wrap',
            'template' => $templateDir . DIRECTORY_SEPARATOR . 'client_sidebar.blade.php',
            'label' => 'Sidebar ผู้รับบริการ',
        ],
        [
            'marker' => 'id="stableMasterSidebar"',
            'template' => $templateDir . DIRECTORY_SEPARATOR . 'main_sidebar.blade.php',
            'label' => 'Sidebar หลัก',
        ],
    ];

    foreach ($targets as $target) {
        $matches = findBladeFiles($viewsRoot, $target['marker']);

        if ($matches === []) {
            echo "[WARN] ไม่พบ {$target['label']} ด้วย marker: {$target['marker']}\n";
            continue;
        }

        echo "[FOUND] {$target['label']} จำนวน " . count($matches) . " ไฟล์\n";
        foreach ($matches as $destination) {
            copyWithBackup($target['template'], $destination);
        }
    }

    echo "\nกำลังล้าง Laravel cache...\n";
    passthru(PHP_BINARY . ' artisan optimize:clear', $exitCode);

    echo "\n[DONE] ติดตั้งระบบซ่อนเมนูตามสิทธิ์แล้ว\n";
    echo "ตรวจสอบ marker ด้วยคำสั่ง:\n";
    echo "findstr /S /N /C:\"FORM_PERMISSION_MENU_V3\" resources\\views\\*.blade.php\n";
    exit($exitCode ?? 0);
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] {$e->getMessage()}\n");
    exit(1);
}
