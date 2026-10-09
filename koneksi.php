<?php

declare(strict_types=1);

/**
 * Koneksi database utama PortfolioHub.
 *
 * File ini dapat dipanggil langsung dari PHP lain dengan:
 *
 *     require_once __DIR__ . '/koneksi.php';
 *
 * Variabel $koneksi kemudian berisi objek PDO yang siap digunakan.
 * Pengaturan koneksi dibaca dari .env bila tersedia, lalu menggunakan nilai
 * default yang cocok untuk XAMPP dan Laragon: database portfolio_hub,
 * username root, dan password kosong.
 */

use App\Core\Database;

require_once __DIR__ . '/app/core/helpers.php';
require_once __DIR__ . '/app/core/Database.php';

loadEnvironmentFile(__DIR__ . '/.env');

$databaseConfig = require __DIR__ . '/app/config/database.php';

try {
    $koneksi = Database::connect($databaseConfig);
} catch (\PDOException $exception) {
    error_log('PortfolioHub database connection error: ' . $exception->getMessage());

    http_response_code(500);
    exit(
        'Koneksi database gagal. Pastikan MySQL sudah menyala dan database '
        . '<strong>portfolio_hub</strong> sudah diimpor dari file '
        . '<code>database/portfoliohub.sql</code>.'
    );
}

/**
 * Alias yang lebih eksplisit untuk kode yang lebih menyukai nama $pdo.
 * Keduanya merujuk pada koneksi PDO yang sama.
 *
 * @var \PDO $pdo
 */
$pdo = $koneksi;
