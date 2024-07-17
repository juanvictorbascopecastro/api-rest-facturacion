<?php
namespace app\modules\service\controllers;

use Yii;
use app\models\Unit;
use yii\data\ActiveDataProvider;
use app\modules\service\controllers\BaseController; 
use app\models\CfgIoSystemBranchUser;
use app\modules\service\models\CfgIoSystemBranch;

use sizeg\jwt\Jwt;

class UnitController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Unit';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];   // personalizar metodo "actionListar" sera el actual ahora

        return $actions;
    }

    public function actionListar()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()->orderBy(['order' => SORT_ASC]),
            'pagination' => false,
        ]);
    }
}