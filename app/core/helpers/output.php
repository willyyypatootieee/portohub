<?php

declare(strict_types=1);

/**
 * Helper keamanan output: escape teks sebelum ditampilkan ke HTML.
 *
 * Selalu pakai fungsi ini ketika menampilkan data dari database atau input
 * pengguna supaya terhindar dari XSS.
 */
function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
