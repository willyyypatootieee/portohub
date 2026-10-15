<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StudioRepository;

/** Menangani autentikasi creator tanpa mencampurnya ke file route. */
final class AuthenticationService
{
    public function __construct(private StudioRepository $repository)
    {
    }

    /** @return array<string, mixed>|null */
    public function attempt(string $email, string $password): ?array
    {
        $user = $this->repository->findUserByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        // Akun yang dinonaktifkan pengelola platform tidak boleh masuk.
        if ((int) ($user['is_active'] ?? 1) === 0) {
            return null;
        }

        return $user;
    }

    /**
     * Kredensial benar tetapi akun dinonaktifkan pengelola platform.
     * Dipakai controller untuk menampilkan pesan yang tepat.
     */
    public function isDeactivatedAccount(string $email, string $password): bool
    {
        $user = $this->repository->findUserByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            return false;
        }

        return (int) ($user['is_active'] ?? 1) === 0;
    }

    public function signIn(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['authenticated_at'] = time();
    }

    public function signOut(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parameters = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parameters['path'],
                $parameters['domain'],
                $parameters['secure'],
                $parameters['httponly']
            );
        }

        session_destroy();
    }
}
