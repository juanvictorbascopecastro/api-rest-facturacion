<?php

namespace app\modules\apiv1\controllers;

use yii\filters\AccessControl;
use Yii;

use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;

class IoSystemBranchController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\IoSystemBranch';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'], 
                    'verbs' => ['GET'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $ioSystemBranchUser = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
        $ioSystemBranch = IoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);
        return $ioSystemBranch;
    }
}
