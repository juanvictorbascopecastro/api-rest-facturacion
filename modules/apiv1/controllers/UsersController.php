<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 
use yii\data\ActiveDataProvider;
use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;
use app\modules\apiv1\models\SiatBranch;
use app\modules\apiv1\models\CrugeUser;

class UsersController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\CrugeUser';

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

        $ioSystemBranchUser = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
        $ioSystemBranch = IoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);

        if ($ioSystemBranch) {
            $ioSystemBranchUsers = IoSystemBranchUser::find()
                ->select('iduserActive') 
                ->where(['idioSystemBranch' => $ioSystemBranch->id])
                ->asArray()
                ->all();

            $iduserActiveList = array_column($ioSystemBranchUsers, 'iduserActive');

            $crugeUsers = CrugeUser::find()
                ->where(['iduser' => $iduserActiveList])
                ->asArray()
                ->all();

            return $crugeUsers;
        }

        return parent::sendResponse([
            'statusCode' => 404,
            'message' => 'Sucursal no encontrada',
        ]);
    }
}
