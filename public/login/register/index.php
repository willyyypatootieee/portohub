<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Core\JsonFormValidator;
use App\Services\AuthenticationService;

require dirname(__DIR__, 3) . '/app/bootstrap.php';

if (currentUserId() !== null) {
    redirect(appUrl('/studio/'));
}

$controller = new AuthController(
    $studioRepository,
    new JsonFormValidator(dirname(__DIR__, 3) . '/app/data/form-schemas.json'),
    new AuthenticationService($studioRepository)
);

['errors' => $errors, 'values' => $values] = $controller->register();
$pageTitle = 'Daftar';

require dirname(__DIR__, 3) . '/views/public/auth/register.php';
