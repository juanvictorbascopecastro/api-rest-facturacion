<?php

namespace app\modules\service\components;

use yii\base\Behavior;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use app\models\IoSystemBranchService;

class TokenValidationBehavior extends Behavior
{
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'checkToken',
        ];
    }

    public function checkToken($event)
    {
        $authHeader = \Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $token = $matches[1];

            if (!IoSystemBranchService::find()->where(['token' => $token])->exists()) {
                throw new UnauthorizedHttpException('Este token ha sido revocado.');
            }
        } else {
            throw new UnauthorizedHttpException('No se proporciona ningún token.');
        }
    }
}
