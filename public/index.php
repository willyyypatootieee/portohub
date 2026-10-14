<?php
/**
 * PortfolioHub landing page entry point.
 *
 * This file only loads data and page sections. The discover grid is filled
 * from real published creator projects, so the landing page always reflects
 * the work creators actually uploaded through the studio.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$pageTitle = SITE_NAME . ' — Great work deserves to be seen';
$showcaseProjects = $studioRepository->getShowcaseProjects();
$showcaseCategories = $studioRepository->getShowcaseCategories();

require dirname(__DIR__) . '/views/layout/header.php';
require dirname(__DIR__) . '/views/public/sections/hero.php';
require dirname(__DIR__) . '/views/public/sections/discover-work.php';
require dirname(__DIR__) . '/views/public/sections/creator-feature.php';
require dirname(__DIR__) . '/views/public/sections/call-to-action.php';
require dirname(__DIR__) . '/views/public/sections/auth-modal.php';
require dirname(__DIR__) . '/views/layout/footer.php';
