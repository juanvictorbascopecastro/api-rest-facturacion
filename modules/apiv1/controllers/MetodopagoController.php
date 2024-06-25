<?php

namespace app\modules\apiv1\controllers;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class MetodopagoController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\MetodoPago';
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
        $this->prepareData(); 

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()->where(['actived' => true])->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['index'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return parent::beforeAction($action);
    }
}
