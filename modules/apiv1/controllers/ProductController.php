<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\Product;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use app\modules\apiv1\models\ProductStore; // stock de los productos
use app\modules\apiv1\models\ProductBranch; // configuracion de los productos
use app\modules\apiv1\models\Productimage;

use yii\web\UploadedFile;
use app\modules\apiv1\helpers\UploadFile;
use app\modules\apiv1\models\SincronizarListaProductosServicios;
use app\modules\apiv1\models\Store;
use app\modules\apiv1\models\ViewProduct;
use app\models\Status;

use yii\data\Pagination;
use app\models\Productstock;

use app\modules\apiv1\models\dto\ProductDTO;

class ProductController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Product';
   
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [ 
                'insert' => ['POST'],     
                'edit' => ['PUT', 'PATCH'], 
                'remove' => ['DELETE'], 
                'search-by-name' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionInsert()
    {
        $transaction = Yii::$app->iooxsBranch->beginTransaction();
        try {
            $user = Yii::$app->user->identity;
            $productDTO = new ProductDTO();
            $productDTO->scenario = ProductDTO::SCENARIO_CREATE;
    
            if ($productDTO->load(Yii::$app->request->post(), '') && $productDTO->validate()) {
                // Registrar producto
                $product = new Product();
                $product->idcategory = $productDTO->idcategory;
                $product->idunit = $productDTO->idunit;
                $product->name = $productDTO->name;
                $product->price = $productDTO->price;
                $product->barcode = $productDTO->barcode;
                $product->recycleBin = $productDTO->recycleBin;
                $product->idstatus = $productDTO->idstatus;
                $product->description = $productDTO->description;
                $product->iduser = $user->iduser;
                if(empty($productDTO->idstatus)) {
                    $product->idstatus = (new Status)->ACTIVO;
                }

                // verificar sincronizar lista productos
                if ($productDTO->codigoProducto) {
                    $listaProductoServicio = SincronizarListaProductosServicios::find()
                        ->where(['codigoProducto' => $productDTO->codigoProducto])
                        ->one();
    
                    if ($listaProductoServicio !== null) {
                        $product->idsincronizarListaProductosServicios = $listaProductoServicio->id;
                    } else {
                        throw new \Exception('El código de producto no es válido.');
                    }
                } else {
                    $listaProductoServicio = SincronizarListaProductosServicios::find()
                        ->where(['is not', 'order', null])
                        ->orderBy(['order' => SORT_ASC])
                        ->one();
    
                    if ($listaProductoServicio !== null) {
                        $product->idsincronizarListaProductosServicios = $listaProductoServicio->id;
                    }
                }
                // ahora obtener el estado
                $modelStatus = Status::findOne(['status' => 'ACTIVO']);
                if (!$modelStatus) {
                    throw new \yii\web\NotFoundHttpException("Status 'ACTIVO' not found.");
                }
                $product->idstatus = $modelStatus->id;
    
                if (!$product->save()) {
                    throw new \Exception('No se pudo crear el producto.');
                }
    
                // Registrar ProductBranch
                $productBranch = new ProductBranch();
                $productBranch->id = $product->id;
                $productBranch->controlInventory = $productDTO->controlInventory;
                $productBranch->enableSale = $productDTO->enableSale;
                $productBranch->price = $productDTO->price;
                $productBranch->idstatus = 10;
                $productBranch->priceChange = false;
                $productBranch->cost = $productDTO->cost;
                $productBranch->stockMin = 0;
                $productBranch->stockMax = 0;
    
                if (!$productBranch->save()) {
                    throw new \Exception('No se pudo guardar ProductBranch.');
                }
    
                // Registrar ProductStore
                $cfgStores = Store::find()->all();
                foreach ($cfgStores as $store) {
                    $productStore = new ProductStore();
                    $productStore->id = $product->id;
                    $productStore->stock = 0;
                    $productStore->idstore = $store->id;
                    $productStore->stockReserved = 0;
                    $productStore->allow = true;
    
                    if (!$productStore->validate() || !$productStore->save()) {
                        throw new \Exception('No se pudo guardar ProductStore para la tienda ID ' . $store->id);
                    }
                }
    
                // Manejar la carga de archivos y guardar ProductImage
                $uploadedFile = UploadedFile::getInstanceByName('image');
                if ($uploadedFile) {
                    $validationResult = UploadFile::validateFile($uploadedFile);
                    if ($validationResult['error']) {
                        throw new \Exception($validationResult['message']);
                    }
    
                    $uploadResult = UploadFile::uploadToCloudinary($uploadedFile);
                    if ($uploadResult['error']) {
                        throw new \Exception($uploadResult['message']);
                    }
    
                    $productImage = new Productimage();
                    $productImage->idproduct = $product->id;
                    $productImage->imagepath = $uploadResult['url'];
                    $productImage->name = $uploadResult['public_id'];
                    if (!$productImage->save()) {
                        throw new \Exception('No se pudo guardar ProductImage.');
                    }
                }

                $result = ViewProduct::findOne(['id' => $product->id]);
    
                $transaction->commit();
                return parent::sendResponse([
                    'statusCode' => 201,
                    'message' => 'Producto creado con éxito',
                    'data' => $result
                ]);
    
            } else {
                throw new \Exception('Datos inválidos');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => $e->getMessage(),
                'errors' => $productDTO->errors
            ]);
        }
    }
    
    public function actionEdit($id)
    {
        $transaction = Yii::$app->iooxsBranch->beginTransaction();
        $product = Product::findOne($id);
        if (!$product) {
            return parent::sendResponse([
                'statusCode' => 404, 
                'message' => "Product with ID $id not found.", 
            ]);
        }
    
        $productDTO = new ProductDTO();
        $productDTO->scenario = ProductDTO::SCENARIO_UPDATE;
        $productDTO->attributes = Yii::$app->request->post();
    
        if ($productDTO->validate()) {
            if (!empty($productDTO->code)) {
               
                $existingProduct = Product::find()
                    ->where(['code' => $productDTO->code])
                    ->andWhere(['!=', 'id', $id])
                    ->one();
        
                if ($existingProduct) {
                    return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => "El codigo de producto '{$productDTO->code}' ya esta asignado a otro producto!",
                    ]);
                }
            }

            // Copiar todos los atributos, excepto idstatus
            $productAttributes = $productDTO->attributes;
            unset($productAttributes['idstatus']); // Asegúrate de que idstatus no se copie
    
            $product->attributes = $productAttributes;
    
            if ($productDTO->codigoProducto) {
                $listaProductoServicio = SincronizarListaProductosServicios::find()
                    ->where(['codigoProducto' => $productDTO->codigoProducto])
                    ->one();
                
                if ($listaProductoServicio !== null) {
                    $product->idsincronizarListaProductosServicios = $listaProductoServicio->id;
                } else {
                    $transaction->rollBack();
                    return parent::sendResponse([
                        'statusCode' => 404, 
                        'message' => 'El código de producto ' . $productDTO->codigoProducto . ' no es válido.', 
                    ]);
                }
            }
    
            // Guardar el producto actualizado
            if (!$product->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500, 
                    'message' => 'Failed to update product ', 
                    'errors' => $product->errors
                ]);
            }
    
            // Verificar y actualizar/crear ProductBranch
            $productBranch = ProductBranch::findOne(['id' => $product->id]) ?? new ProductBranch(['id' => $product->id]);
            $productBranch->controlInventory = $productDTO->controlInventory;
            $productBranch->enableSale = $productDTO->enableSale;
            $productBranch->price = $productDTO->price;
            
            if(isset($productDTO->cost)) {
                $productBranch->cost = $productDTO->cost;
            }
    
            if (!$productBranch->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500, 
                    'message' => 'Failed to update or create ProductBranch', 
                    'errors' => $productBranch->errors
                ]);
            }
    
            // Verificar y actualizar/crear ProductStore
            $cfgStores = Store::find()->all(); // Obtener todas las tiendas
            foreach ($cfgStores as $store) {
                $productStore = ProductStore::findOne(['id' => $product->id, 'idstore' => $store->id]) ?? new ProductStore(['id' => $product->id, 'idstore' => $store->id]);
                $productStore->stock = $productStore->stock ?? 0;
                $productStore->stockReserved = $productStore->stockReserved ?? 0;
                $productStore->allow = $productStore->allow ?? true;
    
                if (!$productStore->save()) {
                    $transaction->rollBack();
                    return parent::sendResponse([
                        'statusCode' => 500, 
                        'message' => 'Failed to update or create ProductStore for store ID ' . $store->id, 
                        'errors' => $productStore->errors
                    ]);
                }
            }
    
            $transaction->commit();
            $product = ViewProduct::findOne($id);
    
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } else {
            $transaction->rollBack();
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $productDTO->errors
            ]);
        }
    }
    
    
    public function actionRemove($id)
    {
        $transaction = Yii::$app->iooxsBranch->beginTransaction();
        try {
            $product = Product::findOne($id);
            if (!$product) {
                return parent::sendResponse(['statusCode' => 404, 'message' => "Product with ID $id not found."]);
            }
    
            $modelStatus = Status::findOne(['status' => 'INACTIVO']);
            if (!$modelStatus) {
                return parent::sendResponse(['statusCode' => 404, 'message' => "Status 'INACTIVO' not found."]);
            }
    
            $product->idstatus = $modelStatus->id;
            if (!$product->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500, 
                    'message' => 'Failed to update product status.',
                    'errors' => $product->errors
                ]);
            }
    
            $transaction->commit();
            return parent::sendResponse(['statusCode' => 200, 'message' => 'Product status updated successfully']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return parent::sendResponse(['statusCode' => 500, 'message' => 'An error occurred while processing your request.']);
        }
    }
    
    
    // public function actionRemove($id)
    // {
    //     $transaction = Yii::$app->iooxsBranch->beginTransaction();
        
    //     $product = Product::findOne($id);
    //     if (!$product) {
    //         return parent::sendResponse(['statusCode' => 404, 'message' => "Product with ID $id not found."]);
    //     }

    //     $productCount = Productstock::find()
    //         ->where(['idproduct' => $id])
    //         ->count();
        
    //     if($productCount > 0) {
    //         return parent::sendResponse([
    //             'statusCode' => 400,
    //             'message' => "No es posible eliminarla. Hay " . $productCount . " registros referente a compras y ventas asignados a este producto.",
    //         ]);
    //     }

    //     // Eliminar imágenes del producto en Cloudinary y la base de datos
    //     $productImages = Productimage::find()->where(['idproduct' => $id])->all();
    //     $errors = [];
    //     foreach ($productImages as $image) {
    //         $deleteResult = UploadFile::deleteFromCloudinary($image->imagepath);
    //         if (isset($deleteResult['error']) && $deleteResult['error']) {
    //             $errors[] = $deleteResult['message'];
    //         }
    //     }
        
    //     if (!empty($errors)) {
    //         $transaction->rollBack();
    //         return parent::sendResponse([
    //             'statusCode' => 500, 
    //             'message' => 'Failed to delete some images from Cloudinary: ' . implode(', ', $errors)
    //         ]);
    //     }
        
    //     Productimage::deleteAll(['idproduct' => $id]);
    //     ProductBranch::deleteAll(['id' => $id]);
    //     ProductStore::deleteAll(['id' => $id]);

    //     if (!$product->delete()) {
    //         $transaction->rollBack();
    //         return parent::sendResponse([
    //             'statusCode' => 500, 
    //             'message' => 'Failed to delete product',
    //             'errors' => $product->errors
    //         ]);
    //     }

    //     $transaction->commit();
    //     return parent::sendResponse(['statusCode' => 200, 'message' => 'Product deleted successfully']);
    // }
    
    public function actionSearchByName($name)
    {
        $query = Product::find()
            ->where(['ILIKE', 'name', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
