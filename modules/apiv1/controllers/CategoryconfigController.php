<?php

namespace app\modules\apiv1\controllers;

use yii\data\ActiveDataProvider;
use Yii;
use app\modules\apiv1\controllers\BaseController; 

class CategoryconfigController extends BaseController
{
    public $modelClass = 'app\models\CategoryConfig';
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],        
                'insert' => ['POST'],     
                'edit' => ['PUT', 'PATCH'], 
                'remove' => ['DELETE'],   
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
