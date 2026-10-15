<?php

declare(strict_types=1);

use App\Core\Connection;
use App\Middleware\AuthMiddleware;
use App\Middleware\SuperadminMiddleware;
use App\Repositories\PortfolioRepository;
use App\Repositories\StudioRepository;

/**
 * Bootstrap aplikasi PortfolioHub.
 *
 * Semua halaman (landing, auth, studio, portfolio, superadmin) memuat file ini
 * lebih dulu. Tugasnya:
 *
 *   1. Menyiapkan session yang aman.
 *   2. Memuat helper, konfigurasi, dan kelas aplikasi.
 *   3. Membuka koneksi database.
 *   4. Memastikan tabel creator sudah diimport.
 *   5. Menyiapkan repository dan middleware yang dipakai halaman.
 */

/* 1. Session --------------------------------------------------------------- */

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $isHttps,
        'path' => '/',
    ]);
    session_start();
}

/* 2. Helper, konfigurasi, dan kelas ---------------------------------------- */

require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Connection.php';
require_once __DIR__ . '/core/JsonFormValidator.php';
require_once __DIR__ . '/config/site.php';
require_once __DIR__ . '/config/media.php';
require_once __DIR__ . '/repositories/PortfolioRepository.php';
require_once __DIR__ . '/repositories/StudioRepository.php';
require_once __DIR__ . '/services/AuthenticationService.php';
require_once __DIR__ . '/services/MediaUploadService.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/StudioController.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/SuperadminMiddleware.php';

loadEnvironmentFile(dirname(__DIR__) . '/.env');

/* 3. Koneksi database ------------------------------------------------------ */

/** @var array{host: string, port: string, name: string, username: string, password: string, charset: string} $databaseConfig */
$databaseConfig = require __DIR__ . '/config/database.php';

/** @var \PDO $koneksi */
$koneksi = Connection::open($databaseConfig);

/** @var \PDO $pdo Alias eksplisit untuk kode yang lebih suka nama $pdo. */
$pdo = $koneksi;

/* 4. Repository ------------------------------------------------------------ */

$portfolioRepository = new PortfolioRepository($koneksi);
$studioRepository = new StudioRepository($koneksi);

if (!$studioRepository->hasRequiredSchema()) {
    http_response_code(503);
    exit(
        'Struktur creator studio belum tersedia. '
        . 'Import <code>database/creator-studio.sql</code> terlebih dahulu.'
    );
}

/* 5. Middleware ------------------------------------------------------------ */

$authMiddleware = new AuthMiddleware($studioRepository);

$superadminMiddleware = new SuperadminMiddleware();
