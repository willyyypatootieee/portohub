<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Penjaga halaman monitoring superadmin.
 *
 * Superadmin adalah akun pengelola platform yang kredensialnya berasal dari
 * konfigurasi (app/config/admin.php), bukan dari tabel users. Semua halaman
 * di bawah /login/superadmin/ (selain form masuk) wajib memanggil
 * requireSuperadmin() di baris paling atas.
 */
final class SuperadminMiddleware
{
    /**
     * Menghentikan akses bila session superadmin belum ada, lalu mengarahkan
     * kembali ke halaman masuk.
     */
    public function requireSuperadmin(): void
    {
        if (empty($_SESSION['superadmin_logged_in'])) {
            redirect(appUrl('/login/superadmin/'));
        }
    }
}
