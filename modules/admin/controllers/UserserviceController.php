<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\IoSystemBranchService;
use yii\filters\VerbFilter;
use app\modules\service\helpers\DbConnection;
use app\models\CrugeUser;
use app\models\IoSystemBranch;
use yii\web\NotFoundHttpException;
use app\modules\admin\validators\ExpireUserValidator;
use app\modules\admin\models\FormularioModel;
/**
 * Default controller for the `admin` module
 */
class UserserviceController extends Controller
{
    public $enableCsrfValidation = false;
    private $dbUser;
    private $dbPassword;
    private $dbHost;

    public function __construct($id, $module, $config = [])
    {
        $this->dbUser = Yii::$app->params['dbUser'];
        $this->dbPassword = Yii::$app->params['dbPassword'];
        $this->dbHost = Yii::$app->params['dbHost'];

        parent::__construct($id, $module, $config);
    }
    
    public function beforeAction($action) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $services = IoSystemBranchService::find()->all();
        return $services;
    }

    public function actionHabilitar() {
    
        $formModel = new FormularioModel();
        $formModel->attributes = \Yii::$app->request->post();

        if ($formModel->validate()) {
            $user = CrugeUser::findById($formModel->iduserActive);
            if (!$user) {
                throw new NotFoundHttpException("User with iduserActive '$formModel->iduserActive' not found.");
            }
            $ioSystemBranch = IoSystemBranch::findOne(['id' => $formModel->idioSystemBranch]);
            if (!$ioSystemBranch) {
                throw new NotFoundHttpException("ioSystemBrnach with ID '$formModel->idioSystemBranch' not found.");
            }
            try {
                
                $dbConnection = DbConnection::getConnection($ioSystemBranch->dbidentifier, $this->dbUser, $this->dbPassword, $this->dbHost);
                // return $ioSystemBranch;
                $expiration = 30 * 24 * 3600; 
                switch ($formModel->expireToken) {
                    case 1:
                        $expiration = 30 * 24 * 3600; // 30 días * 24 horas * 3600 segundos
                        break;
                    case 12:
                        $expiration = 365 * 24 * 3600; // 365 días * 24 horas * 3600 segundos
                        break;
                    case 24:
                        $expiration = 2 * 365 * 24 * 3600; // 2 años * 365 días * 24 horas * 3600 segundos
                        break;
                    case 3:
                        $expiration = 3 * 30 * 24 * 3600; // 3 meses * 30 días * 24 horas * 3600 segundos
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid value for expireToken: {$formModel->expireToken}");
                }
                $token = $this->generateJwt($formModel->iduserActive, $expiration);
                $dbModel = IoSystemBranchService::findOne(['iduserActive' => $formModel->iduserActive]);

                if ($dbModel) {
                    $dbModel->idioSystem = $ioSystemBranch->idioSystem;
                    $dbModel->idioSystemBranch = $formModel->idioSystemBranch;
                    $dbModel->token = (string) $token;
                    $dbModel->expireToken = date('Y-m-d H:i:s', time() + $expiration);
                } else {
                    // Si no existe, crear un nuevo registro
                    $dbModel = new IoSystemBranchService();
                    $dbModel->iduserActive = $formModel->iduserActive;
                    $dbModel->idioSystem = $ioSystemBranch->idioSystem;
                    $dbModel->idioSystemBranch = $formModel->idioSystemBranch;
                    $dbModel->token = (string) $token;
                    $dbModel->expireToken = date('Y-m-d H:i:s', time() + $expiration);
                }
    
                // Guardar el modelo
                if ($dbModel->save()) {
                    return [
                        'status' => 'success',
                        'message' => $dbModel->isNewRecord ? 'Service created successfully' : 'Service updated successfully',
                        'token' => (string) $token // Incluye el token en la respuesta si lo deseas
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => $dbModel->isNewRecord ? 'Failed to create service' : 'Failed to update service',
                        'errors' => $dbModel->errors
                    ];
                }
                
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Database connection failed!',
                    'error' => $e->getMessage(),
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $formModel->errors
            ];
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
