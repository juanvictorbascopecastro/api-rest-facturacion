<?php

namespace app\modules\service\controllers\siat;

use Yii;
use app\modules\service\controllers\BaseController;

use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;
use app\modules\apiv1\models\SiatBranch;

class SiatBranchController extends BaseController
{
    public $modelClass = 'app\models\SiatBranch';
    
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
        $result = $this->modelClass::findOne(['id' => $ioSystemBranch->id]);

        return $result;
    }
}
