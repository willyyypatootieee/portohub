<?php

declare(strict_types=1);

/**
 * Portofolio milik creator yang sedang masuk, ditampilkan di dalam studio.
 *
 * Berkas ini memakai view yang sama dengan halaman publik
 * (views/public/portfolio.php) tetapi dibungkus layout studio, sehingga
 * sidebar dan navigasi studio tetap terlihat. Tautan "Preview portfolio"
 * di topbar tetap membuka versi publik di tab baru.
 */

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$userId = requireCreatorLogin();
$user = $studioRepository->findUser($userId);

if ($user === null) {
    redirect(appUrl('/studio/'));
}

$creator = $user;
$username = (string) $creator['username'];

// Data yang sama dengan halaman portofolio publik.
$projects = $studioRepository->getProjects($userId, 'published', '', false);

foreach ($projects as $index => $project) {
    $projectId = (int) $project['id'];
    $projects[$index]['cover'] = $studioRepository->findProjectCover($projectId);
    $projects[$index]['media'] = $studioRepository->getProjectMedia($userId, $projectId);
}

$publicUrl = appUrl('/portfolio/' . rawurlencode($username) . '/');

$hasCv = !empty($creator['cv_filename']);
$cvUrl = appUrl('/portfolio/cv.php?user=' . rawurlencode($username));
$cvDownloadUrl = $cvUrl . '&download=1';
$cvTitle = (string) ($creator['cv_original_name'] ?? '') !== ''
    ? (string) $creator['cv_original_name']
    : 'Curriculum vitae.pdf';

$page = 'portfolio';
$pageTitle = 'My portfolio';
$counts = $studioRepository->studioCounts($userId);

require dirname(__DIR__, 2) . '/views/creator/layout.php';
?>
<div class="studio-portfolio">
    <div class="studio-portfolio-bar">
        <p>
            <strong>Pratinjau publik.</strong>
            Beginilah portofolio Anda terlihat oleh pengunjung.
        </p>
        <a class="button button-outline" href="<?= escape($publicUrl) ?>" target="_blank" rel="noopener">
            Buka versi publik
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 17 17 7"/>
                <path d="M9 7h8v8"/>
            </svg>
        </a>
    </div>

    <?php require dirname(__DIR__, 2) . '/views/public/portfolio.php'; ?>
</div>
<?php require dirname(__DIR__, 2) . '/views/creator/layout-end.php'; ?>
