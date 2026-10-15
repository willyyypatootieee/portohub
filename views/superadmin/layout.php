<?php
/**
 * Rangka bersama halaman superadmin (overview, users, content).
 *
 * Dipanggil paling atas oleh setiap view superadmin, lalu ditutup dengan
 * layout-end.php. Nilai $page dipakai untuk menandai menu yang sedang aktif.
 *
 * @var string $pageTitle
 * @var string $page  overview | users | projects
 */
$flash = pullFlash();
$adminNavItems = [
    'overview' => ['label' => 'Overview', 'url' => '/login/superadmin/'],
    'users' => ['label' => 'Users', 'url' => '/login/superadmin/users.php'],
    'projects' => ['label' => 'Content', 'url' => '/login/superadmin/projects.php'],
];
$adminPage = $page ?? 'overview';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($pageTitle) ?> — <?= escape(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/studio.css')) ?>">
</head>
<body class="superadmin-page">
    <header>
        <a class="studio-brand" href="<?= escape(appUrl('/login/superadmin/')) ?>">▮▮ <?= escape(SITE_NAME) ?></a>
        <nav>
            <?php foreach ($adminNavItems as $key => $item): ?>
                <a class="<?= $adminPage === $key ? 'is-active' : '' ?>" href="<?= escape(appUrl($item['url'])) ?>"><?= escape($item['label']) ?></a>
            <?php endforeach; ?>
            <a class="admin-signout" href="<?= escape(appUrl('/')) ?>" target="_blank" rel="noopener">View site</a>
            <a class="admin-signout" href="<?= escape(appUrl('/login/superadmin/?logout=1')) ?>">Sign out</a>
        </nav>
    </header>

    <main>
        <?php if ($flash !== null): ?>
            <p class="studio-alert <?= escape($flash['type']) ?>" role="status"><?= escape($flash['message']) ?></p>
        <?php endif; ?>
