<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\Product;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use app\models\CfgIoSystemBranchUser;
use app\modules\apiv1\models\CfgIoSystemBranch;
use app\modules\apiv1\models\ProductStore; // stock de los productos
use app\modules\apiv1\models\ProductBranch; // configuracion de los productos

use sizeg\jwt\Jwt;

class ProductController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Product';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];   // personalizar metodo "actionListar" sera el actual ahora

        return $actions;
    }

    public function actionListar()
    {
        $products = Product::find()->orderBy(['dateCreate' => SORT_ASC])->all();
    
        $productStoreList = ProductStore::find()->all();
        $productBranchList = ProductBranch::find()->all();
    
        foreach ($products as $product) {
            $product->productStores = [];
            foreach ($productStoreList as $store) {
                if ($product->id == $store->id) {
                    $product->productStores[] = $store;
                }
            }
        
            foreach ($productBranchList as $branch) {
                if ($product->id == $branch->id) {
                    $product->productBranch = $branch;
                    break;
                }
            }
        }        
        
        return $products;
    }

    public function actionInsert()
    {
        $this->prepareData();

        $user = Yii::$app->user->identity;
        $product = new Product();
        $product->attributes = Yii::$app->request->post();
        $product->iduser = $user->iduser; 
        if ($product->save()) {
            $product = Product::findOne($product->id);
            return parent::sendResponse(['statusCode' => 201, 'message' => 'product created successfully', 'data' => $product]);
        } else {
            return parent::sendResponse(['statusCode' => 400, 'message' => 'Failed to create product', 'errors' => $product->errors]);
        }
    }

    public function actionEdit($id)
    {
        $product = Product::findOne($id);
        if (!$product) {
            return parent::sendResponse(['statusCode' => 404, 'message' => "product with ID $id not found."]);
        }

        $product->attributes = Yii::$app->request->post();
        if ($product->validate()) {
            if ($product->save()) {
                return parent::sendResponse(['statusCode' => 201, 'message' => 'product updated successfully', 'data' => $product]);
            } else {
                return parent::sendResponse(['statusCode' => 400, 'message' => 'Failed to update product', 'errors' => $product->errors]);
            }
        } else {
            return parent::sendResponse(['statusCode' => 400, 'message' => 'Validation failed', 'errors' => $product->errors]);
        }
    }

    public function actionRemove($id)
    {
        $product = Product::findOne($id);
        if (!$product) {
            return parent::sendResponse(['statusCode' => 404, 'message' => "product with ID $id not found."]);
        }

        if ($product->delete()) {
            return parent::sendResponse(['statusCode' => 201, 'message' => 'product deleted successfully']);
        } else {
            return parent::sendResponse(['statusCode' => 400, 'message' => 'Failed to delete product']);
        }
    }

    public function actionSearchByName($name)
    {
        $query = Product::find()
            ->where(['ILIKE', 'name', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
