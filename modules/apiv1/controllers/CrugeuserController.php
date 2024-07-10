<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CrugeuserController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\CrugeUser';

    public function actions()
    {
        $actions = parent::actions();
        unset(
            // $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['options']
        );
        
        $actions['index']['prepareDataProvider'] = function($action) {
            $user = Yii::$app->user->identity;
            $userArray = $user->toArray(); 
            unset($userArray['password']);

            return $userArray;
        };

        return $actions;
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['index'])) {
            return parent::sendResponse([
                'message' => 'The requested page does not exist.',
                'statusCode' => 404
            ]);
        }
        return parent::beforeAction($action);
    }
}
