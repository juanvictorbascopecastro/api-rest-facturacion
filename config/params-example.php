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
];
