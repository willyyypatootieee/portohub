<?php

declare(strict_types=1);

use App\Controllers\StudioController;
use App\Core\JsonFormValidator;
use App\Services\MediaUploadService;

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$userId = requireCreatorLogin();
$user = $studioRepository->findUser($userId);
$controller = new StudioController(
    $studioRepository,
    new JsonFormValidator(dirname(__DIR__, 2) . '/app/data/form-schemas.json'),
    new MediaUploadService(mediaUploadConfig())
);
$uploadError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $controller->deleteMedia($userId);
    }

    if ($action === 'upload') {
        $uploadError = $controller->uploadMedia($userId);
    }
}

$filter = (string) ($_GET['type'] ?? 'all');
$availableFilters = ['all', 'images', 'video', 'documents'];
$filter = in_array($filter, $availableFilters, true) ? $filter : 'all';
$media = $studioRepository->getMedia($userId, $filter);
$projects = $studioRepository->getProjects($userId);
$page = 'media';
$pageTitle = 'Media library';

require dirname(__DIR__, 2) . '/views/creator/media.php';
