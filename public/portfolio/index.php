<?php

declare(strict_types=1);

/**
 * Portofolio publik satu creator.
 *
 * URL utama: /portfolio/{username}/ (fallback: /portfolio/?user={username}).
 * Menampilkan proyek yang sudah dipublikasikan beserta galeri medianya.
 */

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$username = trim((string) ($_GET['user'] ?? ''));

if ($username === '') {
    http_response_code(404);

    $pageTitle = 'Portfolio not found — ' . SITE_NAME;
    $noticeTitle = 'Portfolio not found';
    $noticeBody = 'Portofolio yang Anda cari tidak ditemukan.';

    require dirname(__DIR__, 2) . '/views/layout/header.php';
    require dirname(__DIR__, 2) . '/views/public/not-found.php';
    require dirname(__DIR__, 2) . '/views/layout/footer.php';
    exit;
}

$creator = $studioRepository->findUserByUsername($username);

if ($creator === null) {
    http_response_code(404);

    $pageTitle = 'Portfolio not found — ' . SITE_NAME;
    $noticeTitle = 'Portfolio not found';
    $noticeBody = 'Tidak ada creator dengan username “' . $username . '”.';

    require dirname(__DIR__, 2) . '/views/layout/header.php';
    require dirname(__DIR__, 2) . '/views/public/not-found.php';
    require dirname(__DIR__, 2) . '/views/layout/footer.php';
    exit;
}

$userId = (int) $creator['id'];
$projects = $studioRepository->getProjects($userId, 'published', '', false);

foreach ($projects as $index => $project) {
    $projectId = (int) $project['id'];
    $projects[$index]['cover'] = $studioRepository->findProjectCover($projectId);
    $projects[$index]['media'] = $studioRepository->getProjectMedia($userId, $projectId);
}

$pageTitle = $creator['display_name'] . ' — Portfolio';
$bodyClass = 'public-portfolio';

// CV dipublikasikan bila creator sudah mengunggahnya. Tombolnya membuka
// flipbook, yang memuat PDF lewat /portfolio/cv.php.
$hasCv = !empty($creator['cv_filename']);
$cvUrl = appUrl('/portfolio/cv.php?user=' . rawurlencode((string) $creator['username']));
$cvDownloadUrl = $cvUrl . '&download=1';
$cvTitle = (string) ($creator['cv_original_name'] ?? '') !== ''
    ? (string) $creator['cv_original_name']
    : 'Curriculum vitae.pdf';

$pageScripts = $hasCv
    ? ['/assets/js/cv-flipbook.js', '/assets/js/lightbox.js']
    : ['/assets/js/lightbox.js'];

require dirname(__DIR__, 2) . '/views/layout/header.php';
require dirname(__DIR__, 2) . '/views/public/portfolio.php';
require dirname(__DIR__, 2) . '/views/layout/footer.php';
