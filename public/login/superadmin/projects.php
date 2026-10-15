<?php

declare(strict_types=1);

/**
 * Superadmin — moderasi konten.
 *
 * Menampilkan seluruh proyek dari semua creator beserta portofolionya, dengan
 * pencarian, filter status/visibilitas, dan aksi menyembunyikan atau
 * menampilkan kembali proyek. Proyek yang disembunyikan tidak pernah muncul di
 * landing page maupun halaman portofolio publik.
 */

require dirname(__DIR__, 3) . '/app/bootstrap.php';

$superadminMiddleware->requireSuperadmin();

$search = trim((string) ($_GET['q'] ?? ''));
$status = (string) ($_GET['status'] ?? 'all');
$visibility = (string) ($_GET['visibility'] ?? 'all');

if (!in_array($status, ['all', 'draft', 'published', 'archived'], true)) {
    $status = 'all';
}
if (!in_array($visibility, ['all', 'visible', 'hidden'], true)) {
    $visibility = 'all';
}

/** Mengembalikan query string daftar konten. */
$listQuery = static function () use ($search, $status, $visibility): string {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($status !== 'all') {
        $params['status'] = $status;
    }
    if ($visibility !== 'all') {
        $params['visibility'] = $visibility;
    }

    return $params === [] ? '' : '?' . http_build_query($params);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $projectId = (int) ($_POST['project_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($projectId > 0 && in_array($action, ['hide', 'unhide', 'delete'], true)) {
        if ($action === 'delete') {
            $studioRepository->adminDeleteProject($projectId);
            setFlash('success', 'Project and its media were deleted.');
        } else {
            $studioRepository->setProjectHidden($projectId, $action === 'hide');
            setFlash('success', $action === 'hide'
                ? 'Project hidden from public pages.'
                : 'Project is visible again.');
        }
    }

    redirect(appUrl('/login/superadmin/projects.php') . $listQuery());
}

$perPage = 20;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalProjects = $studioRepository->countAllProjects($search, $status, $visibility);
$totalPages = max(1, (int) ceil($totalProjects / $perPage));
$currentPage = min($currentPage, $totalPages);
$projects = $studioRepository->listAllProjects($search, $status, $visibility, $perPage, ($currentPage - 1) * $perPage);

require dirname(__DIR__, 3) . '/views/superadmin/projects.php';
