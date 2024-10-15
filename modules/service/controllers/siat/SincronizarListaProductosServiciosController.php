<?php

namespace app\modules\service\controllers\siat;

use yii\data\ActiveDataProvider;
use Yii;
use app\modules\service\controllers\BaseController;

class SincronizarListaProductosServiciosController extends BaseController
{
    public $modelClass = 'app\modules\service\models\SincronizarListaProductosServicios';

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
        $query = $this->modelClass::find()->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }
}
