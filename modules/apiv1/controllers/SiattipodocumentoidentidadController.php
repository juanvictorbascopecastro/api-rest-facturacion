<?php

namespace app\modules\apiv1\controllers;

use Yii;
use yii\filters\AccessControl;

/**
 * Default controller for the `apiv1` module
 */
class SiattipodocumentoidentidadController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\SiatTipoDocumentoIdentidad';
    
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
