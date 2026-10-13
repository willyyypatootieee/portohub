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
