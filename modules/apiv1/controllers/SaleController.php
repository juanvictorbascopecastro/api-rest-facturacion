<?php

namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Customer;
use app\models\Product;
use app\models\Unit;
use app\models\Document;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController;
use yii\web\NotFoundHttpException;
use app\modules\apiv1\models\dto\SaleDTO;
use app\modules\apiv1\models\Sale;
use app\modules\apiv1\models\Productstock;
use app\modules\apiv1\models\ProductStore;
use app\modules\apiv1\models\ProductBranch;
use app\models\DocumentType; 
use app\modules\apiv1\models\Store;

use app\models\SystemPoint;
use app\models\UserSystemPoint;
use app\models\SiatModalidad;
use app\models\Invoice;

use app\modules\apiv1\models\SincronizarListaProductosServicios;

use app\modules\apiv1\models\SiatTipoDocumentoIdentidad;
use app\modules\apiv1\helpers\ValidateNit;
use app\modules\apiv1\models\ViewProduct;

use app\models\IoSystemBranchUser;
use app\models\IoSystemBranch;
use app\models\SiatBranch;
use app\models\CashDocument;
use app\models\CurrentAccountCustomer;

// implementado en anular
use app\models\ReceiptType;
use app\modules\ioLib\helpers\WsdlSiat;

class SaleController extends BaseController {

