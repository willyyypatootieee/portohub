<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$userId = requireCreatorLogin();
$user = $studioRepository->findUser($userId);
$counts = $studioRepository->studioCounts($userId);
$page = 'analytics';
$pageTitle = 'Analytics';
require dirname(__DIR__, 2) . '/views/creator/analytics.php';
