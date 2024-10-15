<?php

namespace app\modules\apiv1\controllers;

use yii\filters\AccessControl;
use Yii;

/**
 * Default controller for the `apiv1` module
 */
class SincronizarlistaproductosserviciosController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\SincronizarListaProductosServicios';
    
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
        $query = $this->modelClass::find();
        return $query->all();
    }
}