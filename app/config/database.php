<?php

declare(strict_types=1);



function environmentValue(string $name, string $default): string
{
    $value = getenv($name);

    return $value === false || $value === '' ? $default : $value;
}

return [
    'host' => environmentValue('DB_HOST', '127.0.0.1'),
    'port' => environmentValue('DB_PORT', '3306'),
    'name' => environmentValue('DB_NAME', 'portfolio_hub'),
    'username' => environmentValue('DB_USER', 'root'),
    'password' => environmentValue('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
