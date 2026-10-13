<?php

declare(strict_types=1);

use App\Services\AuthenticationService;

require dirname(__DIR__, 2) . '/app/bootstrap.php';

(new AuthenticationService($studioRepository))->signOut();

redirect(appUrl('/login/'));
