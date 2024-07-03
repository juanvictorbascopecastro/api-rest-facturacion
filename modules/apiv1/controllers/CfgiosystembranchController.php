<?php

namespace app\modules\apiv1\controllers;


use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

use app\models\CfgIoSystemBranchUser;
use app\modules\apiv1\models\CfgIoSystemBranch;

/**
 * Default controller for the `apiv1` module
 */
class CfgIoSystemBranchController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\CfgIoSystemBranch';
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
            $ioSystemBranchUser = CfgIoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
            $ioSystemBranch = CfgIoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);
            return $ioSystemBranch;
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
