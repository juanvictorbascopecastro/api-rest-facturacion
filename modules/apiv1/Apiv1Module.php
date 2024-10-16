<?php

namespace app\modules\apiv1;

use Yii;
use sizeg\jwt\JwtHttpBearerAuth;
use app\modules\apiv1\behaviors\JwtBehavior;

class Apiv1Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\apiv1\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'except' => ['pdf/*', 'mobil/*'/*, 'validatenit/*'*/],
        ];
        
        return $behaviors;
    }
}
