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

    if ($userId > 0 && in_array($action, ['activate', 'deactivate'], true)) {
        $studioRepository->setUserActive($userId, $action === 'activate');
        setFlash('success', $action === 'activate'
            ? 'Creator account re-enabled.'
            : 'Creator account deactivated.');
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
