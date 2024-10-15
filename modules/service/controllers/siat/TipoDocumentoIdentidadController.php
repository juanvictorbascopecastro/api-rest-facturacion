<?php

namespace app\modules\service\controllers\siat;


use app\modules\service\controllers\BaseController;

/**
 * Default controller for the `service` module
 */
class TipoDocumentoIdentidadController extends BaseController
{
    public $modelClass = 'app\modules\service\models\SiatTipoDocumentoIdentidad';

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
