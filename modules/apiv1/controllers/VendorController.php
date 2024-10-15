<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 
use app\modules\apiv1\models\Vendor;

class VendorController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Vendor';

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