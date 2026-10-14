<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\JsonFormValidator;
use App\Repositories\StudioRepository;
use App\Services\AuthenticationService;

final class AuthController
{
    public function __construct(
        private StudioRepository $repository,
        private JsonFormValidator $validator,
        private AuthenticationService $authentication
    ) {
    }

    /** @return array{errors: array<string, string>, values: array{email: string}} */
    public function login(): array
    {
        $values = ['email' => ''];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return compact('errors', 'values');
        }

        verifyCsrfToken();
        $values['email'] = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $errors = $this->validator->validate('login', $values + ['password' => $password]);

        if ($errors !== []) {
            return $this->respondToForm($errors, $values);
        }

        $user = $this->authentication->attempt($values['email'], $password);

        if ($user === null) {
            $errors['login'] = 'Email atau kata sandi tidak tepat.';
            return $this->respondToForm($errors, $values);
        }

        $this->authentication->signIn((int) $user['id']);
        setFlash('success', 'Selamat datang kembali, ' . $user['display_name'] . '.');

        if (wantsJson()) {
            jsonResponse(['ok' => true, 'redirect' => appUrl('/studio/')]);
        }

        redirect(appUrl('/studio/'));
    }

    /** @return array{errors: array<string, string>, values: array{name: string, email: string}} */
    public function register(): array
    {
        $values = ['name' => '', 'email' => ''];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return compact('errors', 'values');
        }

        verifyCsrfToken();
        $values = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
        ];
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');
        $errors = $this->validator->validate('register', $values + ['password' => $password]);

        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'Konfirmasi kata sandi belum sama.';
        }

        if ($this->repository->emailExists($values['email'])) {
            $errors['email'] = 'Email ini sudah terdaftar.';
        }

        if ($errors !== []) {
            return $this->respondToForm($errors, $values);
        }

        $userId = $this->repository->createUser(
            $values['name'],
            $values['email'],
            $password
        );

        $this->authentication->signIn($userId);
        setFlash('success', 'Akun berhasil dibuat. Lengkapi profil publik Anda untuk memulai.');

        if (wantsJson()) {
            jsonResponse(['ok' => true, 'redirect' => appUrl('/studio/settings.php')]);
        }

        redirect(appUrl('/studio/settings.php'));
    }

    /**
     * Kembalikan error ke view seperti biasa, atau balas JSON saat form
     * dikirim lewat fetch dari modal di landing page.
     *
     * @param array<string, string> $errors
     * @param array<string, string> $values
     * @return array{errors: array<string, string>, values: array<string, string>}
     */
    private function respondToForm(array $errors, array $values): array
    {
        if (wantsJson()) {
            jsonResponse(['ok' => false, 'errors' => $errors, 'values' => $values], 422);
        }

        return compact('errors', 'values');
    }
}
