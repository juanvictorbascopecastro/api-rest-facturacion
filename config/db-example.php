<?php

return [
    'iooxs_access' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname=iooxs_access',
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),
    'iooxs_io' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname=iooxs_io',
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),
];
