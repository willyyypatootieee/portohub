<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Core\JsonFormValidator;
use App\Services\AuthenticationService;

require dirname(__DIR__, 2) . '/app/bootstrap.php';

if (currentUserId() !== null) {
    redirect(appUrl('/studio/'));
}

$controller = new AuthController(
    $studioRepository,
    new JsonFormValidator(dirname(__DIR__, 2) . '/app/data/form-schemas.json'),
    new AuthenticationService($studioRepository)
);

['errors' => $errors, 'values' => $values] = $controller->login();
$pageTitle = 'Masuk';

require dirname(__DIR__, 2) . '/views/public/auth/login.php';
