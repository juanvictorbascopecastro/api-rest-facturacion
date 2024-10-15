<?php

namespace app\modules\service\controllers;

use Yii;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\web\Response;
use yii\rest\Controller;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    // Método para retornar el tipo de respuestas
    protected function sendResponse($response)
    {
        Yii::$app->response->statusCode = $response['statusCode'];

        $responseData = [
            'message' => $response['message'],
            'name' => $response['name'] ?? null,
            'errors' => $response['errors'] ?? null,
            'data' => $response['data'] ?? null,
        ];

        return $responseData;
    }
}
