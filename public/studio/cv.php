<?php

declare(strict_types=1);

/**
 * Preview dan unduhan CV milik creator yang sedang masuk.
 *
 * Berkas CV disimpan di luar web root (storage/cv), jadi URL langsung tidak
 * bisa dibuka. Route ini memastikan pemohon hanya bisa mengakses CV miliknya
 * sendiri, dengan nama berkas asli saat diunduh.
 *
 *   /studio/cv.php             preview di tab baru (inline)
 *   /studio/cv.php?download=1  paksa unduhan sebagai attachment
 */

require dirname(__DIR__, 2) . '/app/bootstrap.php';

$userId = requireCreatorLogin();
$user = $studioRepository->findUser($userId);
$storedName = (string) ($user['cv_filename'] ?? '');

$filePath = cvUploadConfig()['directory'] . '/' . basename($storedName);

if ($storedName === '' || !is_file($filePath)) {
    http_response_code(404);
    exit('CV belum tersedia.');
}

$originalName = trim((string) ($user['cv_original_name'] ?? ''));
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
header('Cache-Control: private, max-age=0, must-revalidate');
header('Content-Disposition: ' . ($wantsDownload ? 'attachment' : 'inline') . '; filename="' . $downloadName . '"');

readfile($filePath);
exit;
