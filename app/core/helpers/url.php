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

    // Saat proyek dijalankan dari root melalui .htaccess XAMPP/Laragon,
    // Apache mengirim request ke public/index.php. Folder public adalah
    // implementasi internal, bukan bagian dari URL yang dilihat pengguna.
    if (str_ends_with($basePath, '/public')) {
        $basePath = substr($basePath, 0, -strlen('/public'));
    }

    foreach ($markers as $marker) {
        $position = strpos($scriptName, $marker);

        if ($position !== false) {
            $basePath = substr($scriptName, 0, $position);
            break;
        }
    }

    if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
        $basePath = '';
    }

    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}
