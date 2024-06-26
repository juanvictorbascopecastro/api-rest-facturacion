<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Category;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use yii\web\NotFoundHttpException;

use sizeg\jwt\Jwt;

class CategoryController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Category';

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
            'query' => $this->modelClass::find()->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
    }


    public function actionInsert()
    {
        $this->prepareData();

        $user = Yii::$app->user->identity;
        $category = new Category();
        $category->attributes = Yii::$app->request->post();
        $category->iduser = $user->iduser; 
        if ($category->save()) {
            $category = Category::findOne($category->id);
            return ['status' => 201, 'message' => 'Category created successfully', 'data' => $category];
        } else {
            return ['status' => 400, 'message' => 'Failed to create category', 'errors' => $category->errors];
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
                return ['status' => 201, 'message' => 'Category updated successfully', 'data' => $category];
            } else {
                return ['status' => 400, 'message' => 'Failed to update category', 'errors' => $category->errors];
            }
        } else {
            return ['status' => 400, 'message' => 'Validation failed', 'errors' => $category->errors];
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
            return ['status' => 201, 'message' => 'Category deleted successfully'];
        } else {
            return ['status' => 400, 'message' => 'Failed to delete category'];
        }
    }

}
