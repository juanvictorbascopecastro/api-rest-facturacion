<?php

namespace app\modules\service\controllers\siat;

// use yii\data\ActiveDataProvider;
// use Yii;
use yii\filters\AccessControl;
use app\modules\service\models\SiatUnidadMedida;
use app\modules\service\controllers\BaseController;

class UnidadMedidaController extends BaseController
{
    public $modelClass = 'app\modules\service\models\SiatUnidadMedida';

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