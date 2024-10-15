<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 
use yii\filters\AccessControl;
use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;
use app\modules\apiv1\models\SiatBranch;

class SiatbranchController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\SiatBranch';
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
        $result = $this->modelClass::findOne(['id' => $ioSystemBranch->id]);

        return $result;
    }
}
