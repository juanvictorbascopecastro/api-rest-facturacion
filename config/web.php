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
            'cookieValidationKey' => 'i4BTTS4ycVgJhAW1VJp3jo_4smEIpU00',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\CrugeUser',
            'enableAutoLogin' => true,
            'enableSession' => false, // Si estás usando tokens, probablemente no uses sesiones
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
        // 'empresa1_sb0' => $db['empresa1_sb0'],
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
                'GET apiv1/sales/actionProductsBySale/<id>' => 'apiv1/sale/product-by-sale',
                ['pattern' => 'apiv1/my-user', 'route' => 'apiv1/crugeuser/index'],
                'apiv1/cfgiosystembranch' => 'apiv1/cfg-io-system-branch/index',
            ],
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'CLAVE-SECRETA',  // Cambia esto por tu clave secreta
            'jwtValidationData' => \app\modules\auth\components\JwtValidationData::class,
        ],
        // 'jwtToken' => [
        //     'class' => 'app\modules\apiv1\components\JwtToken',
        // ],
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
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}


return $config;
