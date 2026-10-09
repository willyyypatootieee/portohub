<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../app/bootstrap.php';

$adminConfig = require __DIR__ . '/../app/config/admin.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    redirect('index.php');
}

if (!empty($_POST['action']) && $_POST['action'] === 'login') {
    verifyCsrfToken();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $isCorrectUsername = hash_equals($adminConfig['username'], $username);
    $isCorrectPassword = hash_equals($adminConfig['password'], $password);

    if ($isCorrectUsername && $isCorrectPassword) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        redirect('index.php');
    }

    $loginError = 'Nama pengguna atau kata sandi tidak tepat.';
}

if (empty($_SESSION['admin_logged_in'])) {
    require __DIR__ . '/../views/admin/login.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $action = (string) ($_POST['action'] ?? '');
    $itemId = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $itemId > 0) {
        $portfolioRepository->delete($itemId);
        $_SESSION['flash_message'] = 'Karya berhasil dihapus.';
        redirect('index.php');
    }

    if ($action === 'save') {
        $item = portfolioItemFromRequest();

        if ($itemId > 0) {
            $portfolioRepository->update($itemId, $item);
            $_SESSION['flash_message'] = 'Karya berhasil diperbarui.';
        } else {
            $portfolioRepository->create($item);
            $_SESSION['flash_message'] = 'Karya baru berhasil ditambahkan.';
        }

        redirect('index.php');
    }
}

$editingItem = null;
if (isset($_GET['edit'])) {
    $editingItem = $portfolioRepository->find((int) $_GET['edit']);
}

$portfolioItems = $portfolioRepository->getAll();
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

require __DIR__ . '/../views/admin/dashboard.php';

/** @return array<string, int|string> */
function portfolioItemFromRequest(): array
{
    return [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'creator_name' => trim((string) ($_POST['creator_name'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')),
        'image_filename' => basename(trim((string) ($_POST['image_filename'] ?? ''))),
        'likes_count' => max(0, (int) ($_POST['likes_count'] ?? 0)),
        'views_count' => max(0, (int) ($_POST['views_count'] ?? 0)),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'display_order' => (int) ($_POST['display_order'] ?? 0),
    ];
}
