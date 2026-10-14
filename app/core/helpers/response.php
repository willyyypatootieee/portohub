<?php

declare(strict_types=1);

/**
 * Helper HTTP response.
 *
 * wantsJson() dipakai controller untuk memilih antara merender HTML seperti
 * biasa atau membalas JSON. Mode JSON dipakai modal di landing page yang
 * mengirim form lewat fetch, supaya error bisa tampil tanpa berpindah halaman.
 * Permintaan browser biasa tetap mendapatkan HTML, jadi perilaku lama tidak
 * berubah.
 */

function wantsJson(): bool
{
    $accept = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');

    return str_contains($accept, 'application/json');
}

/**
 * @param array<string, mixed> $payload
 */
function jsonResponse(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
