<?php

namespace app\modules\auth\controllers;

use Yii;
use app\modules\auth\controllers\BaseController;
use app\models\CrugeUser;
use sizeg\jwt\Jwt;

use app\modules\auth\models\LoginForm;
use app\models\IoSystemBranchUser;
/**
 * Login controller for the `auth` module
 */
class LoginController extends BaseController
{  
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $loginForm = new LoginForm();
        $loginForm->attributes = \Yii::$app->request->post();
        if ($loginForm->validate()) {
            if($loginForm->email) $user = CrugeUser::findOne(['email' => $loginForm->email]);
            else $user = CrugeUser::findOne(['username' => $loginForm->username]);

            if ($user && $user->validatePassword($loginForm->password)) {
                $dataActive = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);

                if(!$dataActive) {
                    return parent::sendResponse("Este usuario no está habilitado para el uso de la API-REST!", 401);
                }
                $token = $this->generateJwt($user);
                return [
                   "user" => [
                        "iduser" => $user->iduser,
                        "regdate" => $user->regdate,
                        "actdate" => $user->actdate,
                        "logondate" => $user->logondate,
                        "username" => $user->username,
                        "email" => $user->email,
                        "authkey" => $user->authkey,
                        "state" => $user->state,
                        "totalsessioncounter" => $user->totalsessioncounter,
                        "currentsessioncounter" => $user->currentsessioncounter,
                        "temporal" => $user->temporal,
                        "fullname" => $user->fullname,
                        "name" => $user->name,
                        "lastname" => $user->lastname,
                        "surname" => $user->surname,
                    ],
                    "token" => (string) $token,
                ];
                               
            } else {
                return parent::sendResponse("Invalid username or password", 401, "Unauthorized");
            }
        } else {
            return parent::sendResponse([
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $loginForm->errors
    ]);
        }
    }

    private function generateJwt(CrugeUser $user) {
		$jwt = Yii::$app->jwt;
		$signer = $jwt->getSigner('HS256');
		$key = $jwt->getKey();
		$time = time();

		$jwtParams = Yii::$app->params['jwt'];

		return $jwt->getBuilder()
			->issuedBy($jwtParams['issuer'])
			->permittedFor($jwtParams['audience'])
			->identifiedBy($jwtParams['id'], true)
			->issuedAt($time)
			->expiresAt($time + $jwtParams['expire'])
			->withClaim('uid', $user->iduser)
			->getToken($signer, $key);
	}   
}
