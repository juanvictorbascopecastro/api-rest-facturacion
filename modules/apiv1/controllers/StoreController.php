<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController;

class StoreController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Store';

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
