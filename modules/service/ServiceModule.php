<?php

namespace app\modules\service;

use sizeg\jwt\JwtHttpBearerAuth;
use app\modules\service\behaviors\JwtBehavior;
/**
 * service module definition class
 */
class ServiceModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\service\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [ 'class' => JwtHttpBearerAuth::class ];
        
        return $behaviors;
    }
}
