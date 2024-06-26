<?php
namespace app\modules\service\controllers;

use Yii;
use app\modules\service\helpers\DbConnection;
use app\models\Category;
use yii\data\ActiveDataProvider;
use app\modules\service\controllers\BaseController; 
use app\models\CfgIoSystemBranchService;
use app\modules\service\models\CfgIoSystemBranch;

use sizeg\jwt\Jwt;

class CategoryController extends BaseController
{
    public $modelClass = 'app\modules\service\models\Category';
    private $dbUser;
    private $dbPassword;

    public function __construct($id, $module, $config = [])
    {
        // Obtener los valores de params.php
        $this->dbUser = Yii::$app->params['dbUser'];
        $this->dbPassword = Yii::$app->params['dbPassword'];

        parent::__construct($id, $module, $config);
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];   // personalizar metodo "actionListar" sera el actual ahora

        return $actions;
    }

    public function actionListar()
    {
        $this->prepareData();

        return new ActiveDataProvider([
            'query' => $this->modelClass::find(),
            'pagination' => false,
        ]);

    }

    public function actionInsert()
    {
        $this->prepareData();

        $category = new Category();
        $category->attributes = Yii::$app->request->post();
        if ($category->save()) {
            return ['status' => 'success', 'message' => 'Category created successfully', 'data' => $category];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create category', 'errors' => $category->errors];
        }
    }

    public function actionEdit($id)
    {
        $this->prepareData();

        $category = Category::findOne($id);
        if (!$category) {
            throw new NotFoundHttpException("Category with ID $id not found.");
        }

        $category->attributes = Yii::$app->request->post();
        if ($category->validate()) {
            if ($category->save()) {
                return ['status' => 'success', 'message' => 'Category updated successfully', 'data' => $category];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update category', 'errors' => $category->errors];
            }
        } else {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $category->errors];
        }
    }

    public function actionRemove($id)
    {
        $this->prepareData();

        $category = Category::findOne($id);
        if (!$category) {
            throw new NotFoundHttpException("Category with ID $id not found.");
        }

        if ($category->delete()) {
            return ['status' => 'success', 'message' => 'Category deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete category'];
        }
    }

    private function prepareData()
    {
        $user = Yii::$app->user->identity;
        $ioSystemBranchService = CfgIoSystemBranchService::findOne(['iduserActive' => $user->iduser]);

        if (!$ioSystemBranchService) {
            throw new \yii\web\NotFoundHttpException("Service with iduserActive " . $user->iduserActive . " not found.");
        }

        $ioSystemBranch = CfgIoSystemBranch::findOne(['id' => $ioSystemBranchService->idioSystemBranch]);
        
        if (!$ioSystemBranch) {
            throw new \yii\web\NotFoundHttpException("Service with idioSystemBranch from iduserActive" . $user->iduser . " not found.");
        }
        
        $token = $this->getAuthToken();
        if (strtolower($token) !== strtolower($ioSystemBranchService->token)) {
            throw new \yii\web\ForbiddenHttpException("El token '" . $token . "' no es válido para este servicio!");
        }

        $db = DbConnection::getConnection($ioSystemBranch->cfgIoSystem->dbidentifier, $this->dbUser, $this->dbPassword);
        
        if (!$db) {
            throw new \yii\base\InvalidConfigException("No se pudo establecer la conexión a la base de datos.");
        }

        $this->modelClass::setCustomDb($db); // Asignar la conexión personalizada al modelo
    }

    private function getAuthToken()
    {
        $jwt = new Jwt();
        $token = Yii::$app->request->headers->get('Authorization');
        
        if ($token !== null) {
            $token = str_replace('Bearer ', '', $token);
        }
        
        return $token;
    }  
}
