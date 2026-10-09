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

/**
 * Mode test hanya berjalan saat koneksi.php dibuka langsung lewat browser.
 * Saat file ini di-require oleh index.php atau halaman admin, tidak ada output
 * sama sekali sehingga redirect, header, dan tampilan aplikasi tetap aman.
 */
$dibukaLangsung = isset($_SERVER['SCRIPT_FILENAME'])
    && realpath((string) $_SERVER['SCRIPT_FILENAME']) === __FILE__;

if ($dibukaLangsung) {
    tampilkanHasilTestKoneksi($koneksi);
}

/**
 * Menampilkan status koneksi dengan echo untuk test manual di browser.
 */
function tampilkanHasilTestKoneksi(\PDO $koneksi): void
{
    echo '<!doctype html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Test Koneksi Database</title>';
    echo '<style>';
    echo 'body { max-width: 700px; margin: 60px auto; padding: 0 20px; font-family: Arial, sans-serif; color: #1b1d1b; }';
    echo '.success { padding: 18px; color: #115c35; background: #e4f6e9; border: 1px solid #9bd5ae; border-radius: 6px; }';
    echo '.error { padding: 18px; color: #8d2020; background: #fbe8e8; border: 1px solid #e8b2b2; border-radius: 6px; }';
    echo 'table { width: 100%; margin-top: 20px; border-collapse: collapse; }';
    echo 'th, td { padding: 12px; text-align: left; border: 1px solid #d6d8d3; }';
    echo 'th { background: #f3f4f1; }';
    echo 'code { padding: 2px 5px; background: #f0f0ed; border-radius: 3px; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<h1>Test koneksi database</h1>';

    try {
        $query = $koneksi->query(
            'SELECT DATABASE() AS database_aktif, COUNT(*) AS total_karya
             FROM portfolio_items'
        );
        $hasil = $query->fetch();

        echo '<div class="success">';
        echo '<strong>Koneksi berhasil.</strong> PHP berhasil terhubung ke MySQL.';
        echo '</div>';
        echo '<table>';
        echo '<tr><th>Informasi</th><th>Nilai</th></tr>';
        echo '<tr>';
        echo '<td>Database aktif</td>';
        echo '<td><code>' . htmlspecialchars((string) $hasil['database_aktif']) . '</code></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Total data karya</td>';
        echo '<td><strong>' . (int) $hasil['total_karya'] . '</strong></td>';
        echo '</tr>';
        echo '</table>';
        echo '<p>File ini sedang dibuka langsung. Saat dipanggil dengan <code>require_once</code> dari halaman lain, hasil test ini tidak akan ditampilkan.</p>';
    } catch (\PDOException $exception) {
        echo '<div class="error">';
        echo '<strong>Koneksi berhasil, tetapi tabel tidak dapat dibaca.</strong>';
        echo '<p>Import file <code>database/portfoliohub.sql</code> melalui phpMyAdmin.</p>';
        echo '</div>';
    }

    echo '</body>';
    echo '</html>';
}
