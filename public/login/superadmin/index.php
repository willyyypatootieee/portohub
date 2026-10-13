<?php

declare(strict_types=1);

require dirname(__DIR__, 3) . '/app/bootstrap.php';

$adminConfig = require dirname(__DIR__, 3) . '/app/config/admin.php';
$loginError = null;

if (isset($_GET['logout'])) {
    unset($_SESSION['superadmin_logged_in']);
    redirect(appUrl('/login/superadmin/'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $submittedUsername = trim((string) ($_POST['username'] ?? ''));
    $submittedPassword = (string) ($_POST['password'] ?? '');
    $credentialsMatch = hash_equals($adminConfig['username'], $submittedUsername)
        && hash_equals($adminConfig['password'], $submittedPassword);

    if ($credentialsMatch) {
        session_regenerate_id(true);
        $_SESSION['superadmin_logged_in'] = true;
        redirect(appUrl('/login/superadmin/'));
    }

    $loginError = 'The superadmin credentials are incorrect.';
}

if (empty($_SESSION['superadmin_logged_in'])) {
    require dirname(__DIR__, 3) . '/views/superadmin/login.php';
    exit;
}

$counts = $studioRepository->monitoringCounts();
$activity = $studioRepository->recentCreatorActivity();

require dirname(__DIR__, 3) . '/views/superadmin/dashboard.php';
