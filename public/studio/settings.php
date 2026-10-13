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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $errors = $controller->updateProfile($userId);

    if ($errors !== []) {
        $user = [
            ...$user,
            'display_name' => trim((string) ($_POST['display_name'] ?? '')),
            'username' => strtolower(trim((string) ($_POST['username'] ?? ''))),
            'headline' => trim((string) ($_POST['headline'] ?? '')),
            'bio' => trim((string) ($_POST['bio'] ?? '')),
        ];
    }
}

$page = 'settings';
$pageTitle = 'User settings';

require dirname(__DIR__, 2) . '/views/creator/settings.php';
