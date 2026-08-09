<?php
/**
 * Production preflight for this project.
 * Standalone: does not require vendor/autoload.php or Laravel bootstrap.
 * Usage: php scripts/production_preflight.php
 */

declare(strict_types=1);

$root = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
$failures = 0;
$warnings = 0;

function out(string $level, string $message): void
{
    echo sprintf("[%s] %s%s", $level, $message, PHP_EOL);
}

function pass(string $message): void { out('PASS', $message); }
function warn(string $message): void { global $warnings; $warnings++; out('WARN', $message); }
function fail(string $message): void { global $failures; $failures++; out('FAIL', $message); }

function parseEnvFile(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $result = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($value !== '' && (($value[0] ?? '') === '"' || ($value[0] ?? '') === "'")) {
            $quote = $value[0];
            if (str_ends_with($value, $quote)) {
                $value = substr($value, 1, -1);
            }
        }
        $result[$key] = $value;
    }
    return $result;
}

function boolEnv(?string $value): ?bool
{
    if ($value === null) return null;
    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
}

function cleanVersion(?string $version): ?string
{
    if (!$version) return null;
    return ltrim(trim($version), 'vV');
}

function composerLockedVersion(string $lockPath, string $package): ?string
{
    if (!is_file($lockPath)) return null;
    $json = json_decode((string) file_get_contents($lockPath), true);
    if (!is_array($json)) return null;
    foreach (array_merge($json['packages'] ?? [], $json['packages-dev'] ?? []) as $pkg) {
        if (($pkg['name'] ?? null) === $package) {
            return cleanVersion($pkg['version'] ?? null);
        }
    }
    return null;
}

function npmLockedVersion(string $lockPath, string $package): ?string
{
    if (!is_file($lockPath)) return null;
    $json = json_decode((string) file_get_contents($lockPath), true);
    if (!is_array($json)) return null;
    return cleanVersion($json['packages']['node_modules/' . $package]['version'] ?? null);
}

function denyFileLooksSafe(string $path): bool
{
    if (!is_file($path)) return false;
    $text = strtolower((string) file_get_contents($path));
    return str_contains($text, 'require all denied')
        || str_contains($text, 'deny from all')
        || str_contains($text, 'rewriteRule') && str_contains($text, '[f');
}

echo "Laravel Production Preflight\n";
echo "Project: {$root}\n";
echo str_repeat('=', 72) . "\n";

// Runtime
if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    pass('PHP ' . PHP_VERSION . ' satisfies project minimum (^8.2).');
} else {
    fail('PHP ' . PHP_VERSION . ' is below project minimum 8.2.');
}

