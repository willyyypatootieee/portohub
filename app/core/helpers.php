<?php

declare(strict_types=1);



// ini adalah file helper yang berisi fungsi-fungsi umum yang digunakan di seluruh aplikasi, seperti escape, csrfToken, verifyCsrfToken, redirect, dan shortNumber. 
// Fungsi-fungsi ini membantu menjaga keamanan, validasi, dan manipulasi data di aplikasi.
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
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): void
{
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (!is_string($submittedToken) || !is_string($sessionToken) || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        exit('Permintaan formulir tidak valid. Silakan kembali dan coba lagi.');
    }
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function shortNumber(int $number): string
{
    if ($number < 1000) {
        return (string) $number;
    }

    return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
}
