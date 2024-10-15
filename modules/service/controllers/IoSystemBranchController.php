<?php

namespace app\modules\service\controllers;

use Yii;
use yii\filters\AccessControl;
use app\modules\service\models\IoSystemBranchService;
use app\modules\service\models\IoSystemBranch;

/**
 * Default controller for the `apiv1` module
 */
class IoSystemBranchController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\IoSystemBranch';
    
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
        $user = Yii::$app->user->identity;
        $ioSystemBranchService = IoSystemBranchService::findOne(['iduserActive' => $user->iduser]);
        $ioSystemBranch = IoSystemBranch::findOne(['id' => $ioSystemBranchService->idioSystemBranch]);
        return [
            'ioSystemBranch' => $ioSystemBranch,
            'ioSystemBranchService' => $ioSystemBranchService
        ];
    }
}
