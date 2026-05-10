<?php
// ============================================================
//  LKSecure — Loader biến môi trường từ file .env
// ============================================================
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        if (!empty($key)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env từ thư mục gốc project
loadEnv(__DIR__ . '/../.env');

function env(string $key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}