<?php

namespace app\modules\service\controllers;

use yii\data\ActiveDataProvider;
use Yii;

class MetodopagoController extends BaseController
{
    public $modelClass = 'app\modules\service\models\MetodoPago';
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
        
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];

        return $actions;
    }

    public function actionListar()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()->where(['actived' => true])->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['index'])) {
            return parent::sendResponse(['statusCode' => 404, 'message' => 'The requested page does not exist.']);
        }
        return parent::beforeAction($action);
    }
}
