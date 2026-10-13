<?php
/** @var array<string, mixed> $user */
$navItems = [
    'dashboard' => ['label' => 'Creator studio', 'url' => '/studio/'],
    'portfolio' => ['label' => 'My portfolio', 'url' => '/portfolio/?user=' . rawurlencode((string) $user['username'])],
    'projects' => ['label' => 'Projects', 'url' => '/studio/projects.php'],
    'media' => ['label' => 'Media library', 'url' => '/studio/media.php'],
    'analytics' => ['label' => 'Analytics', 'url' => '/studio/analytics.php'],
    'settings' => ['label' => 'User settings', 'url' => '/studio/settings.php'],
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
            <span>▮▮</span>
            <?= escape(SITE_NAME) ?>
        </a>

        <a class="creator-switcher" href="<?= escape(appUrl('/studio/settings.php')) ?>">
            <span class="avatar"><?= escape(strtoupper(substr((string) $user['display_name'], 0, 2))) ?></span>
            <span>
                <strong><?= escape($user['display_name']) ?></strong>
                <small><?= escape($user['headline']) ?></small>
            </span>
            <b>›</b>
        </a>
        <p class="sidebar-label">Workspace</p>
        <nav class="studio-navigation" aria-label="Studio navigation">
            <?php foreach (array_slice($navItems, 0, 5, true) as $key => $item): ?>
                <a class="<?= $page === $key ? 'is-active' : '' ?>" href="<?= escape(appUrl($item['url'])) ?>">
                    <?= escape($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <p class="sidebar-label">Manage</p>
        <nav class="studio-navigation">
            <a class="<?= $page === 'settings' ? 'is-active' : '' ?>" href="<?= escape(appUrl('/studio/settings.php')) ?>">User settings</a>
        </nav>
        <a class="back-discovery" href="<?= escape(appUrl('/')) ?>">← Back to discovery</a>
    </aside>
    <section class="studio-content">
        <header class="studio-topbar">
            <button class="sidebar-toggle" type="button" aria-label="Toggle navigation" aria-controls="studio-sidebar">☰</button>
            <strong><?= escape($pageTitle) ?></strong>

            <div class="topbar-actions">
                <a class="button button-outline" href="<?= escape(appUrl('/portfolio/?user=' . rawurlencode((string) $user['username']))) ?>" target="_blank" rel="noopener">Preview portfolio</a>
                <a class="button" href="<?= escape(appUrl('/studio/projects.php#project-form')) ?>">＋ New project</a>
                <a class="logout-link" href="<?= escape(appUrl('/studio/logout.php')) ?>">Sign out</a>
            </div>
        </header>
        <main class="studio-main">
            <?php if ($flash): ?>
                <p class="studio-alert <?= escape($flash['type']) ?>" role="status">
                    <?= escape($flash['message']) ?>
                </p>
            <?php endif; ?>
