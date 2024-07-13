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
use app\modules\apiv1\models\ProductImage;

use yii\web\UploadedFile;
use app\modules\apiv1\helpers\UploadFile;

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
        $user = Yii::$app->user->identity;
        $product = new Product();
        $product->attributes = Yii::$app->request->post();
        $product->iduser = $user->iduser;
    
        $uploadedFile = UploadedFile::getInstanceByName('image');
        $imageUrl = null;
        $publicId = null;
    
        if ($uploadedFile) {
            $validationResult = UploadFile::validateFile($uploadedFile);
            if ($validationResult['error']) {
                return parent::sendResponse(['statusCode' => 400, 'message' => $validationResult['message'], 'error' => $validationResult['code']]);
            }
    
            $uploadResult = UploadFile::uploadToCloudinary($uploadedFile);
            if ($uploadResult['error']) {
                return parent::sendResponse(['statusCode' => 500, 'message' => $uploadResult['message'], 'error' => $uploadResult['details']]);
            }
    
            $imageUrl = $uploadResult['url'];
            $publicId = $uploadResult['public_id'];
        }
    
        // Guardar Product
        if ($product->save()) {
            $product = Product::findOne($product->id);
    
            // Si se subió una imagen a Cloudinary, guardar ProductImage
            if ($imageUrl) {
                $productImage = new ProductImage();
                $productImage->idproduct = $product->id; // Asignar el ID del producto
                $productImage->imagepath = $imageUrl; // Asignar la URL de la imagen
                $productImage->name = $publicId; // Asignar el public_id como nombre
                if (!$productImage->save()) {
                    return parent::sendResponse(['statusCode' => 500, 'message' => 'No se pudo guardar ProductImage', 'errors' => $productImage->errors]);
                }
            }
    
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Producto creado con éxito',
                'data' => $product
            ]);
        } else {
            return parent::sendResponse(['statusCode' => 400, 'message' => 'No se pudo crear el producto', 'errors' => $product->errors]);
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
                $productStoreList = ProductStore::find()->where(['id' => $product->id])->all();
                $productBranchList = ProductBranch::find()->where(['id' => $product->id])->all();

                $product->productStores = [];
                foreach ($productStoreList as $store) {
                    $product->productStores[] = $store;
                }
    
                if (!empty($productBranchList)) {
                    $product->productBranch = $productBranchList[0]; // Suponiendo que solo hay una entrada relevante
                }
    
                return parent::sendResponse([
                    'statusCode' => 201,
                    'message' => 'Product updated successfully',
                    'data' => $product
                ]);
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
            return parent::sendResponse(['statusCode' => 404, 'message' => "Product with ID $id not found."]);
        }

        $productImages = ProductImage::find()->where(['idproduct' => $id])->all();
        
        $errors = [];
        foreach ($productImages as $image) {
            $deleteResult = UploadFile::deleteFromCloudinary($image->imagepath);
            if ($deleteResult['error']) {
                $errors[] = $deleteResult['message'];
            }
        }
        // Si hay errores, retornarlos
        if (!empty($errors)) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Failed to delete some images from Cloudinary',
                'errors' => $errors
            ]);
        }

        ProductImage::deleteAll(['idproduct' => $id]);

        if ($product->delete()) {
            return parent::sendResponse(['statusCode' => 201, 'message' => 'Product deleted successfully']);
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
