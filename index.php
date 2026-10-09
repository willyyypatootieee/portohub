<?php
/**
 * PortfolioHub landing page entry point.
 *
 * This file only loads data and page sections. Portfolio cards come from MySQL
 * through a repository, while each visible page area lives in views/sections.
 */

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$pageTitle = SITE_NAME . ' — Great work deserves to be seen';
$portfolioItems = $portfolioRepository->getAll();
$portfolioCategories = $portfolioRepository->getCategories();

require __DIR__ . '/views/layout/header.php';
require __DIR__ . '/views/sections/hero.php';
require __DIR__ . '/views/sections/discover-work.php';
require __DIR__ . '/views/sections/creator-feature.php';
require __DIR__ . '/views/sections/call-to-action.php';
require __DIR__ . '/views/layout/footer.php';
