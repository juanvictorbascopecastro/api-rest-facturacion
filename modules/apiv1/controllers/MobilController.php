<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class MobilController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'latest_version' => '3.3',
            'update_url'  => 'https://play.google.com/store/apps/details?id=com.iosoftware.fact',
        ];
    }

    public function actionRestaurant()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'latest_version' => '1.0',
            'update_url'  => 'https://play.google.com/store/apps/details?id=com.iosoftware.fact',
        ];
    }
}