    public $modelClass = 'app\modules\apiv1\models\Sale';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],        
                'insert' => ['POST'],     
                'cancelar' => ['DELETE'],   
                'product-by-sale' => ['GET'],
                'by-id' => ['GET'],
                'id-store' => ['GET'],
                'validate-nit' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        $filter = Yii::$app->request->get('filter', 0);
        $start = Yii::$app->request->get('start', null);
        $end = Yii::$app->request->get('end', null);
        $date = Yii::$app->request->get('date', null);
    
        $query = Sale::find()
                     ->with('productStocks')
                     ->orderBy(['dateCreate' => SORT_DESC]);
    
        switch ($filter) {
            case 0:
                $query->andWhere(['invoice' => true]);
                break;
            case 1:
                $query->andWhere(['invoice' => false]);
                break;
            default:
                // $query->andWhere(['invoice' => true]);
                break;
        }
    
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
    
        $sales = $dataProvider->getModels();
        return $sales;
    }

    public function actionInsert() {

        $user = Yii::$app->user->identity;
       
        // $q=new Wsdlsiat();
        // echo $q->runOk();
        // echo Yii::$app->user->getId();
        $modelUserSystemPoint = new UserSystemPoint();
        $modelUser = $modelUserSystemPoint->getModel();

        // $modelCashOpen = Cash::model()->find("idstatus=" . Cash::model()->statusABIERTO . ' and iduser=' . Yii::app()->user->getId());
        // if ($modelCashOpen == null) {
        //     echo System::conditionOpen(false, 'Debe realizar una  �APERTURA DE CAJA VENTA�  previamente ');
        //     return;
        // }       

        $transaction = Yii::$app->iooxsBranch->beginTransaction();

        $saleDTO = new SaleDTO();
        $saleDTO->load(Yii::$app->request->post(), ''); 
        if (empty($saleDTO->getCashOpen())) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => 'Debe realizar una "APERTURA DE CAJA VENTA" previamente.',
            ]);
        } 
           

        if ($saleDTO->validate()) { 
            $codigoExcepcion = 1;
            // AQUI VALIDAMOS QUE SEA UN NIT
            if(!$saleDTO->validateCodigoExcepcion && $saleDTO->invoice) { // en caso que sea falso quiere decir que ese nit no se ha validado
                $siatTipoDoc = SiatTipoDocumentoIdentidad::findOne(['id' => $saleDTO->codigoTipoDocumentoIdentidad]); // consultar si debe validad ese nit
                if($siatTipoDoc->codigoClasificador == 5) { // Es un documento que debe ser validado en impuesto
                    $isValidNit = ValidateNit::isValid($saleDTO->numeroDocumento);
                    if ($isValidNit['codigoExcepcion'] === -1) {
                        // return parent::sendResponse([
                        //     'statusCode' => 500,
                        //     'message' => "Este usuario no esta habilitado para emitir facturas con SIAT!",
                        // ]);
                        // RETORNA -1
                        $codigoExcepcion = 1;
                    } elseif ($isValidNit['codigoExcepcion'] === false) {
                        // RETORNA false
                        $codigoExcepcion = 1;
                        return parent::sendResponse([
                            'statusCode' => 422,
                            'message' => "Para la Factura NIT(" . $saleDTO->numeroDocumento . ") inválido.",
                        ]);
                    } else {
                        // RETORNA true
                        $codigoExcepcion = 0;
                    }
                } else {
                    $codigoExcepcion = 0;
                }
            }
            // cuando envia 1 se autoriza, cuando envia 0 no se autoriza
            // validar cliente
            if (!$saleDTO->idcustomer || $saleDTO->idcustomer == null) { // en caso de que no se envie el id guardar
                $customer = new Customer();
                $customer->razonSocial = $saleDTO->razonSocial;
                $customer->name = $saleDTO->razonSocial;
                $customer->numeroDocumento = $saleDTO->numeroDocumento;
                $customer->codigoTipoDocumentoIdentidad = $saleDTO->codigoTipoDocumentoIdentidad;
                $customer->phone = $saleDTO->phone;
                
                if ($customer->save()) {
                    $saleDTO->idcustomer = $customer->id;
                } else {
                    return parent::sendResponse([
                        'statusCode' => 500,
                        'message' => 'Failed to save customer',
                        'errors' => $customer->errors
                    ]);
                }
            } else if (!empty($saleDTO->idcustomer)) {
                $customer = Customer::findOne($saleDTO->idcustomer);
                if (!$customer) {
                    return parent::sendResponse([
                        'statusCode' => 404,
                        'message' => 'Customer with id ' . $saleDTO->idcustomer . ' does not exist.',
                    ]);
                }
                // en caso de que se envie el id, actualizamos lo del usuario
                if(isset($saleDTO->razonSocial)) $customer->razonSocial = $saleDTO->razonSocial;
                if(isset($saleDTO->razonSocial)) $customer->name = $saleDTO->razonSocial;
                if(isset($saleDTO->numeroDocumento)) $customer->numeroDocumento = $saleDTO->numeroDocumento;
                if(isset($saleDTO->codigoTipoDocumentoIdentidad)) $customer->codigoTipoDocumentoIdentidad = $saleDTO->codigoTipoDocumentoIdentidad;
                if(isset($saleDTO->phone)) $customer->phone = $saleDTO->phone;
                $customer->save();
            }
            // recorre los productos y debe registrar en caso de que no este registrado un producto
            $products = $this->saveProducts($saleDTO->products, $user);
            if (isset($products['statusCode']) && $products['statusCode'] == 500) {
                return parent::sendResponse($products);
            }
            // Continuar con el proceso de registro de la venta
            $modelSale = new Sale();
   
            $modelSale->attributes = $saleDTO->attributes;
            $modelSale->montoTotal = $saleDTO->total;
            $modelSale->discountamount = $saleDTO->discountamount;
            $modelSale->subTotal = isset($saleDTO->subTotal) ? $saleDTO->subTotal : $saleDTO->total - $saleDTO->discountamount;
            $modelSale->montoRecibido = isset($saleDTO->montoRecibido) ? $saleDTO->montoRecibido : $saleDTO->total;
            $modelSale->numeroDocumento = $saleDTO->numeroDocumento;
            $modelSale->codigoTipoDocumentoIdentidad = $saleDTO->codigoTipoDocumentoIdentidad;
            $modelSale->razonSocial = $saleDTO->razonSocial;
            $modelSale->phone = $saleDTO->phone;
            $modelSale->codigoMetodoPago = $saleDTO->codigoMetodoPago;
            $modelSale->codigoDocumentoSector = 1; // siat factura compra y venta
            $modelSale->idstatus = $modelSale->statusPROCESADO;
            $modelSale->number = $saleDTO->number;
            
            //complete data for invoice
            $modelSystemPoint = SystemPoint::getModelCurrent();
            $modelSale->codigoModalidad = $modelSystemPoint->idsiatBranch0->codigoModalidad;
            $modelSale->idsystemPoint = $modelUser->idsystemPoint;

            if (!$modelSale->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save sale',
                    'errors' => $modelSale->errors
                ]);
            }

            // Guardar el documento
            $modelDocument = new Document();
            $modelDocument->iddocumentType = DocumentType::$idTypeSALE; // Tipo de documento venta = 3
            $modelDocument->comment = 'VENTA Nro ' . $modelSale->number . ' ';
            $modelDocument->iduser = $user->iduser;
            $modelDocument->idsale = $modelSale->id;
            
             // registro de cuenta de cliente
            if ($modelSale->idtypeCharge == 2) {
                $model_cac = new CurrentAccountCustomer();
                $model_cac->idcustomer = $modelSale->idcustomer;
                $model_cac->debit = $modelSale->montoTotal;
                $model_cac->idsale = $modelSale->id;
                $model_cac->comment = 'NRO VENTA:  ' . $modelSale->number;
                $model_cac->save();
            }
            // buscar un idstore, codigo temporal            
            $modelDocument->idstore = $this->getIdStore($products);

            if (!$modelDocument->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save Document',
                    'errors' => $modelDocument->errors
                ]);
            }            

            $modelSale->iddocument = $modelDocument->id;
            $modelCashDocument = new CashDocument();
            $resultCachDoc = $modelCashDocument->saveCash($saleDTO->cashOpen, $modelSale);
            if ($resultCachDoc['error']) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => $resultCachDoc['message'],
                ]);
            }
            $modelSale->idcashDocument = $modelCashDocument->id;
            
            if (!$modelSale->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save sale iddocument',
                    'errors' => $modelSale->errors
                ]);
            }
            // Actualizamos el stock 
            $errors = $this->saveDocument($modelDocument, $products, $saleDTO->ioSystemBranch); 

            if (!$errors['success']) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => $errors['message'],
                    'errors' => $errors['errors']
                ]);
            }

            // Guardar los documentos y productos relacionados con la venta
            $productsResult = [];
            if ($modelSale->invoice == 1 && ($modelSale->codigoModalidad == SiatModalidad::$codigoModalidadCOMPUTARIZADA || $modelSale->codigoModalidad == SiatModalidad::$codigoModalidadELECTRONICA)) {

                // $model->cafc = $cafc;
                $modelSale->codigoExcepcion = $codigoExcepcion;
                $modelInvoice = new Invoice();

                $resultINVOICE = $modelInvoice->record($modelSale); 

                if ($resultINVOICE['success'] == false || ($modelInvoice->codigoEmision == 1 && !( $modelInvoice->transaccion == true))) {
                    // $success = false;
                    // //$modelInvoice->sentToFile();
                    // //$transaction->rollback();
                    // echo System::hasErrors('VENTA NO REGISTRADA, Intente nuenamente. ' . $resultINVOICE['message']);
                    // return;
                    $transaction->rollBack();
                    return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => 'VENTA NO REGISTRADA. ' . $resultINVOICE['message'],
                        // 'errors' => $resultINVOICE['message']
                    ]);
                } 
            }

            $transaction->commit();
            // Todo se ha guardado exitosamente
            return parent::sendResponse([
                'statusCode' => 201,
                'message' => 'Invoice created successfully',
                'data' => [
                    'idsale' => $modelSale->id
                ]
            ]);
        } else {
            // Si la validacion del formulario de venta falla, retornar errores
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => '¡Información no válida!',
                'errors' => $saleDTO->errors
            ]);
        }
    }
    
    // las validaciones del array $products estan en SaleDTO
    private function saveDocument($modelDocument, $products, $ioSystemBranch) {
        $xml = '';
        $iddocument = $modelDocument->id;
        $success = true;
        $message = '';

        if ($iddocument != Null && isset($products)) {
            $productosWS = array();
            foreach ($products as $fila) {
                $modelProductstock = new Productstock();
                $modelProductstock->nprocess = 1;
                $modelProductstock->quantityoutput = $fila['quantity'];
                $modelProductstock->price = $fila['price'];

                $modelProductstock->idproduct = $fila['id'];

                // if ($fila['quantity'] * 1 == 0) {
                //     $success = false;
                //     $message .= "La cantidad Producto [" . $fila['name'] . "], debe ser mayor a 0";
                //     break;
                // }

                if ($modelProductstock->comment != null) {
                    $modelProductstock->comment = trim($modelProductstock->comment);
                    if ($modelProductstock->comment == '')
                        $modelProductstock->comment = null;
                }

                if ($modelProductstock->cost == null || $modelProductstock->cost == '') {
                    $modelProductstock->cost = null;
                }

                if ($modelProductstock->price == null || $modelProductstock->price == '') {
                    $modelProductstock->price = null;
                }

                $inc = 1;
                if ($modelDocument->iddocumentType0->action == 1) {
                    $modelProductstock->quantityinput = $fila['quantity'];
                    $inc = 1;
                }

                if ($modelDocument->iddocumentType0->action == -1) {
                    $modelProductstock->quantityoutput = $fila['quantity'];
                    $inc = -1;
                }

                $modelProductstock->iddocument = $iddocument;

                if ($modelDocument->idsale != null) {
                    $modelProductstock->idsale = $modelDocument->idsale;
                }

                if ($modelDocument->idpurchase != null) {
                    $modelProductstock->idpurchase = $modelDocument->idpurchase;
                }

                if ($modelDocument->idproductionOrder != null) {
                    $modelProductstock->idproductionOrder = $modelDocument->idproductionOrder;
                }

                $idstore = 1;
                if(empty($fila['idstore'])) { // en caso de que no haya almacen 
                    // obtenemos el primer registro de almacen
                    // $store = Store::find()->orderBy(['id' => SORT_ASC])->one();
                    // $idstore = $store->id; // asignamos el id del almacen
                    $user = Yii::$app->user->identity;
                    // Verificar si ese usuario esta restringido para vender de un almacen en espesifico
                    // en caso de que el idstoreMain = null este usuario no tiene restricciones
                    $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);
                    // print_r($modelUserSystemPoint);
                    if($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreMain)) {
                        $idstore = $modelUserSystemPoint->idstoreMain;
                    } else {
                        $store = Store::find()->orderBy(['id' => SORT_ASC])->one();
                        $idstore = $store->id; // asignamos el id del almacen
                    }
                } else {
                    $idstore = $fila['idstore'];
                }
               
                $modelProductstock->idstore = $idstore;
               
                // echo "[$modelDocument->idstore]";
                // if ($modelDocument->idstore != null) {
                //     $idstore = $modelProductstock->idstore = $modelDocument->idstore;
                // }

                // echo "[idstore=$idstore]";
                /* VERIFICA STOCK DE PRODUCTO EN SUCURSAL */
                $modelProductStore = ProductStore::findOne(['id' => $modelProductstock->idproduct, 'idstore' => $idstore]);
                $modelProductBranch = ProductBranch::findOne(['id' => $modelProductstock->idproduct]);

                if ($modelProductStore == null) { // verificamos si ese producto no esta registrado en ese almacen
                    $modelProductStore = new ProductStore();
                    $modelProductStore->id = $modelProductstock->idproduct;

                    $modelProductStore->stock = 0;
                    $modelProductStore->idstore = $idstore;
                    if (!$modelProductStore->save()) {
                        $success = false;
                        $message .= "problema: Al INICIAR EL STOCK en la Sucursal del Producto[" . $fila['name'] . "]" . $idstore;
                        break;
                    }
                }

                $previousStock = $modelProductStore->stock;
                $modelProductStore->stock += $inc * $fila['quantity'];
                // $prod=$modelProductBranch->product->typeService;
                $dd = $modelProductBranch->controlInventory;

                if ($inc == -1 && $modelProductBranch != null && $dd == true && $modelProductStore->stock < 0) {
                    $missingQuantity = $previousStock < 0 ? $fila['quantity'] : $modelProductStore->stock * -1;
                    $success = false;
                    $message .= 'Producto "' . $fila['name'] . '", la cantidad faltante es "' . $missingQuantity . '"';
                    // $message .= "Almacen [" . $modelProductStore->idstore0->name . "]";
                    break;
                }
                // actualizar los productos por lotes
                if($ioSystemBranch->allowLot && !empty($fila['idproductstock'])) { // si se envia un lote
                    $whatsLot = Productstock::findOne(['id' => $fila['idproductstock']]);
                    if(!empty($whatsLot)) { // si existe ese lote
                        $whatsLot->quantityoutput = $whatsLot->quantityoutput + $fila['quantity'];
                        if(!$whatsLot->save()) {
                            $success = false;
                            $message .= "No fue posible actualizar el lote del producto " . $fila['name'];
                            break;
                        }
                    }
                }
                
                /* FIN VERIFICA */
                if ($modelProductstock->montoDescuento == null)
                    $modelProductstock->montoDescuento = 0;
                
                if ($modelProductstock->save()) {
                    if (!$modelProductStore->update()) {
                        $success = false;
                        $message .= "Problema: Al actualizar el stock de producto[" . $fila['name'] . "]";
                        break;
                    } 
                } else {
                    $success = false;
                    $message .= "Problema: Al registrar Movimiento de Producto[" . $fila['name'] . "]";
                    // print_r($modelProductstock->getErrors());
                    break;
                }
                
            }

            return array('success' => $success, 'message' => $message, 'xmlDetailProducts' => $xml);
        } else {
            return array('success' => false, 'message' => $message);
            throw new CrugeException("Error al registrar el documento Nro $modelDocument->number.", 483);
        }
    }

    public function actionProductsBySale($idsale) {
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

        $query = ViewProduct::find()->where(['id' => $productIds])->orderBy(['id' => SORT_ASC]);

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
            $newProduct->idstatus = 10;
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
            $productBranch->cost = 0; // costo de compra
            $productBranch->controlInventory = false; // si es un producto donde se sigue el control de inventario
            $productBranch->enableSale = true; // permite editar el precio en la venta
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

    public function actionCancelar($idsale) {
        // return parent::sendResponse([
        //     'statusCode' => 404,
        //     'message' => "No es posible anular desde la app móvil; por favor, hazlo desde el sistema web.",
        // ]);
        $transaction = Yii::$app->iooxsBranch->beginTransaction();
        try {
            // Obtener la venta
            $modelSale = $this->modelClass::find()->where(['id' => $idsale])->with('productStocks')->one();
            if (!$modelSale) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 404,
                    'message' => "Sale with ID $idsale not found.",
                ]);
            }
    
            // Verificar apertura de caja
            $saleDTO = new SaleDTO();
            if (empty($saleDTO->getCashOpen())) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'Debe realizar una "APERTURA DE CAJA VENTA" previamente.',
                ]);
            }

            // Verificar la modalidad de la venta
            if ($modelSale->codigoModalidad == SiatModalidad::$codigoModalidadCOMPUTARIZADA) {
                $modelInvoice = Invoice::find()->where(['idsale' => $modelSale->id])->one();
                if ($modelInvoice && $modelInvoice->idcontingencia != null && $modelInvoice->idcontingencia0->codigoRecepcion == null) {
                    $transaction->rollBack();
                    return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => 'No puede anular esta factura, la factura de contingencia aún no se envió a impuestos.',
                    ]);
                }
            }
    
            // Obtener el punto de venta del usuario
            $modelUser = UserSystemPoint::findOne(['iduserEnabled' => Yii::$app->user->id, 'idstatus' => UserSystemPoint::$statusACTIVO]);
            if (!$modelUser) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'User system point not found or not active.',
                ]);
            }
    
            // Cambiar el stock al estado normal
            foreach ($modelSale->productStocks as $productStock) {
                $errors = $this->updateStock($documentType, [
                    'id' => $productStock->idproduct,
                    'quantity' => $productStock->quantityinput,
                    'price' => $productStock->price,
                    'idstore' => $productStock->idstore,
                ]);
    
                if ($errors) {
                    $transaction->rollBack();
                    return parent::sendResponse([
                        'statusCode' => 500,
                        'message' => 'Failed to update stock: ' . implode(', ', $errors),
                    ]);
                }
            }
    
            // Registrar los productos como devolución
            $modelSale->idstatus = $modelSale->statusANULADO;
            $montoTotal = $modelSale->montoTotal;
            $modelSale->montoTotal = 0;
    
            if (!$modelSale->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'Failed to update sale status.',
                    'errors' => $modelSale->errors
                ]);
            }
    
            $modelDocument = new Document();
            $modelDocument->idstatus = $modelDocument->statusPROCESADO;
            $modelDocument->idsale = $modelSale->id;
            $modelDocument->iddocumentType = DocumentType::$idTypeSALE_ANNUL;
            $modelDocument->comment = 'VENTA Nro ' . $modelSale->number . ' ANULADA';
            $modelDocument->idstore = 1;
            if (!$modelDocument->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'Failed to save document.',
                    'errors' => $modelDocument->errors
                ]);
            }
    
            $modelCashDocument = new CashDocument();
            $modelCashDocument->idreceiptType = ReceiptType::$SALE_ANNUL;
            $modelCashDocument->idcash = $modelCashOpen->id;
            $modelCashDocument->observation = 'VENTA N°:' . $modelSale->number . ' ANULADA';
            $modelCashDocument->amount = $montoTotal;
            $modelCashDocument->idstatus = $modelCashDocument->statusPROCESADO;
            $modelCashDocument->codigoMetodoPago = $modelSale->codigoMetodoPago;
            $modelCashDocument->idcardService = $modelSale->idcardService;
            $modelCashDocument->idsale = $modelSale->id;
            if (!$modelCashDocument->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => 'Failed to save cash document.',
                    'errors' => $modelCashDocument->errors
                ]);
            }
    
            $productsSale = $modelSale->productStocks;
            $products = array();
            foreach ($productsSale as $item) {
                $products[] = [
                    // 'name' => $item->name,
                    'quantity' => $item->quantityoutput,
                    'price' => $item->price,
                    // 'idunit' => $item->idunit,
                    // 'codigoProducto' => $item->codigoProducto,
                    'id' => $item->idproduct,
                    'idproductstock' => $item->id,
                ];
            }
            

            $errors = $this->saveDocument($modelDocument, $products, $saleDTO->ioSystemBranch); 
            if (!$errors['success']) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => $errors['message'],
                    'errors' => $errors['errors']
                ]);
            }
    
            if ($modelSale->invoice && ($modelSale->codigoModalidad == SiatModalidad::$codigoModalidadCOMPUTARIZADA || $modelSale->codigoModalidad == SiatModalidad::$codigoModalidadELECTRONICA)) {
                $wsdlSiat = new wsdlSiat();
                $modelInvoice = Invoice::findOne(["idsale"=> $modelSale->id]);
                // Aquí puedes agregar el código necesario para procesar las facturas
                $servicio = $wsdlSiat->serviceSWDL($modelInvoice->codigoDocumentoSector, $modelInvoice->codigoModalidad);
                $wsdlSiat = new wsdlSiat($servicio);
               
                $params = array(
                    'SolicitudServicioAnulacionFactura' => array(
                        'codigoAmbiente' => $modelInvoice->codigoAmbiente,
                        'codigoPuntoVenta' => $modelInvoice->codigoPuntoVenta,
                        'codigoSistema' => $modelInvoice->codigoSistema,
                        'codigoSucursal' => $modelInvoice->codigoSucursal,
                        'nit' => $wsdlSiat::$nit,
                        'codigoDocumentoSector' => $modelInvoice->codigoDocumentoSector,
                        'codigoEmision' => 1,
                        'codigoModalidad' => $modelInvoice->codigoModalidad,
                        'cufd' => $modelSale->idsystemPoint0->SiatCufdActive()->cufd,
                        'cuis' => $modelSale->idsystemPoint0->SiatCuisActive()->cuis,
                        'tipoFacturaDocumento' => $modelInvoice->tipoFacturaDocumento,
                        'codigoMotivo' => 1,
                        'cuf' => $modelInvoice->cuf
                    )
                );

                if ($wsdlSiat->success()) {

                    $respons = $wsdlSiat->run('AnulacionFactura', $params, false);
                    // print_r($respons);
                    
                    if ($respons != false) {
                        if ($respons->RespuestaServicioFacturacion->transaccion) {
                            $modelInvoice->responseAnulacion = print_r($respons, true);
                            $modelInvoice->save();

                            $modelInvoice->sendMailAnnul();
                        } else {
                            // echo 'SIAT: ' . $respons->RespuestaServicioFacturacion->codigoDescripcion;
                            return parent::sendResponse([
                                'statusCode' => 200,
                                'message' =>  $respons->transaccion ? 'ANULADO CORRECTAMENTE' : 'SIAT: ' . $respons->RespuestaServicioFacturacion->codigoDescripcion,
                            ]);
                        }
                    } else {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }
    
            $transaction->commit();
            return parent::sendResponse([
                'statusCode' => 200,
                'message' => 'ANULADO CORRECTAMENTE!',
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return parent::sendResponse([
                'statusCode' => 500,
                'message' =>  $e->getMessage(),
                'errors' => $e
            ]);
        }
    }

    public function actionById($id) {
        $sale = $this->modelClass::find()->where(['id' => $id])->with('productStocks')->one();

        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Sale with ID $id not found.",
            ]);
        }
    
        return $sale;
    }    

    private function getIdStore($products) {
        $user = Yii::$app->user->identity;
        // Verificar si ese usuario esta restringido para vender de un almacen en espesifico
        // en caso de que el idstoreMain = null este usuario no tiene restricciones
        $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);
        if($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreMain)) {
            return $modelUserSystemPoint->idstoreMain;
        }
        // Recorrer el array de productos
        foreach ($products as $product) {
            // Verificar si el idstore está definido y no es null
            if (isset($product->idstore) && $product->idstore !== null) {
                return $product->idstore;
            }
        }
    
        // Si no se encontró un idstore válido, consultar el primer idstore de la base de datos
        $store = Store::find()->orderBy(['id' => SORT_ASC])->one();
        // Verificar si se encontró un registro en la tabla Store
        if ($store !== null) {
            return $store->id;
        }
        // Retornar null si no se encuentra un idstore en el array ni en la consulta
        return null;
    }
  
    public function actionValidateNit($nit)
    {
        $codigoExcepcion = 1;
        $isValidNit = ValidateNit::isValid($nit);
        if ($isValidNit['codigoExcepcion'] === -1) {
            $codigoExcepcion = 1;
        } elseif ($isValidNit['codigoExcepcion'] === false) {
            $codigoExcepcion = 1;
            $isValidNitString = $isValidNit['codigoExcepcion'] ? 'true' : 'false';
            return [
                'codigoExcepcion' => $codigoExcepcion,
                'nit'  => $nit,
                'siat' =>  'ValidateNit::isValid($nit) Responde: ' . $isValidNitString,
                'message' => "Para la Factura NIT(" . $nit . ") inválido.",
            ];
        } else {
            $codigoExcepcion = 0;
        }
        $isValidNitString = $isValidNit['codigoExcepcion'] ? 'true' : 'false';
        return [
            'codigoExcepcion' => $codigoExcepcion,
            'nit'  => $nit,
            'siat' => 'ValidateNit::isValid($nit) Responde: ' . $isValidNitString
        ]; 
        // 3348169011
        // 7550286015
    }
}
