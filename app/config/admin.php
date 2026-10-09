<?php

declare(strict_types=1);



// admin config, if jika tidak ada environment variable, maka akan menggunakan default value username admin dan password admin123

return [
    'username' => environmentValue('ADMIN_USERNAME', 'admin'),
    'password' => environmentValue('ADMIN_PASSWORD', 'admin123'),
];
