<?php

namespace app\modules\auth\controllers;

use Yii;
use yii\web\Controller; 
use app\modules\auth\controllers\BaseController; 

class PruebaController extends BaseController
{ 
    public $enableCsrfValidation = false;
    
    public function actionIndex()
    {
        $attributes = \Yii::$app->request->post();
        return [
            'status' => 200,
            'message' => 'Correcto!!!',
            'data' => $attributes
        ];
    }  
}
