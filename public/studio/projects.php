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
$errors = [];
$mediaErrors = [];
$editingProject = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        $controller->deleteProject($userId);
    }

    if ($action === 'save') {
        $errors = $controller->saveProject($userId);
        $editingProject = [
            'id' => (int) ($_POST['id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'draft'),
            'featured' => isset($_POST['featured']) ? 1 : 0,
        ];
    }

    if ($action === 'upload-media') {
        $mediaErrors = $controller->uploadProjectMedia($userId);
        $editingProject = $studioRepository->findProject($userId, (int) ($_POST['project_id'] ?? 0));
    }

    if ($action === 'delete-media') {
        $controller->deleteProjectMedia($userId);
    }

    if ($action === 'delete-gallery') {
        $controller->deleteGalleryMedia($userId);
    }
}

if ($editingProject === null && isset($_GET['edit'])) {
    $editingProject = $studioRepository->findProject($userId, (int) $_GET['edit']);
}

$projectMedia = $editingProject !== null
    ? $studioRepository->getProjectMedia($userId, (int) $editingProject['id'])
    : [];

$status = (string) ($_GET['status'] ?? 'all');
$availableStatuses = ['all', 'draft', 'published', 'archived'];
$status = in_array($status, $availableStatuses, true) ? $status : 'all';
$search = trim((string) ($_GET['q'] ?? ''));
$projects = $studioRepository->getProjects($userId, $status === 'all' ? null : $status, $search);
$counts = $studioRepository->studioCounts($userId);

$mediaFilter = (string) ($_GET['media'] ?? 'all');
$availableMediaFilters = ['all', 'images', 'video', 'documents'];
$mediaFilter = in_array($mediaFilter, $availableMediaFilters, true) ? $mediaFilter : 'all';
$galleryMedia = $studioRepository->getMedia($userId, $mediaFilter);

$page = 'projects';
$pageTitle = 'Projects';

require dirname(__DIR__, 2) . '/views/creator/projects.php';
