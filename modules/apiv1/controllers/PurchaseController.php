<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Vendor;
use app\models\Status;
use app\modules\apiv1\models\Product;
use app\models\Unit;
use app\models\Document;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController;
use app\modules\apiv1\models\dto\PurchaseDTO;
use app\modules\apiv1\models\Purchase;
use app\modules\apiv1\models\Productstock;
use app\modules\apiv1\models\ProductStore; // stock de los productos
use app\modules\apiv1\models\ProductBranch; // configuracion de los productos
use app\models\DocumentType; 
use app\modules\apiv1\models\Store;
use app\modules\apiv1\models\ViewProduct;

class PurchaseController extends BaseController {

    public $modelClass = 'app\modules\apiv1\models\Purchase';
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],        
                'insert' => ['POST'],     
                'cancelar' => ['DELETE'],   
                'product-by-purchase' => ['GET'],
                'by-id' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        // Obtener los parámetros filter, start, end, date
        $start = Yii::$app->request->get('start', null);
        $end = Yii::$app->request->get('end', null);
        $date = Yii::$app->request->get('date', null);
    
        $query = Purchase::find()
                     ->with('productStocks')
                     ->orderBy(['dateCreate' => SORT_DESC]);

    
        if ($date != null) { // en caso de que le enviemos date toma en cuenta eso como primera condicion
            try {
                $dateValue = new \DateTime($date);  // 'Y-m-d'
            $startDate = $dateValue->setTime(0, 0, 0); 
            $endDate = clone $startDate; 
            $endDate->setTime(23, 59, 59); 
            
            $query->andWhere(['>=', 'dateCreate', $startDate->format('Y-m-d H:i:s')]);
            $query->andWhere(['<=', 'dateCreate', $endDate->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => "Invalid date format. Please use yyyy-mm-dd.",
                ]);
            }
        } else { // caso contrario validamos el rango
            if ($start !== null) {
                try {
                    $startDate = new \DateTime($start);
                    $startDate->setTime(0, 0, 0); 
                    $query->andWhere(['>=', 'dateCreate', $startDate->format('Y-m-d H:i:s')]);
                } catch (\Exception $e) {
                    return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => "Invalid start date format. Please use yyyy-mm-dd.",
                    ]);
                }
            }
    
            if ($end !== null) {
                try {
                    $endDate = new \DateTime($end);
                    $endDate->setTime(23, 59, 59); 
                    $query->andWhere(['<=', 'dateCreate', $endDate->format('Y-m-d H:i:s')]);
                } catch (\Exception $e) {
                    return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => "Invalid end date format. Please use yyyy-mm-dd.",
                    ]);
                }
            }
        }
        
        $limit = ($start === null && $end === null && $date === null) ? 250 : null;
        $query->limit($limit);
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    
        $purchases = $dataProvider->getModels();
        return $purchases;
    }
    
    
    public function actionInsert() {

        $user = Yii::$app->user->identity;
        $transaction = Yii::$app->iooxsBranch->beginTransaction();

        $purchaseDTO = new PurchaseDTO();
        $purchaseDTO->load(Yii::$app->request->post(), '');
        
        if ($purchaseDTO->validate()) {         
           
            // Continuar con el proceso de registro de la venta
            $modelPurchase = new Purchase();
   
            $modelPurchase->attributes = $purchaseDTO->attributes;
            $modelPurchase->montoTotal = isset($purchaseDTO->subTotal) ? $purchaseDTO->subTotal : $purchaseDTO->total - $purchaseDTO->discountamount;
            $modelPurchase->subTotal = isset($purchaseDTO->montoTotal) ? $purchaseDTO->montoTotal : $purchaseDTO->total;
            $modelPurchase->idstatus = (new Status())->PROCESADO; // aqui traer el valor del lugar respectivo
           
            // $modelPurchase->discountpercentage = $purchaseDTO->discountpercentage;
            // $modelPurchase->discountamount = $purchaseDTO->discountamount;
            // $modelPurchase->numeroDocumento = $purchaseDTO->numeroDocumento;
            // $modelPurchase->nameVendor = $purchaseDTO->nameVendor;
            // $modelPurchase->idvendor = $purchaseDTO->idvendor;
            // $modelPurchase->comment = $purchaseDTO->comment;
            // $modelPurchase->numeroFactura = $purchaseDTO->numeroFactura;
            // $modelPurchase->idstore = $purchaseDTO->idstore;
            $modelPurchase->iduser = $user->iduser;

            if (!$modelPurchase->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save purchase',
                    'errors' => $modelPurchase->errors
                ]);
            }

            // Guardar el documento
            $modelDocument = new Document();
            $modelDocument->iddocumentType = DocumentType::$idTypePURCHASE; // Tipo de documento compra = 3
            $modelDocument->comment = 'Compra Nro ' . $modelPurchase->number . ' ';
            $modelDocument->iduser = $user->iduser;
            $modelDocument->idpurchase = $modelPurchase->id;
            $modelDocument->idstore = $purchaseDTO->idstore;

            if (!$modelDocument->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save Document',
                    'errors' => $modelDocument->errors
                ]);
            }            

            $modelPurchase->iddocument=$modelDocument->id;
            
            if (!$modelPurchase->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save purchase iddocument',
                    'errors' => $modelPurchase->errors
                ]);
            }

            $error = $this->saveDocument($modelDocument, $purchaseDTO->products, $transaction); // Actualizamos el stock 

            if ($error['success'] == false) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => $error['message'],
                    'errors' => $error['error']
                ]);
            }

            $transaction->commit();
            // Todo se ha guardado exitosamente
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Purchase created successfully',
                'data' => [
                    'idpurchase' => $modelPurchase->id
                ]
            ]);
        } else {
            // Si la validaci�n del formulario de venta falla, retornar errores
            return parent::sendResponse([
                'statusCode' => 500,
                'message' => 'Validation failed',
                'errors' => $purchaseDTO->errors
            ]);
        }
    }

    // las validaciones del array $products estan en purchaseDTO
    public function saveDocument($modelDocument, $products, $transaction) {

        if ($modelDocument->id != Null && isset($products)) {
            $productosWS = array();
            foreach ($products as $fila) {
                $modelProductstock = new Productstock();
                $modelProductstock->quantityinput = $fila['quantity'];
                $modelProductstock->cost = $fila['cost'];
                $modelProductstock->idproduct = $fila['id'];
                $modelProductstock->nprocess = 1;

                if ($modelProductstock->comment != null) {
                    $modelProductstock->comment = trim($modelProductstock->comment);
                    if ($modelProductstock->comment == '')
                        $modelProductstock->comment = null;
                }

                if ($modelProductstock->cost == null || $modelProductstock->cost == '')
                    $modelProductstock->cost = null;

                if ($modelProductstock->price == null || $modelProductstock->price == '')
                    $modelProductstock->price = null;

                $incAction = 1;
                if ($modelDocument->iddocumentType0->action == 1) {
                    $modelProductstock->quantityinput = $fila['quantity'];
                    $incAction = 1;
                }

                if ($modelDocument->iddocumentType0->action == -1) {
                    $modelProductstock->quantityoutput = $fila['quantity'];
                    $incAction = -1;
                }

                $modelProductstock->iddocument = $modelDocument->id;

                if ($modelDocument->idsale != null)
                    $modelProductstock->idsale = $modelDocument->idsale;

                if ($modelDocument->idpurchase != null)
                    $modelProductstock->idpurchase = $modelDocument->idpurchase;

                if ($modelDocument->idproductionOrder != null)
                    $modelProductstock->idproductionOrder = $modelDocument->idproductionOrder;

                $idstore = $modelDocument->idstore;

                /* VERIFICA STOCK DE PRODUCTO EN SUCURSAL */
                $modelProductBranch = ProductBranch::findOne(['id' => $modelProductstock->idproduct]);
                // en caso de que no exista crear una configuracion del producto
                if (empty($modelProductBranch)) { 
                    $modelProductBranch = new ProductBranch();
                    $modelProductBranch->id = $fila['id'];
                    $modelProductBranch->dateCreate = date('Y-m-d H:i:s');
                    $modelProductBranch->idstatus = 10;
                    $modelProductBranch->recycleBin = false;
                    $modelProductBranch->priceChange = false;
                    $modelProductBranch->controlInventory = true;
                    $modelProductBranch->enableSale = true;
                    $modelProductBranch->price = 0;
                    $modelProductBranch->cost = $fila['cost'];
                    $modelProductBranch->stockMin = 0;
                    $modelProductBranch->stockMax = 0;

                    if (!$modelProductBranch->save()) {
                        $transaction->rollBack();
                        return [
                            'error' => $modelProductBranch->getErrors(),
                            'success' => false,
                            'message' => 'Error al crear ProductBranch para el producto ID ' . $product['id'] . ': '
                        ];
                    }
                }
                
                $modelProductStore = ProductStore::findOne(['id' => $modelProductstock->idproduct, 'idstore' => $idstore]);
                // en caso de que no exista crear el registro productStore
                if (empty($modelProductStore)) {
                    $modelProductStore = new ProductStore();
                    $modelProductStore->id = $fila['id'];
                    $modelProductStore->idstore = $idstore;
                    $modelProductStore->dateCreate = date('Y-m-d H:i:s');
                    $modelProductStore->recycleBin = false;
                    $modelProductStore->allow = true;
                    $modelProductStore->stock = 0;
                    $modelProductStore->stockReserved = 0;

                    if (!$modelProductStore->save()) {
                        $transaction->rollBack();
                        return [
                            'error' => $modelProductStore->getErrors(),
                            'success' => false,
                            'message' => 'Error al crear ProductStore para el producto ID ' . $product['id'] . ' en el almacen con ID ' . $idstore . ': '
                        ];
                    }
                }

                $previousStock = $modelProductStore->stock;
                $modelProductStore->stock += $incAction * $fila['quantity'];
                //$prod=$modelProductBranch->product->typeService;
                if ($incAction == -1 && $modelProductBranch->controlInventory == true && $modelProductStore->stock < 0) {
                    $missingQuantity = $previousStock < 0 ? $fila['quantity'] : $modelProductStore->stock * -1;
                    $message .= "Producto [" . $fila['name'] . "], la cantidad faltante es [$missingQuantity]";
                    $message .= "Almacen [" . $modelProductStore->idstore0->name . "]";
                    $transaction->rollBack();
                    return [
                        'error' => $modelProductStore->getErrors(),
                        'success' => false,
                        'message' => $message
                    ];
                    break;
                }
               
                /* FIN VERIFICA */
                if ($modelProductstock->montoDescuento == null)
                    $modelProductstock->montoDescuento = 0;
                
                if ($modelProductstock->save()) {
                    if (!$modelProductStore->update()) {
                        $transaction->rollBack();
                        return [
                            'error' => $modelProductStore->getErrors(),
                            'success' => false,
                            'message' => "Problema: Al actualizar el stock de producto[" . $fila['name'] . "]"
                        ];
                    } 
                } else {
                    $transaction->rollBack();
                    return [
                        'error' => $modelProductStore->getErrors(),
                        'success' => false,
                        'message' => "Problema: Al registrar Movimiento de Producto[" . $fila['name'] . "]"
                    ];
                }
                
            }

            return [
                'success' => true,
                'message' => "Datos cambiado correctamente"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Error al registrar el documento Nro $modelDocument->number."
            ];
        }
    }

    // actualizar el stock de un producto
    protected function updateStock($typeDocument, $product, $idstore)
    {
        $productBranch = ProductBranch::findOne($product['id']);
        // en caso de que no exista crear una configuracion del producto
        if (!$productBranch) { 
            $productBranch = new ProductBranch();
            $productBranch->id = $product['id'];
            $productBranch->dateCreate = date('Y-m-d H:i:s');
            $productBranch->idstatus = 10;
            $productBranch->recycleBin = false;
            $productBranch->priceChange = false;
            $productBranch->controlInventory = true;
            $productBranch->enableSale = true;
            $productBranch->price = 0;
            $productBranch->cost = $product['cost'];
            $productBranch->stockMin = 0;
            $productBranch->stockMax = 0;

            if (!$productBranch->save()) {
                return 'Error al crear ProductBranch para el producto ID ' . $product['id'] . ': ' . json_encode($productBranch->errors);
            }
        }
       
        $productStore = ProductStore::findOne(['id' => $product['id'], 'idstore' => $idstore]);
        // en caso de que no exista crear el registro productStore
        if (!$productStores) {
            $productStore = new ProductStore();
            $productStore->id = $product['id'];
            $productStore->idstore = $idstore;
            $productStore->dateCreate = date('Y-m-d H:i:s');
            $productStore->recycleBin = false;
            $productStore->allow = true;
            $productStore->stock = 0;
            $productStore->stockReserved = 0;

            if (!$productStore->save()) {
                return 'Error al crear ProductStore para el producto ID ' . $product['id'] . ' en la tienda ID ' . $idstore . ': ' . json_encode($productStore->errors);
            }
        }

         // actualizamos ese producto en ese almacen
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
    }    

    public function actionProductsByPurchase($idpurchase) {
        $purchase = $this->modelClass::find()->where(['id' => $idpurchase])->with('productStocks')->one();

        if (!$purchase) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Purchase with ID $idpurchase not found.",
            ]);
        }

        $productIds = [];
        foreach ($purchase->productStocks as $productStock) {
            $productIds[] = $productStock->idproduct;
        }

        $query = ViewProduct::find()->where(['id' => $productIds])->orderBy(['id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    public function actionById($id) {
        $purchase = $this->modelClass::find()->where(['id' => $id])->with('productStocks')->one();

        if (!$purchase) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Purchase with ID $id not found.",
            ]);
        }
    
        return $purchase;
    } 

    public function actionCancelar($idpurchase) {
        $sale = $this->modelClass::find()->where(['id' => $idpurchase])->with('productStocks')->one();
        
        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Purchase with ID $idpurchase not found.",
            ]);
        }
        $sale->idstatus = 80;
        $documentType = DocumentType::findOne(['type' => 'VENTA ANULADA']); // Obtener el tipo de salida
        
        foreach ($sale->productStocks as $productStock) {
            $errors = $this->updateStock($documentType, 
            [
                'id' => $productStock->idproduct,
                'quantity' => $productStock->quantityinput,
                'price' => $productStock->price,
                // 'idunit' => $productStock->idunit,
                // 'name' => $productStock->name,
                'idstore' =>  $productStock->idstore,
                // 'codigoProducto' => $productStock->codigoProducto
            ]); // Actualizamos el stock
            
            if ($errors != null) {
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to update stock',
                    'errors' => $errors
                ]);
            }
        }         

        if($sale->save()) {
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Invoice cancel successfully',
            ]);
        }

        return $sale;
    }
}
