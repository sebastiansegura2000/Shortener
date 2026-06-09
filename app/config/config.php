<?php

return [
    'app_url' => getenv('APP_URL') ?: 'http://localhost:8080',

    'database' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => getenv('DB_PORT') ?: '5432',
        'name' => getenv('DB_NAME') ?: 'link_shortener_db',
        'user' => getenv('DB_USER') ?: 'postgres',
        'password' => getenv('DB_PASSWORD') ?: 'postgres',
    ],
];