// Environment (do not print secrets)
$envPath = $root . '/.env';
$env = parseEnvFile($envPath);
if (!$env) {
    fail('.env is missing. Create it on the server from .env.production.example and fill real values.');
} else {
    (($env['APP_ENV'] ?? '') === 'production') ? pass('APP_ENV=production') : fail('APP_ENV must be production.');
    (boolEnv($env['APP_DEBUG'] ?? null) === false) ? pass('APP_DEBUG=false') : fail('APP_DEBUG must be false.');

    $url = $env['APP_URL'] ?? '';
    str_starts_with(strtolower($url), 'https://') ? pass('APP_URL uses HTTPS.') : fail('APP_URL must use https:// in production.');

    $appKey = $env['APP_KEY'] ?? '';
    ($appKey !== '' && $appKey !== 'CHANGE_ME') ? pass('APP_KEY is present (value not displayed).') : fail('APP_KEY is missing. Do not regenerate an existing production key unless you understand the encryption impact.');

    (boolEnv($env['SESSION_SECURE_COOKIE'] ?? null) === true) ? pass('SESSION_SECURE_COOKIE=true') : fail('SESSION_SECURE_COOKIE should be true behind HTTPS.');
    (boolEnv($env['SESSION_HTTP_ONLY'] ?? 'true') === true) ? pass('SESSION_HTTP_ONLY=true') : fail('SESSION_HTTP_ONLY must be true.');
    (boolEnv($env['SESSION_ENCRYPT'] ?? null) === true) ? pass('SESSION_ENCRYPT=true') : warn('SESSION_ENCRYPT is not true. Recommended for this project.');

    (($env['APP_TIMEZONE'] ?? '') === 'Asia/Bangkok') ? pass('APP_TIMEZONE=Asia/Bangkok') : warn('APP_TIMEZONE is not Asia/Bangkok.');
    (($env['LOG_CHANNEL'] ?? '') === 'daily') ? pass('LOG_CHANNEL=daily') : warn('LOG_CHANNEL=daily is recommended to prevent one log file growing without rotation.');

    foreach (['DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key) {
        $value = $env[$key] ?? '';
        if ($value === '' || $value === 'CHANGE_ME') {
            fail("{$key} is not configured.");
        }
    }
}

// Deployment artifacts
is_file($root . '/vendor/autoload.php') ? pass('vendor/autoload.php exists.') : fail('vendor/ is missing. Run composer install --no-dev --optimize-autoloader on a connected build/deploy machine.');
is_file($root . '/public/build/manifest.json') ? pass('Vite production build manifest exists.') : fail('public/build/manifest.json is missing. Run npm install and npm run build before deployment.');
!file_exists($root . '/public/hot') ? pass('public/hot is absent.') : fail('public/hot exists; remove it in production or Laravel may point to a dev server.');
!file_exists($root . '/public/.env') ? pass('No public/.env file.') : fail('public/.env must never be web-accessible.');

// Dependency lock floors based on known patched versions as of this audit (2026-08-08)
$laravel = composerLockedVersion($root . '/composer.lock', 'laravel/framework');
if ($laravel === null) {
    fail('Cannot read laravel/framework version from composer.lock.');
} elseif (version_compare($laravel, '12.61.1', '>=')) {
    pass("composer.lock laravel/framework={$laravel} is above the minimum security floor checked by this audit.");
} else {
    fail("composer.lock laravel/framework={$laravel}; update to >=12.61.1 (project composer.json targets ^12.64.0).");
}

$axios = npmLockedVersion($root . '/package-lock.json', 'axios');
if ($axios === null) {
    warn('Cannot read axios from package-lock.json.');
} elseif (version_compare($axios, '1.16.0', '>=')) {
    pass("package-lock axios={$axios} meets the audited security floor.");
} else {
    fail("package-lock axios={$axios}; update to >=1.16.0 (project package.json targets ^1.18.1).");
}

$vite = npmLockedVersion($root . '/package-lock.json', 'vite');
if ($vite === null) {
    warn('Cannot read vite from package-lock.json.');
} elseif (version_compare($vite, '7.3.5', '>=')) {
    pass("package-lock vite={$vite} meets the audited Vite 7 security floor.");
} else {
    fail("package-lock vite={$vite}; update to >=7.3.5 (project package.json targets ^7.3.6).");
}

// Filesystem permissions
foreach (['storage', 'bootstrap/cache'] as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        fail("{$dir} directory is missing.");
    } elseif (!is_writable($path)) {
        fail("{$dir} is not writable by the current PHP user.");
    } else {
        pass("{$dir} is writable by the current PHP user.");
    }
}

// Legacy public sensitive directories should be denied even though new files use private storage.
foreach ([
    'public/upload/client_images',
    'public/upload/scholarship_children',
    'public/upload/scholarship_expenses',
    'public/upload/publicizes',
] as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        pass("{$dir} does not exist (no legacy public exposure). ");
        continue;
    }
    denyFileLooksSafe($path . '/.htaccess')
        ? pass("{$dir}/.htaccess blocks direct web access.")
        : fail("{$dir}/.htaccess is missing or does not appear to deny direct access.");
}

// Database/session/cache migration prerequisites
$requiredTablePatterns = [
    'sessions' => "Schema::create('sessions'",
    'cache' => "Schema::create('cache'",
];
$migrations = '';
foreach (glob($root . '/database/migrations/*.php') ?: [] as $file) {
    $migrations .= (string) file_get_contents($file) . "\n";
}
foreach ($requiredTablePatterns as $table => $needle) {
    str_contains($migrations, $needle) ? pass("Migration for {$table} table is present.") : fail("Migration for {$table} table is missing.");
}

echo str_repeat('-', 72) . "\n";
echo "Failures: {$failures} | Warnings: {$warnings}\n";
if ($failures > 0) {
    echo "RESULT: NOT READY - resolve FAIL items before production.\n";
    exit(1);
}

echo "RESULT: PRE-FLIGHT PASSED. Continue with application smoke tests, backup, and restore test.\n";
exit(0);
