<?php

declare(strict_types=1);

/**
 * Helper URL. appUrl() menyusun URL yang tetap benar ketika PortfolioHub
 * dipasang di subfolder Apache (misalnya http://localhost/portohub/).
 */
function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function appUrl(string $path = '/'): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $markers = ['/login/', '/studio/', '/portfolio/'];
    $basePath = dirname($scriptName);

    // Route yang memakai index.php di dalam folder (mis. /login/index.php)
    // membuat dirname() menunjuk ke folder route, bukan folder proyek. Ambil
    // bagian sebelum marker route supaya base path kembali ke root proyek.
    foreach ($markers as $marker) {
        $position = strpos($scriptName, $marker);

        if ($position !== false) {
            $basePath = substr($scriptName, 0, $position);
            break;
        }
    }

    // Saat proyek dijalankan dari root melalui .htaccess XAMPP/Laragon,
    // Apache mengirim request ke public/index.php. Folder public adalah
    // implementasi internal, bukan bagian dari URL yang dilihat pengguna.
    // Buang dari base path, termasuk saat base path berasal dari marker route
    // di atas (mis. /portohub/public -> /portohub).
    if (str_ends_with($basePath, '/public')) {
        $basePath = substr($basePath, 0, -strlen('/public'));
    }

    if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
        $basePath = '';
    }

    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}
