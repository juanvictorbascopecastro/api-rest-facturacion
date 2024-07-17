<?php
namespace app\modules\service\controllers;

use Yii;
use app\modules\service\helpers\DbConnection;
use app\models\Customer;
use app\models\Product;
use app\models\Unit;
use app\models\Document;
use yii\data\ActiveDataProvider;
use app\modules\service\controllers\BaseController;
use yii\web\NotFoundHttpException;
use app\modules\service\models\SaleForm;
use app\modules\service\models\Sale;
use app\models\Invoice;
use app\modules\service\models\Productstock;

use app\modules\service\models\ProductStore;
use app\modules\service\models\ProductBranch;
use app\models\DocumentType;
use app\modules\service\models\Store;

class SaleController extends BaseController
{
    public $modelClass = 'app\modules\service\models\Sale';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];
        return $actions;
    }

    public function actionListar()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Sale::find()
                        ->with('productStocks') 
                        ->orderBy(['dateCreate' => SORT_DESC])
                        ->limit(250),
            'pagination' => false,
        ]);
    
        $sales = $dataProvider->getModels();

        return $sales;
    }

    public function actionInsert()
    {
        $saleForm = new SaleForm();
        $saleForm->load(Yii::$app->request->post(), '');

        $user = Yii::$app->user->identity;
        if ($saleForm->validate()) {
            // Se verifica y se registra el producto
            $products = $this->saveProducts($saleForm->products, $user);
            if (isset($products['statusCode']) && $products['statusCode'] == 500) {
                return parent::sendResponse($products);
            }

            if (!$saleForm->idcustomer || $saleForm->idcustomer == null) {
                $customer = new Customer();
                $customer->razonSocial = $saleForm->razonSocial;
                $customer->numeroDocumento = $saleForm->numeroDocumento;
                $customer->iddocumentNumberType = $saleForm->idtypeDocument;
                $customer->phone = $saleForm->phone;
                $customer->iduser = $user->iduser;

                if ($customer->save()) {
                    $saleForm->idcustomer = $customer->id;
                } else {
                    return parent::sendResponse([
                        'statusCode' => 500,
                        'message' => 'Failed to save customer',
                        'errors' => $customer->errors
                    ]);
                }
            } else if (!empty($saleForm->idcustomer)) {
                $existingCustomer = Customer::findOne($saleForm->idcustomer);
                if (!$existingCustomer) {
                    return parent::sendResponse([
                        'statusCode' => 404,
                        'message' => 'Customer with id ' . $saleForm->idcustomer . ' does not exist.',
                    ]);
                }
            }

            // Continuar con el proceso de registro de la venta
            $sale = new Sale();
            $sale->attributes = $saleForm->attributes;
            $sale->montoTotal = $saleForm->total;
            $sale->discountamount = $saleForm->discountamount;
            $sale->subTotal = isset($saleForm->subTotal) ? $saleForm->subTotal : $saleForm->total - $saleForm->discountamount;
            $sale->montoRecibido = isset($saleForm->montoRecibido) ? $saleForm->montoRecibido : $saleForm->total;
            $sale->numeroDocumento = $saleForm->numeroDocumento;
            $sale->iddocument = $saleForm->idtypeDocument;
            $sale->razonSocial = $saleForm->razonSocial;
            $sale->phone = $saleForm->phone;
            $sale->iduser = $user->iduser;
            $sale->codigoMetodoPago = $saleForm->codigoMetodoPago;
            $sale->codigoDocumentoSector = 1; // siat factura compra y venta

            if (!$sale->save()) {
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save sale',
                    'errors' => $sale->errors
                ]);
            }

            $documentType = DocumentType::findOne(['type' => 'VENTA']); // Obtener el tipo de salida
            // Guardar los documentos y productos relacionados con la venta
            $productsResult = [];
            if (isset($products) && is_array($products)) {
                foreach ($products as $productData) {
                    // Guardar el documento
                    $document = new Document();
                    $document->attributes = $productData;
                    $document->idcliente = $saleForm->idcustomer;
                    $document->iddocumentType = $documentType->id; // Tipo de documento venta = 3
                    $document->number = $productData['quantity'];
                    $document->iduser = $user->iduser;
                    $document->idsale = $sale->id;

                    if (!$document->save()) {
                        return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to save Document',
                            'errors' => $document->errors
                        ]);
                    }

                    $errors = $this->updateStock($documentType, $productData); // Actualizamos el stock
                    if ($errors != null) {
                        return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to update stock',
                            'errors' => $errors
                        ]);
                    }

                    // Guardar el producto
                    $product = new Productstock();
                    $product->attributes = $productData;
                    $product->nprocess = 1;
                    $product->quantityoutput = $productData['quantity'];
                    $product->price = $productData['quantity'] * $productData['price'];
                    $product->iduser = $user->iduser;
                    $product->idproduct = $productData['id'];
                    $product->iddocument = $document->id;  // Aquí se agrega el id del document registrado
                    $product->idsale = $sale->id;

                    if (!$product->validate()) {
                        return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Validation failed for Product',
                            'errors' => $product->errors
                        ]);
                    }
                    $productsResult[] = $product;
                }

                // Guardar cada producto de la venta
                foreach ($productsResult as $product) {
                    if (!$product->save()) {
                        return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to save Product',
                            'errors' => $product->errors
                        ]);
                    }
                }
            }

            // Todo se ha guardado exitosamente
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Invoice created successfully',
            ]);
        } else {
            // Si la validación del formulario de venta falla, retornar errores
            return parent::sendResponse([
                'statusCode' => 500,
                'message' => 'Validation failed',
                'errors' => $saleForm->errors
            ]);
        }
    }
    
    public function actionProductsBySale($idsale)
    {
        $sale = $this->modelClass::find()->where(['id' => $idsale])->with('productStocks')->one();

        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Sale with ID $idsale not found.",
            ]);
        }

        $productIds = [];
        foreach ($sale->productStocks as $productStock) {
            $productIds[] = $productStock->idproduct;
        }

        $query = Product::find()->where(['id' => $productIds])->orderBy(['id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    // metodo para verificar que no se haya enviado el id, entonces determina que es nuevo y debe registrarse
    protected function saveProducts($productsData, $user)
    {
        $products = [];
        
        foreach ($productsData as $productData) {
            // Verificar si 'id' está presente, es diferente de null y es numérico
            if (isset($productData['id']) && $productData['id'] != null) {
                $products[] = $productData;
                continue; // Si cumple todas las condiciones, no guardar el producto y pasar al siguiente
            }
    
            $newProduct = new Product();
            $newProduct->name = $productData['name'];
            $newProduct->price = $productData['price'];
            $newProduct->idunit = $productData['idunit'] ?? null;
            $newProduct->idstatus = 1;
            $newProduct->iduser = $user->iduser;
         
            if ($newProduct->idunit == null) {
                $unit = Unit::find()->where(['is not', 'order', null])->orderBy(['order' => SORT_ASC])->one();
                if ($unit !== null) {
                    $newProduct->idunit = $unit->id;
                }
            }

            if (isset($productData['codigoProducto']) && $productData['codigoProducto'] != null) {
                $listaProductoServicio = SincronizarListaProductosServicios::find()->where(['codigoProducto' => $productData['codigoProducto']])->one();
                $newProduct->idsincronizarListaProductosServicios = $listaProductoServicio->id;
            } else {
                $listaProductoServicio = SincronizarListaProductosServicios::find()
                    ->where(['is not', 'order', null])
                    ->orderBy(['order' => SORT_ASC])
                    ->one();
                    
                if ($listaProductoServicio != null) {
                    $newProduct->idsincronizarListaProductosServicios = $listaProductoServicio->id;
                }
            }

            if (!$newProduct->validate()) {
                
                return [
                    'statusCode' => 500,
                    'message' => 'Validation failed for Product',
                    'errors' => $newProduct->errors
                ];
            }
      
            if (!$newProduct->save()) {
                return [
                    'statusCode' => 500,
                    'message' => 'Failed to save Product',
                    'errors' => $newProduct->errors
                ];
            }
            // Añadir el id del producto registrado a productData
            $productData['id'] = $newProduct->id;
    
            $productBranch = new ProductBranch();
            $productBranch->id = $newProduct->id;
            $productBranch->iduser = $newProduct->iduser;
            $productBranch->idstatus = 10;
            $productBranch->priceChange = false;
            $productBranch->price = $newProduct->price;
            $productBranch->cost = 0;
            $productBranch->controlInventory = false;
            $productBranch->enableSale = true;
            $productBranch->stockMin = 0;
            $productBranch->stockMax = 0;
    
            // Validar y guardar productBranch
            if (!$productBranch->validate()) {
                return [
                    'statusCode' => 500,
                    'message' => 'Validation failed for ProductBranch',
                    'errors' => $productBranch->errors
                ];
            }
    
            if (!$productBranch->save()) {
                return [
                    'statusCode' => 500,
                    'message' => 'Failed to save ProductBranch',
                    'errors' => $productBranch->errors
                ];
            }
    
            $cfgStores = Store::find()->all(); // Obtener todos los registros de CfgStore
            foreach($cfgStores as $store){
                $productStore = new ProductStore();
                $productStore->id = $newProduct->id;
                $productStore->iduser = $newProduct->iduser;
                $productStore->stock = 0;
                $productStore->idstore = $store->id;
                $productStore->stockReserved = 0;
                $productStore->allow = true;
    
                // Validar y guardar productStore
                if (!$productStore->validate()) {
                    return [
                        'statusCode' => 500,
                        'message' => 'Validation failed for productStore for store ID ' . $store->id,
                        'errors' => $productStore->errors
                    ];
                }
    
                if (!$productStore->save()) {
                    return [
                        'statusCode' => 500,
                        'message' => 'Failed to save productStore for store ID ' . $store->id,
                        'errors' => $productStore->errors
                    ];
                }
            }
            $products[] = $productData; 
        }
    
        return $products;
    }
    // actualizar el stock de un producto
    protected function updateStock($typeDocument, $product)
    {
        $productBranch = ProductBranch::findOne($product['id']);   
        if ($productBranch && $productBranch->controlInventory) {
            $productStores = ProductStore::findAll(['id' => $product['id']]);
    
            if (!$productStores) {
                return 'No se encontró el registro del producto en ProductStore para el producto ID ' . $product['id'];
            }
    
            // Verificar si se proporciona un idStore
            if (isset($product['idStore']) && !empty($product['idStore'])) {
                $productStore = ProductStore::findOne(['id' => $product['id'], 'idstore' => $product['idStore']]);
                if ($productStore) {
                    if ($typeDocument->action == 1) {
                        $productStore->stock = floatval($productStore->stock) + $product['quantity'];
                    } else if ($typeDocument->action == -1) {
                        $productStore->stock = floatval($productStore->stock) - $product['quantity'];
                    }
                    if ($productStore->save()) {
                        return null;
                    } else {
                        return 'Error al actualizar el stock para el producto ID ' . $product['id'] . ' en la tienda ID ' . $product['idStore'] . ': ' . json_encode($productStore->errors);
                    }
                } else {
                    return 'No se encontró el registro del producto en la tienda ID ' . $product['idStore'];
                }
            } else { // Si no se proporciona idStore, decrementar la cantidad total entre los registros disponibles
                $remainingQuantity = $product['quantity'];
                foreach ($productStores as $productStore) {
                    if ($remainingQuantity <= 0) {
                        break; // Salir del bucle si ya se ha cubierto toda la cantidad
                    }
                    // Actualizar el stock segun la acción del tipo de documento
                    if ($typeDocument->action == 1) {
                        $productStore->stock = floatval($productStore->stock) + $remainingQuantity;
                        $remainingQuantity = 0; // Toda la cantidad ha sido añadida
                    } else if ($typeDocument->action == -1) {
                        if ($productStore->stock >= $remainingQuantity) {
                            $productStore->stock = floatval($productStore->stock) - $remainingQuantity;
                            $remainingQuantity = 0; // Toda la cantidad ha sido restada
                        } else {
                            $remainingQuantity -= $productStore->stock; // Restar solo lo disponible en este registro
                            $productStore->stock = 0;
                        }
                    }

                    if (!$productStore->save()) {
                        return 'Error al actualizar el stock para el producto ID ' . $product['id'] . ' en la tienda ID ' . $productStore->idstore . ': ' . json_encode($productStore->errors);
                    }
                }
                return null;
            }
        } else {
            return null;
        }
    }    

    public function actionInvoiceFile ($idsale) {
        $sale = $this->modelClass::find()->where(['id' => $idsale])->with('productStocks')->one();

        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Sale with ID $idsale not found.",
            ]);
        }
        
        $filePath = Yii::getAlias('@webroot/files/invoice.pdf');
        return Yii::$app->response->sendFile($filePath, 'invoice.pdf', [
            'mimeType' => 'application/pdf',
            'inline' => true // Cambia a false para forzar la descarga en lugar de mostrar en el navegador
        ]);
    }
}