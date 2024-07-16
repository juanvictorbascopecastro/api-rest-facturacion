<?php

namespace app\modules\apiv1\controllers;


use yii\data\ActiveDataProvider;
use Yii;

/**
 * Default controller for the `apiv1` module
 */
class SincronizarlistaproductosserviciosController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\SincronizarListaProductosServicios';
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
            $modelClass = $this->modelClass;
            return new ActiveDataProvider([
                'query' => $modelClass::find(),
                'pagination' => false,
            ]);
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
