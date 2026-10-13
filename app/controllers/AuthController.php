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
            return compact('errors', 'values');
        }

        $user = $this->authentication->attempt($values['email'], $password);

        if ($user === null) {
            $errors['login'] = 'Email atau kata sandi tidak tepat.';
            return compact('errors', 'values');
        }

        $this->authentication->signIn((int) $user['id']);
        setFlash('success', 'Selamat datang kembali, ' . $user['display_name'] . '.');
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
            return compact('errors', 'values');
        }

        $userId = $this->repository->createUser(
            $values['name'],
            $values['email'],
            $password
        );

        $this->authentication->signIn($userId);
        setFlash('success', 'Akun berhasil dibuat. Lengkapi profil publik Anda untuk memulai.');
        redirect(appUrl('/studio/settings.php'));
    }
}
