<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=apiphp;charset=utf8mb4',
        'user' => 'root',
        'pass' => ''
    ],
    'app' => [
        'env' => 'local',
        'debug' => true,
        'base_url' => 'http://localhost/api-php-native/Leri/public',
        'jwt_secret' => 'password_hash_leri_99_numerik_api',
        'allowed_origins' => ['http://localhost:3000', 'http://localhost']
    ]
    ];