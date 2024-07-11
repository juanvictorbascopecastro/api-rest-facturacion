<?php

namespace app\modules\apiv1\controllers;


use yii\data\ActiveDataProvider;
use Yii;

use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;

/**
 * Default controller for the `apiv1` module
 */
class IosystembranchController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\IoSystemBranch';
    public function actions()
    {
        $actions = parent::actions();
        unset(
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['options']
        );
        
        $actions['index']['prepareDataProvider'] = function($action) {
            $user = Yii::$app->user->identity;
            $ioSystemBranchUser = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
            $ioSystemBranch = IoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);
            return $ioSystemBranch;
        };

        return $actions;
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['index'])) {
            return parent::sendResponse(['statusCode' => 404, 'message' => 'The requested page does not exist.']);
        }
        return parent::beforeAction($action);
    }
}
