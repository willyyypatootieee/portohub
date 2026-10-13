<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$userId = requireCreatorLogin();
$user = $studioRepository->findUser($userId);
$counts = $studioRepository->studioCounts($userId);
$projects = array_slice($studioRepository->getProjects($userId), 0, 5);
$media = array_slice($studioRepository->getMedia($userId), 0, 4);
$page = 'dashboard';
$pageTitle = 'Creator studio';
require dirname(__DIR__, 2) . '/views/creator/dashboard.php';
