<?php

namespace app\modules\auth\controllers;

use yii\web\Controller; 
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;

class BaseController extends Controller
{
    public static function allowedDomains() {
        return [$_SERVER["REMOTE_ADDR"], "http://localhost:4200"];
    }
    // configuracion del CORS
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => static::allowedDomains(),
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Headers' => ['authorization', 'X-Requested-With', 'content-type'],
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-Pagination-Page-Count']
            ],
        ];
        return $behaviors;
    }

    public function beforeAction($action) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }
}