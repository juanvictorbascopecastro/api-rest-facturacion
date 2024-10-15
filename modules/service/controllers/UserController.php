<?php

namespace app\modules\service\controllers;


use yii\data\ActiveDataProvider;
use Yii;

class UserController extends BaseController
{
    public $modelClass = 'app\models\CrugeUser';

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
        $userArray = $user->toArray([
            'iduser',
            'regdate',
            'actdate',
            'logondate',
            'username',
            'email',
            'authkey',
            'state',
            'totalsessioncounter',
            'currentsessioncounter',
            'temporal',
            'fullname',
            'name',
            'lastname',
            'surname'
        ]);

        return $userArray;
    }
}
