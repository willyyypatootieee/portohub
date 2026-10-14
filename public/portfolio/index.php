<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$username = trim((string) ($_GET['user'] ?? ''));

if ($username === '') {
    http_response_code(404);
    exit('Portfolio not found.');
}

$creator = $studioRepository->findUserByUsername($username);

if ($creator === null) {
    http_response_code(404);
    exit('Portfolio not found.');
}

$projects = $studioRepository->getProjects((int) $creator['id'], 'published');

foreach ($projects as $index => $project) {
    $projectId = (int) $project['id'];
    $projects[$index]['cover'] = $studioRepository->findProjectCover($projectId);
    $projects[$index]['media'] = $studioRepository->getProjectMedia((int) $creator['id'], $projectId);
}

require dirname(__DIR__, 2) . '/views/public/portfolio.php';
