<?php

namespace app\modules\service;

use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use app\modules\service\behaviors\JwtBehavior;
use app\modules\service\components\TokenValidationBehavior;

/**
 * Módulo de definición para el servicio
 */
class ServiceModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\service\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false; 
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Agregar autenticador y validación de token
        $auth = $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];
        // Remover autenticador temporalmente
        unset($behaviors['authenticator']);

        // Configuración de CORS
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => static::allowedDomains(),
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // Métodos permitidos
                'Access-Control-Allow-Credentials' => true, // Permitir credenciales (si es necesario)
                'Access-Control-Allow-Headers' => ['authorization', 'X-Requested-With', 'content-type'],
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                'Access-Control-Max-Age' => 3600,
            ],
        ];

        // Volver a habilitar autenticador y excluir las opciones
        $behaviors['authenticator'] = $auth;
        $behaviors['authenticator']['except'] = ['options'];

        // Agregar validación de token
        $behaviors['tokenValidation'] = TokenValidationBehavior::class;

        return $behaviors;
    }
    
    public static function allowedDomains() {
        return [$_SERVER["REMOTE_ADDR"], 'http://localhost:5173'];
    }
}
