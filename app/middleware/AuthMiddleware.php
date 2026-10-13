<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\StudioRepository;

/**
 * Pintu tunggal untuk validasi autentikasi creator studio.
 *
 * Semua halaman /studio/ wajib memanggil requireSessionUserId() di baris atas.
 * Dengan begitu aturan keamanan hanya hidup di satu tempat:
 *
 *   1. Memastikan ada session login yang sah.
 *   2. Memastikan nilai user_id valid dan benar-benar ada di database.
 *   3. Membersihkan session yang rusak lalu mengarahkan ke halaman masuk.
 *
 * Autentikasi memakai session cookie (HttpOnly + SameSite=Lax) yang dibuat
 * setelah login berhasil, dan setiap form POST tetap dilindungi CSRF token.
 */
final class AuthMiddleware
{
    public function __construct(private StudioRepository $repository)
    {
    }

    /**
     * Mengembalikan id creator yang sedang login, atau redirect bila belum.
     */
    public function requireSessionUserId(): int
    {
        $userId = $this->sessionUserId();

        if ($userId === null) {
            $this->reject('Silakan masuk untuk mengakses creator studio.');
        }

        if (!$this->userStillExists($userId)) {
            $this->forgetSession();
            $this->reject('Sesi Anda sudah berakhir. Silakan masuk kembali.');
        }

        return $userId;
    }

    /**
     * Membaca id user dari session dan memastikan tipenya angka yang valid.
     */
    private function sessionUserId(): ?int
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (is_int($userId)) {
            return $userId;
        }

        if (is_string($userId) && ctype_digit($userId)) {
            return (int) $userId;
        }

        return null;
    }

    /**
     * Memastikan akun masih ada. Berguna ketika user dihapus admin saat
     * session-nya masih hidup.
     */
    private function userStillExists(int $userId): bool
    {
        return $this->repository->findUser($userId) !== null;
    }

    /**
     * Menghapus data login dari session yang sudah tidak sah.
     */
    private function forgetSession(): void
    {
        unset($_SESSION['user_id']);
    }

    /**
     * Menolak akses: beri pesan flash lalu arahkan ke halaman masuk.
     */
    private function reject(string $message): never
    {
        setFlash('error', $message);
        redirect(appUrl('/login/'));
    }
}
