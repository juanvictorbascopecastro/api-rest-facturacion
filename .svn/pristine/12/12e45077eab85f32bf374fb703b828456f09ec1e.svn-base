<?php
namespace app\modules\service\controllers;

use Yii;
use app\models\Category;
use yii\data\ActiveDataProvider;
use app\modules\service\controllers\BaseController; 

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
        return new ActiveDataProvider([
            'query' => $this->modelClass::find(),
            'pagination' => false,
        ]);

    }

    public function actionInsert()
    {
        $category = new Category();
        $category->attributes = Yii::$app->request->post();
        if ($category->save()) {
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Category created successfully',
                'data' => $category,
            ]);
        } else {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Failed to create category',
                'errors' => $category->errors,
            ]);
        }
    }

    public function actionEdit($id)
    {
        $category = Category::findOne($id);
        if (!$category) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Category with ID $id not found.",
            ]);
        }

        $category->attributes = Yii::$app->request->post();
        if ($category->validate()) {
            if ($category->save()) {
                return parent::sendResponse([
                    'statusCode' => 200,
                    'message' => 'Category updated successfully',
                    'data' => $category,
                ]);
            } else {
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'Failed to update category',
                    'errors' => $category->errors,
                ]);
            }
        } else {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $category->errors,
            ]);
        }
    }

    public function actionRemove($id)
    {
        $category = Category::findOne($id);
        if (!$category) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Category with ID $id not found.",
            ]);
        }

        if ($category->delete()) {
            return parent::sendResponse([
                'statusCode' => 200,
                'message' => 'Category deleted successfully',
            ]);
        } else {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Failed to delete category',
            ]);
        }
    } 
}
