<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'jwt' => [
        'issuer' => 'https://api.example.com',
        'audience' => 'https://frontend.example.com',
        'id' => 'UNIQUE-JWT-IDENTIFIER',
        'expire' => 86400,
    ],
    'dbUser' => 'postgres',
    'dbPassword' => '12345678',
    'dbHost' => 'localhost',
    'cloudinary' => [
        'api_key' => 'your_api_key',
        'api_secret' => 'your_api_secret',
        'cloud_name' => 'your_cloud_name',
    ],
    'mail' => [
        'emailAddress' => 'example@gmail.com',
        'emailKey' => '***********',
        'emailHost' => 'smtp.gmail.com'
    ],
    'whatsapp' => [
        'token' => '',
        'phone' => ''
    ]
];
