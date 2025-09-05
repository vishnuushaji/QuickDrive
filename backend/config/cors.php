<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',          // local dev
        'https://quickdrive-9xxc.onrender.com', // Render backend
        'https://quickdrive-1.onrender.com', // Render frontend
        'https://quick-drive-five.vercel.app',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
