<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A curated home for outstanding creative portfolios.">
    <title><?= escape($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/public.css')) ?>">
</head>
<body<?= ($bodyClass ?? '') !== '' ? ' class="' . escape($bodyClass) . '"' : '' ?>>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container navigation-wrap">
            <a class="brand" href="<?= escape(appUrl('/')) ?>" aria-label="<?= escape(SITE_NAME) ?> home">
                <span class="brand-mark" aria-hidden="true">
                    <i></i>
                    <i></i>
                    <i></i>
                </span>
                <span><?= SITE_NAME ?></span>
            </a>

            <button
                class="menu-toggle"
                type="button"
                aria-label="Open navigation"
                aria-expanded="false"
                aria-controls="primary-navigation"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="primary-navigation" id="primary-navigation" aria-label="Primary navigation">
                <a href="<?= escape(appUrl('/explore/')) ?>">Explore</a>
                <a href="<?= escape(appUrl('/explore/?tab=creators')) ?>">Creators</a>
            </nav>

            <div class="navigation-actions">
                <button class="theme-toggle" type="button" aria-label="Switch to dark theme" title="Switch color theme">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.4 14.8A8.6 8.6 0 0 1 9.2 3.6a8.7 8.7 0 1 0 11.2 11.2Z"/>
                    </svg>
                </button>
                <?php
                $currentUser = null;
                $currentUserId = currentUserId();

                if ($currentUserId !== null && isset($studioRepository)) {
                    $currentUser = $studioRepository->findUser($currentUserId);
                }

                $currentUserName = $currentUser !== null
                    ? (string) ($currentUser['display_name'] ?: $currentUser['username'])
                    : null;
                ?>
                <?php if ($currentUser !== null): ?>
                    <div class="account-menu" data-account-menu>
                        <button
                            class="account-trigger"
                            type="button"
                            aria-haspopup="true"
                            aria-expanded="false"
                            data-account-trigger
                        >
                            <span class="account-avatar" aria-hidden="true"><?= escape(mb_strtoupper(mb_substr($currentUserName, 0, 1))) ?></span>
                            <span class="account-name"><?= escape($currentUserName) ?></span>
                        </button>
                        <div class="account-dropdown" data-account-dropdown hidden>
                            <p class="account-dropdown-label">Masuk sebagai</p>
                            <p class="account-dropdown-user"><?= escape($currentUserName) ?></p>
                            <a href="<?= escape(appUrl('/studio/')) ?>">Creator studio</a>
                            <a href="<?= escape(appUrl('/portfolio/' . rawurlencode((string) $currentUser['username']) . '/')) ?>">Portofolio publik</a>
                            <a class="account-logout" href="<?= escape(appUrl('/studio/logout.php')) ?>">Keluar</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="login-link" href="<?= escape(appUrl('/login/')) ?>" data-auth-open="login">Log in</a>
                <?php endif; ?>
                <a class="button button-small" href="<?= escape(appUrl('/studio/')) ?>">Creator studio</a>
            </div>
        </div>
    </header>
    <main id="main-content">
