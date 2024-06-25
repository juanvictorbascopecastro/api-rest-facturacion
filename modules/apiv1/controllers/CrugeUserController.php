<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
/**
 * Default controller for the `apiv1` module
 */
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
            $userArray = $user->toArray(); // removemos lo que es password
            unset($userArray['password']);

            return $userArray;
        };

        return $actions;
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['index'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return parent::beforeAction($action);
    }
}
