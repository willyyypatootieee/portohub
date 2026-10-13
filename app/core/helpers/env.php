<?php

declare(strict_types=1);

/**
 * Memuat berkas .env secara sederhana tanpa dependency eksternal.
 *
 * Format yang didukung: satu pasangan KUNCI=NILAI per baris, komentar diawali
 * tanda #, dan nilai boleh dibungkus tanda kutip. Nilai yang sudah ada di
 * environment tidak akan ditimpa.
 */
function loadEnvironmentFile(string $filePath): void
{
    if (!is_file($filePath) || !is_readable($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), "\"'");

        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}
