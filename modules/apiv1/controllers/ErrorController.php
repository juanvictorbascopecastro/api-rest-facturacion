<?php

namespace app\modules\apiv1\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class ErrorController extends Controller
{
    public function actionIndex()
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            $statusCode = ($exception instanceof HttpException) ? $exception->statusCode : 500;

            return $this->asJson([
                'statusCode' => $statusCode,
                'message' => $exception->getMessage(),
                'name' => $exception->getName(),
            ]);
        }

        return $this->asJson([
            'statusCode' => 500,
            'message' => 'Internal Server Error',
        ]);
    }
}
