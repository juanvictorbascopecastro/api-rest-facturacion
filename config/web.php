<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'yfhY3qBDs0wdq0Jw3uqEs54q7NV0EbVr',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        
        // conexiones a las bases de datos
        // 'empresa8_sb0' => $db['empresa8_sb0'],
        'iooxs_access' => $db['iooxs_access'],
        'iooxs_io' => $db['iooxs_io'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'class' => 'yii\rest\UrlRule',
                'GET,HEAD service/categories' => 'service/category/index',
                'POST service/categories' => 'service/category/insert',
                'PUT,PATCH service/categories/<id:\d+>' => 'service/category/edit',
                'DELETE service/categories' => 'service/category/remove',
                'GET apiv1/customers/actionSearchByDoc/<numeroDocumento>' => 'apiv1/customer/search-by-doc',
                'GET apiv1/customers/actionSearchByName/<name>' => 'apiv1/customer/search-by-name',
                // ADMIN
            ],
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'CLAVE-SECRETA',  // Cambia esto por tu clave secreta
            'jwtValidationData' => \app\modules\auth\components\JwtValidationData::class,
        ],
    ],
    'modules' => [
        'apiv1' => [
            'class' => 'app\modules\apiv1\Apiv1Module',
        ],
        'auth' => [
            'class' => 'app\modules\auth\AuthModule',
        ],
        'service' => [
            'class' => 'app\modules\service\ServiceModule',
        ],
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
