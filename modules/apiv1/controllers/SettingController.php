<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class MobilController extends Controller
{
    public function actionDevelopment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return {
            
        }
    }

    public function actionProduction()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        return 'https://apirest.app.io.ioox.io/web';
    }
}
