<?php

declare(strict_types=1);

/**
 * Superadmin — direktori pengguna.
 *
 * Menampilkan daftar creator dengan pencarian dan filter status, serta aksi
 * mengaktifkan atau menonaktifkan akun. Akun yang dinonaktifkan tidak bisa
 * masuk lagi (lihat AuthenticationService dan AuthMiddleware).
 */

require dirname(__DIR__, 3) . '/app/bootstrap.php';

$superadminMiddleware->requireSuperadmin();

$search = trim((string) ($_GET['q'] ?? ''));
$status = (string) ($_GET['status'] ?? 'all');

if (!in_array($status, ['all', 'active', 'inactive'], true)) {
    $status = 'all';
}

/** Mengembalikan query string daftar (tanpa membawa parameter aksi). */
$listQuery = static function () use ($search, $status): string {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($status !== 'all') {
        $params['status'] = $status;
    }

    return $params === [] ? '' : '?' . http_build_query($params);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $userId = (int) ($_POST['user_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($userId > 0 && in_array($action, ['activate', 'deactivate', 'update', 'delete'], true)) {
        if ($action === 'activate' || $action === 'deactivate') {
            $studioRepository->setUserActive($userId, $action === 'activate');
            setFlash('success', $action === 'activate'
                ? 'Creator account re-enabled.'
                : 'Creator account deactivated.');
        } elseif ($action === 'update') {
            $displayName = trim((string) ($_POST['display_name'] ?? ''));
            $username = strtolower(trim((string) ($_POST['username'] ?? '')));
            $headline = trim((string) ($_POST['headline'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));

            $errors = [];
            if ($displayName === '') {
                $errors[] = 'Display name is required.';
            }
            if ($username === '' || !preg_match('/^[a-z0-9-]{3,30}$/', $username)) {
                $errors[] = 'Username must be 3–30 characters: letters, numbers, or dashes.';
            } elseif ($studioRepository->usernameExists($username, $userId)) {
                $errors[] = 'That username is already taken.';
            }
            if ($headline === '') {
                $errors[] = 'Headline is required.';
            }
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            } elseif ($studioRepository->emailExists($email, $userId)) {
                $errors[] = 'That email is already in use.';
            }

            if ($errors !== []) {
                setFlash('error', implode(' ', $errors));
            } else {
                $studioRepository->adminUpdateUser($userId, [
                    'display_name' => $displayName,
                    'username' => $username,
                    'headline' => $headline,
                    'email' => $email,
                ]);
                setFlash('success', 'Creator details updated.');
            }
        } elseif ($action === 'delete') {
            if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
                setFlash('error', 'You cannot delete your own account.');
            } else {
                $studioRepository->deleteUser($userId);
                setFlash('success', 'Creator and all of their content were deleted.');
            }
        }
    }

    redirect(appUrl('/login/superadmin/users.php') . $listQuery());
}

$perPage = 20;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalUsers = $studioRepository->countUsers($search, $status);
$totalPages = max(1, (int) ceil($totalUsers / $perPage));
$currentPage = min($currentPage, $totalPages);
$users = $studioRepository->listUsers($search, $status, $perPage, ($currentPage - 1) * $perPage);

require dirname(__DIR__, 3) . '/views/superadmin/users.php';
