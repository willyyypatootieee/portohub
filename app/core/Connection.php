<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Membuka koneksi database aplikasi.
 *
 * Kelas ini menyatukan seluruh logika koneksi yang dulu tersebar di file
 * koneksi.php di root. Sekarang bootstrap dan script CLI cukup memanggil
 * Connection::open() dan tidak perlu tahu detail DSN maupun error handling.
 */
final class Connection
{
    /**
     * Membuat koneksi PDO dari konfigurasi aplikasi.
     *
     * @param array{host: string, port: string, name: string, username: string, password: string, charset: string} $config
     */
    public static function open(array $config): PDO
    {
        try {
            return Database::connect($config);
        } catch (PDOException $exception) {
            error_log('PortfolioHub database connection error: ' . $exception->getMessage());

            http_response_code(500);
            exit(
                'Koneksi database gagal. Pastikan MySQL sudah menyala dan database '
                . '<strong>portfolio_hub</strong> sudah diimpor dari file '
                . '<code>database/portfoliohub.sql</code>.'
            );
        }
    }
}
