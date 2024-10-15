<?php
namespace app\modules\service\controllers;

use Yii;
use app\modules\service\helpers\DbConnection;
use app\models\Document;
use yii\data\ActiveDataProvider;
use app\modules\service\controllers\BaseController;
use app\modules\service\models\dto\SaleDTO;
use app\modules\service\models\Sale;
use app\models\Invoice;

use app\models\DocumentType;
use app\modules\apiv1\models\SiatTipoDocumentoIdentidad;
use app\modules\apiv1\helpers\ValidateNit;

use app\models\UserSystemPoint;
use app\models\SystemPoint;
use app\models\CurrentAccountCustomer;
use app\models\Productstock;

class SaleController extends BaseController
{
    public $modelClass = 'app\modules\service\models\Sale';
    public function actions()
    {
        $actions = parent::actions();
        unset(
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['options']
        );
        
        return $actions;
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, [])) {
            return parent::sendResponse(['statusCode' => 404, 'message' => 'The requested page does not exist.']);
        }
        return parent::beforeAction($action);
    }

    public function actionInsert()
    {
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
        $user = Yii::$app->user->identity;

        if ($saleDTO->validate()) {
            // AQUI VALIDAMOS QUE SEA UN NIT
            $codigoExcepcion = 0;
            // AQUI VALIDAMOS QUE SEA UN NIT
            if(!$saleDTO->validateCodigoExcepcion) { // en caso que sea falso quiere decir que ese nit no se ha validado
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

            if (!$modelDocument->save()) {
                $transaction->rollBack();
                return parent::sendResponse([
                    'statusCode' => 500,
                    'message' => 'Failed to save Document',
                    'errors' => $modelDocument->errors
                ]);
            }   
            
            // Actualizamos el stock 
            $errors = $this->saveDocument($modelDocument, $saleDTO->products, $saleDTO->ioSystemBranch); 

            if (!$errors['success']) {
                $transaction->rollBack();
                return $errors;
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
                        'errors' => $resultINVOICE['message']
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
            // Si la validación del formulario de venta falla, retornar errores
            return parent::sendResponse([
                'statusCode' => 500,
                'message' => 'Validation failed',
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
               
                $modelProductstock->idstore = $idstore;
                
                /* FIN VERIFICA */
                if ($modelProductstock->montoDescuento == null)
                    $modelProductstock->montoDescuento = 0;
                
                if ($modelProductstock->save()) {
                    $success = true;
                    break;
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
}
