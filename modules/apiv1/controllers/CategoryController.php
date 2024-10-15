<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Category;
use app\models\Product;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use yii\web\NotFoundHttpException;

use sizeg\jwt\Jwt;

class CategoryController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Category';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],        
                'insert' => ['POST'],     
                'edit' => ['PUT', 'PATCH'], 
                'remove' => ['DELETE'],   
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $query = $this->modelClass::find()->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

    public function actionInsert()
    {
        $user = Yii::$app->user->identity;
        $category = new Category();
        $category->attributes = Yii::$app->request->post();
        $category->iduser = $user->iduser; 
        if(!$category->validate()) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Validacion de datos!',
                'errors' => $category->errors
            ]);
        }
        if ($category->save()) {
            $category = Category::findOne($category->id);
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

        $productCount = Product::find()
            ->where(['idcategory' => $id])
            ->count();
        
        if($productCount > 0) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => "No es posible eliminarla. Hay " . $productCount . " productos asignados a esta categoría.",
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
