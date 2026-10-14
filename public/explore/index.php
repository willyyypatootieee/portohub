<?php

declare(strict_types=1);

/**
 * Halaman eksplorasi karya publik.
 *
 * Menampilkan semua proyek yang sudah dipublikasikan, lengkap dengan pencarian
 * (judul/kategori/deskripsi/nama creator) dan filter kategori. Tersedia juga
 * tab "Creators" untuk mencari pengguna yang punya karya published.
 *
 * Semua filter diproses di server lewat query string supaya URL bisa dibagikan
 * dan halaman tetap berfungsi tanpa JavaScript.
 */

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$search = trim((string) ($_GET['q'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$tab = (string) ($_GET['tab'] ?? 'projects');

if (!in_array($tab, ['projects', 'creators'], true)) {
    $tab = 'projects';
}

$categories = $studioRepository->getShowcaseCategories();

if ($category !== '' && !in_array($category, $categories, true)) {
    $category = '';
}

$projectTotal = $studioRepository->countPublishedProjects($search, $category);
$projects = $tab === 'projects'
    ? $studioRepository->searchPublishedProjects($search, $category, 60)
    : [];
$creators = $tab === 'creators'
    ? $studioRepository->searchCreators($search, 60)
    : [];

$pageTitle = 'Explore work — ' . SITE_NAME;

require dirname(__DIR__, 2) . '/views/layout/header.php';
require dirname(__DIR__, 2) . '/views/public/explore.php';
require dirname(__DIR__, 2) . '/views/layout/footer.php';
