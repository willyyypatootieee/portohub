<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A curated home for outstanding creative portfolios.">
    <title><?= escape($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container navigation-wrap">
            <a class="brand" href="#top" aria-label="<?= escape(SITE_NAME) ?> home">
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
                <a href="#discover">Explore</a>
                <a href="#creator">Creators</a>
                <a href="#footer">Resources</a>
            </nav>

            <div class="navigation-actions">
                <button class="theme-toggle" type="button" aria-label="Switch to dark theme" title="Switch color theme">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.4 14.8A8.6 8.6 0 0 1 9.2 3.6a8.7 8.7 0 1 0 11.2 11.2Z"/>
                    </svg>
                </button>
                <a class="login-link" href="#cta">Log in</a>
                <a class="button button-small" href="#cta">Creator studio</a>
            </div>
        </div>
    </header>
    <main id="main-content">
