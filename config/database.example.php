<?php
declare(strict_types=1);

return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'dbname' => $_ENV['DB_NAME'] ?? 'your_database_name',
    'username' => $_ENV['DB_USER'] ?? 'your_database_user',
    'password' => $_ENV['DB_PASS'] ?? 'your_database_password',
    'charset' => 'utf8mb4'
];