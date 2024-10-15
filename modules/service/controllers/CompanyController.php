<?php

namespace app\modules\service\controllers;

use Yii;
use yii\web\Request;
use app\modules\service\controllers\BaseController;
use app\models\IoSystemBranchService;

class CompanyController extends BaseController
{
    public $modelClass = 'app\models\IoSystemBranchService';

    public function actions()
    {
        $actions = parent::actions();
        unset(
            $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['options']
        );

        return $actions;
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['update'])) {
            return parent::sendResponse(['statusCode' => 404, 'message' => 'The requested page does not exist.']);
        }
        return parent::beforeAction($action);
    }

    public function actionUpdate()
    {
        $user = Yii::$app->user->identity;
        $ioSystemBranchService = IoSystemBranchService::findOne(['iduserActive' => $user->id]);
        if (empty($ioSystemBranchService)) {
            return parent::sendResponse([
                'statusCode' => 404, 
                'message' => 'User with iduserActive ' . $user->id . ' not found.',
            ]);
        }
       
        $request = new Request();
        $Authorization = $request->getHeaders()->get('Authorization');
        $token = str_replace('Bearer ', '', $Authorization);

        if($token !== $ioSystemBranchService->token) {
            return parent::sendResponse([
                'statusCode' => 401, 
                'message' => 'El token enviado no coincide con el token habilitado a el usuario "' . $user->username . '"',
            ]);
        }

        return $ioSystemBranchService;
    }

    private function decodeJWT($jwt, $secretKey) {
        // Dividir el JWT en sus partes
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
    
        // Decodificar el header y el payload
        $header = json_decode(base64UrlDecode($headerEncoded), true);
        $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    
        // Crear la firma nuevamente
        $signature = base64UrlDecode($signatureEncoded);
        $validSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secretKey, true);
    
        // Verificar la firma
        if (hash_equals($validSignature, $signature)) {
            return $payload;
        } else {
            throw new Exception('Firma JWT no válida');
        }
    }

    private function generateJwt($id, $expiration)
    {
        $jwt = \Yii::$app->jwt; 
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();

        $time = time();

        $jwtParams = \Yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $expiration)
            ->withClaim('uid', $id)
            ->getToken($signer, $key);
    }
}
