<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
// echo $keyJWT;

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
            // 'errorAction' => 'site/error',
            'class' => 'yii\web\ErrorHandler',
            'errorAction' => 'apiv1/error/index',
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
        // 'empresa0_api' => $db['empresa0_api'],
        'iooxs_access' => $db['iooxs_access'],
        'iooxs_io' => $db['iooxs_io'],
        'iooxsRoot' => $db['iooxsRoot'],
        'iooxsBranch' => $db['iooxsBranch'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'class' => 'yii\rest\UrlRule',
                // 'GET,HEAD service/siat/unidad-medida' => 'service/siat/siatunidadmedida',
                // 'GET,HEAD service/siat/tipo-documento-identidad' => 'service/siat/siattipodocumentoidentidad',
                // 'GET,HEAD service/siat/metodo-pago' => 'service/siat/siatmetodopago',
                // 'GET,HEAD service/siat/lista-productos-servicios' => 'service/siat/sincronizarlistaproductosservicios',
                // 'GET,HEAD service/siat/actividades' => 'service/siat/sincronizaractividades',
                // 'GET,HEAD service/siat/lista-leyendas-factura' => 'service/siat/sincronizarlistaleyendasfactura',
                // 'GET,HEAD service/siat/branch' => 'service/siat/siatbranch',
                // 'GET,HEAD service/update-token' => 'service/updatetoken',
                'PUT service/company/update-token' => 'service/company/update',
                
                'GET apiv1/customers/actionSearchByDoc/<numeroDocumento>' => 'apiv1/customer/search-by-doc',
                'GET apiv1/customers/actionSearchByName/<name>' => 'apiv1/customer/search-by-name',
                'GET apiv1/sales/actionProductsBySale/<id>' => 'apiv1/sale/product-by-sale',
                'GET apiv1/my-user' => 'apiv1/crugeuser/index',
                // 'GET apiv1/io-system-branch' => 'apiv1/iosystembranch',
                // 'GET service/io-system-branch' => 'service/iosystembranch',
                'GET apiv1/product/actionSearchByName/<name>' => 'apiv1/product/search-by-doc',
               // 'GET,HEAD apiv1/mobil/latest-version' => 'apiv1/mobil/latestversion',
            ],
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => $keyJWT, 
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
        'ioLib' => [
            'class' => 'app\modules\ioLib\Module',
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

//print_r($config);
return $config;
