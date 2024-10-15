<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\rest\ActiveController;
use app\modules\apiv1\controllers\BaseController; 


class StatusTableController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\StatusTable';

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
        $query = $this->modelClass::find()->one();
        return $query;
    }
}
