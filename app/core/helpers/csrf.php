<?php

declare(strict_types=1);

/**
 * Proteksi CSRF untuk seluruh form POST.
 *
 * csrfToken() membuat token acak sekali per session dan menyimpannya di
 * $_SESSION. verifyCsrfToken() membandingkan token yang dikirim form dengan
 * token di session memakai hash_equals agar tidak rentan timing attack.
 */
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

    $isValid = is_string($submittedToken)
        && is_string($sessionToken)
        && hash_equals($sessionToken, $submittedToken);

    if (!$isValid) {
        http_response_code(403);
        exit('Permintaan formulir tidak valid. Silakan kembali dan coba lagi.');
    }
}
