<?php

declare(strict_types=1);


require dirname(__DIR__, 2) . '/app/bootstrap.php';

$username = trim((string) ($_GET['user'] ?? ''));

if ($username === '') {
    http_response_code(404);
    exit('CV tidak ditemukan.');
}

$creator = $studioRepository->findUserByUsername($username);
$storedName = (string) ($creator['cv_filename'] ?? '');

if ($creator === null || $storedName === '') {
    http_response_code(404);
    exit('CV tidak tersedia.');
}

$filePath = cvUploadConfig()['directory'] . '/' . basename($storedName);

if (!is_file($filePath)) {
    http_response_code(404);
    exit('CV tidak tersedia.');
}

$originalName = trim((string) ($creator['cv_original_name'] ?? ''));
$originalName = $originalName !== '' ? $originalName : 'Curriculum-vitae.pdf';

// Nama di header Content-Disposition hanya boleh berisi karakter aman.
$downloadName = (string) preg_replace('/[^A-Za-z0-9._-]+/', '_', $originalName);
$downloadName = $downloadName !== '' ? $downloadName : 'curriculum-vitae.pdf';

if (!str_ends_with(strtolower($downloadName), '.pdf')) {
    $downloadName .= '.pdf';
}

$wantsDownload = isset($_GET['download']);

header('Content-Type: application/pdf');
header('Content-Length: ' . (string) filesize($filePath));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');
header('Content-Disposition: ' . ($wantsDownload ? 'attachment' : 'inline') . '; filename="' . $downloadName . '"');

readfile($filePath);
exit;
