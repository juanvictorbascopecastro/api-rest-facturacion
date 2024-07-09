<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\Cors;
use sizeg\jwt\JwtHttpBearerAuth;
use app\models\CrugeUser;

use app\modules\apiv1\helpers\DbConnection;
use app\models\CfgIoSystemBranchUser;
use app\modules\apiv1\models\CfgIoSystemBranch;

class BaseController extends ActiveController
{
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

    public static function allowedDomains() {
        return [$_SERVER["REMOTE_ADDR"], "http://localhost:4200"];
    }
    // configuracion del CORS
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => static::allowedDomains(),
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Headers' => ['authorization', 'X-Requested-With', 'content-type'],
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-Pagination-Page-Count']
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function init() {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }

    protected function renderException($exception)
    {
        $response = Yii::$app->getResponse();
        $response->data = [
            'success' => false,
            'error' => $exception->getMessage(),
            'code' => $exception instanceof \yii\web\HttpException ? $exception->statusCode : $exception->getCode(),
        ];
        $response->setStatusCodeByException($exception);
        $response->send();
    }
    
    // metodo para obtener el token
    protected function getAuthToken()
    {
        $jwt = new Jwt();
        $token = Yii::$app->request->headers->get('Authorization');
        
        if ($token !== null) {
            $token = str_replace('Bearer ', '', $token);
        }
        
        return $token;
    }  
}
