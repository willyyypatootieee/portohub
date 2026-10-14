<?php

declare(strict_types=1);

/**
 * Halaman detail proyek publik.
 *
 * Menampilkan satu proyek yang sudah dipublikasikan lengkap dengan galeri
 * medianya (gambar/GIF/video/PDF), informasi creator, dan karya lain dari
 * creator yang sama. URL utama: /project/{id}/ (fallback: /project/?id={id}).
 */

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$projectId = (int) ($_GET['id'] ?? 0);
$project = $projectId > 0 ? $studioRepository->findPublishedProject($projectId) : null;

if ($project === null) {
    http_response_code(404);

    $pageTitle = 'Work not found — ' . SITE_NAME;
    $noticeTitle = 'Work not found';
    $noticeBody = 'Karya yang Anda cari tidak ditemukan atau belum dipublikasikan.';

    require dirname(__DIR__, 2) . '/views/layout/header.php';
    require dirname(__DIR__, 2) . '/views/public/not-found.php';
    require dirname(__DIR__, 2) . '/views/layout/footer.php';
    exit;
}

$userId = (int) $project['user_id'];

$project['cover'] = $studioRepository->findProjectCover($projectId);
$media = $studioRepository->getProjectMedia($userId, $projectId);
$moreProjects = $studioRepository->getPublishedProjectsByUser($userId, $projectId, 3);
$creatorProjectCount = $studioRepository->countPublishedProjectsForUser($userId);

$pageTitle = $project['title'] . ' — ' . SITE_NAME;
$pageScripts = ['/assets/js/lightbox.js'];

require dirname(__DIR__, 2) . '/views/layout/header.php';
require dirname(__DIR__, 2) . '/views/public/project.php';
require dirname(__DIR__, 2) . '/views/layout/footer.php';
