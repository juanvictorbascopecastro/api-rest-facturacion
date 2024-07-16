<?php

namespace app\modules\service\controllers;


use yii\data\ActiveDataProvider;
use Yii;

use app\models\IoSystemBranchService;
use app\modules\service\models\IoSystemBranch;

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
            $ioSystemBranchService = IoSystemBranchService::findOne(['iduserActive' => $user->iduser]);
            $ioSystemBranch = IoSystemBranch::findOne(['id' => $ioSystemBranchService->idioSystemBranch]);
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
