<?php

declare(strict_types=1);

/**
 * Helper autentikasi tingkat halaman.
 *
 * currentUserId() membaca id user dari session bila ada.
 * requireCreatorLogin() memastikan halaman hanya bisa diakses creator yang
 * sudah login, dan langsung redirect ke halaman masuk bila belum.
 */
function currentUserId(): ?int
{
    $userId = $_SESSION['user_id'] ?? null;

    return is_int($userId) || ctype_digit((string) $userId) ? (int) $userId : null;
}

function requireCreatorLogin(): int
{
    /** @var \App\Middleware\AuthMiddleware $authMiddleware */
    global $authMiddleware;

    return $authMiddleware->requireSessionUserId();
}
