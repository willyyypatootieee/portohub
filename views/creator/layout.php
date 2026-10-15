<?php
/** @var array<string, mixed> $user */

/*
 * Navigasi studio. Setiap item membawa ikon SVG (garis) supaya sidebar
 * mudah dipindai; ikon mengikuti warna tautan lewat currentColor.
 */
$navItems = [
    'dashboard' => [
        'label' => 'Creator studio',
        'url' => '/studio/',
        'icon' => '<path d="M4 10.5 12 4l8 6.5"/><path d="M6 9.5V19a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9.5"/>',
    ],
    'portfolio' => [
        'label' => 'My portfolio',
        'url' => '/studio/portfolio.php',
        'icon' => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17"/><path d="M12 3.5c2.2 2.4 3.4 5.3 3.4 8.5s-1.2 6.1-3.4 8.5c-2.2-2.4-3.4-5.3-3.4-8.5S9.8 5.9 12 3.5Z"/>',
    ],
    'projects' => [
        'label' => 'Projects & media',
        'url' => '/studio/projects.php',
        'icon' => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M3.5 9.5h17"/><path d="m7.5 14 2.2 2.2L14 12"/>',
    ],
];
$navManage = [
    'settings' => [
        'label' => 'Profile & CV',
        'url' => '/studio/settings.php',
        'icon' => '<circle cx="12" cy="8.5" r="3.5"/><path d="M5 20c0-3.6 3.1-5.5 7-5.5s7 1.9 7 5.5"/>',
    ],
];
$flash = pullFlash();
?>
<!doctype html>
<html lang="id">
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
<body class="studio-page">
<div class="studio-shell">
    <aside class="studio-sidebar" id="studio-sidebar">
        <a class="studio-brand" href="<?= escape(appUrl('/studio/')) ?>">
            <span aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4 7h6v10H4z"/>
                    <path d="M14 7h6v10h-6z"/>
                </svg>
            </span>
            <?= escape(SITE_NAME) ?>
        </a>

        <a class="creator-switcher" href="<?= escape(appUrl('/studio/settings.php')) ?>" title="Edit profile">
            <span class="avatar"><?= escape(strtoupper(substr((string) $user['display_name'], 0, 2))) ?></span>
            <span class="creator-switcher-meta">
                <strong><?= escape($user['display_name']) ?></strong>
                <small><?= escape($user['headline']) ?></small>
            </span>
            <span class="creator-switcher-edit" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4 20h4L19 9l-4-4L4 16Z"/>
                    <path d="m14 6 4 4"/>
                </svg>
            </span>
        </a>

        <p class="sidebar-label">Workspace</p>
        <nav class="studio-navigation" aria-label="Studio navigation">
            <?php foreach ($navItems as $key => $item): ?>
                <a class="<?= $page === $key ? 'is-active' : '' ?>" href="<?= escape(appUrl($item['url'])) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><?= $item['icon'] ?></svg>
                    <span><?= escape($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <p class="sidebar-label">Manage</p>
        <nav class="studio-navigation" aria-label="Manage navigation">
            <?php foreach ($navManage as $key => $item): ?>
                <a class="<?= $page === $key ? 'is-active' : '' ?>" href="<?= escape(appUrl($item['url'])) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><?= $item['icon'] ?></svg>
                    <span><?= escape($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <a class="back-discovery" href="<?= escape(appUrl('/')) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M19 12H5"/>
                <path d="m11 6-6 6 6 6"/>
            </svg>
            Back to discovery
        </a>
    </aside>
    <section class="studio-content">
        <header class="studio-topbar">
            <button class="sidebar-toggle" type="button" aria-label="Toggle navigation" aria-controls="studio-sidebar">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
            <strong><?= escape($pageTitle) ?></strong>

            <div class="topbar-actions">
                <a class="button button-outline" href="<?= escape(appUrl('/portfolio/?user=' . rawurlencode((string) $user['username']))) ?>" target="_blank" rel="noopener">Preview portfolio</a>
                <a class="button" href="<?= escape(appUrl('/studio/settings.php#cv')) ?>">My CV</a>
            </div>
        </header>
        <main class="studio-main">
            <?php if ($flash): ?>
                <p class="studio-alert <?= escape($flash['type']) ?>" role="status">
                    <?= escape($flash['message']) ?>
                </p>
            <?php endif; ?>
