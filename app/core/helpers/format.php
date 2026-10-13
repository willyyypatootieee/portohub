<?php

declare(strict_types=1);

/**
 * Helper format angka untuk tampilan. Contoh: 18400 menjadi "18.4k".
 */
function shortNumber(int $number): string
{
    if ($number < 1000) {
        return (string) $number;
    }

    return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
}
