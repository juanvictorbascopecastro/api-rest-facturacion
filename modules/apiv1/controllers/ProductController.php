<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\Product;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use app\models\CfgIoSystemBranchUser;
use app\modules\apiv1\models\CfgIoSystemBranch;
use yii\web\NotFoundHttpException;
use app\modules\apiv1\models\CfgProductStore; // stock de los productos
use app\modules\apiv1\models\CfgProductBranch; // configuracion de los productos

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
        $this->prepareData(false); 
        $products = Product::find()->orderBy(['dateCreate' => SORT_ASC])->all();
    
        $db = $this->prepareData(true); 
        CfgProductStore::setCustomDb($db);
        $cfgProductStores = CfgProductStore::find()->all();

        CfgProductBranch::setCustomDb($db);
        $cfgProductBranchs = CfgProductBranch::find()->all();
    
        foreach ($products as $product) {
            $product->cfgProductStores = [];
            foreach ($cfgProductStores as $store) {
                if ($product->id == $store->id) {
                    $product->cfgProductStores[] = $store;
                }
            }
        
            foreach ($cfgProductBranchs as $branch) {
                if ($product->id == $branch->id) {
                    $product->cfgProductBranch = $branch;
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
            return ['status' => 201, 'message' => 'product created successfully', 'data' => $product];
        } else {
            return ['status' => 400, 'message' => 'Failed to create product', 'errors' => $product->errors];
        }
    }

    public function actionEdit($id)
    {
        $this->prepareData();

        $product = Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException("product with ID $id not found.");
        }

        $product->attributes = Yii::$app->request->post();
        if ($product->validate()) {
            if ($product->save()) {
                return ['status' => 201, 'message' => 'product updated successfully', 'data' => $product];
            } else {
                return ['status' => 400, 'message' => 'Failed to update product', 'errors' => $product->errors];
            }
        } else {
            return ['status' => 400, 'message' => 'Validation failed', 'errors' => $product->errors];
        }
    }

    public function actionRemove($id)
    {
        $this->prepareData();

        $product = Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException("product with ID $id not found.");
        }

        if ($product->delete()) {
            return ['status' => 201, 'message' => 'product deleted successfully'];
        } else {
            return ['status' => 400, 'message' => 'Failed to delete product'];
        }
    }

    public function actionSearchByName($name)
    {
        $this->prepareData();

        $query = Product::find()
            ->where(['ILIKE', 'name', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
