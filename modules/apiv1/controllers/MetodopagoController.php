<?php

namespace app\modules\apiv1\controllers;

use yii\filters\AccessControl;
use Yii;

class MetodopagoController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\MetodoPago';
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $query = $this->modelClass::find()->where(['actived' => true])->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }
}
