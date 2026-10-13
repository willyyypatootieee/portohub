<?php

declare(strict_types=1);

/**
 * Helper flash message: pesan singkat yang bertahan satu kali redirect.
 *
 * setFlash() menyimpan pesan ke session. pullFlash() mengambilnya lalu
 * langsung menghapusnya, jadi pesan hanya tampil sekali.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash_' . $type] = $message;
}

/** @return array{type: string, message: string}|null */
function pullFlash(): ?array
{
    foreach (['success', 'error'] as $type) {
        $key = 'flash_' . $type;

        if (isset($_SESSION[$key])) {
            $message = (string) $_SESSION[$key];
            unset($_SESSION[$key]);

            return ['type' => $type, 'message' => $message];
        }
    }

    return null;
}
