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
use app\modules\apiv1\models\SaleForm;
use app\modules\apiv1\models\Sale;
use app\modules\apiv1\models\Productstock;
use app\modules\apiv1\models\ProductStore; // stock de los productos
use app\modules\apiv1\models\ProductBranch; // configuracion de los productos
use app\models\DocumentType; 
use app\modules\apiv1\models\Store;

use app\models\SystemPoint;
use app\models\UserSystemPoint;
use app\models\SiatModalidad;
use app\models\Invoice;

use app\modules\apiv1\models\SincronizarListaProductosServicios;

use app\modules\apiv1\models\SiatTipoDocumentoIdentidad;
use app\modules\apiv1\helpers\ValidateNit;

class SaleController extends BaseController {

    public $modelClass = 'app\modules\apiv1\models\Sale';

    public function actions() {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];
        return $actions;
    }

    public function actionListar() {
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

    public function actionInsert() {


        // $q=new Wsdlsiat();
        // echo $q->runOk();
        // echo Yii::$app->user->getId();
        $modelUserSystemPoint = new UserSystemPoint();
        $modelUser = $modelUserSystemPoint->getModel();

//        $modelCashOpen = Cash::model()->find("idstatus=" . Cash::model()->statusABIERTO . ' and iduser=' . Yii::app()->user->getId());
//        if ($modelCashOpen == null) {
//            echo System::conditionOpen(false, 'Debe realizar una  �APERTURA DE CAJA VENTA�  previamente ');
//            return;
//        }


        $saleForm = new SaleForm();
        $saleForm->load(Yii::$app->request->post(), '');

        if ($saleForm->validate()) {
             // AQUI VALIDAMOS QUE SEA UN NIT
             if(!$saleForm->validateCodigoExcepcion) { // en caso que sea falso quiere decir que ese nit no se ha validado
                $siatTipoDoc = SiatTipoDocumentoIdentidad::findOne(['id' => $saleForm->codigoTipoDocumentoIdentidad]); // consultar si debe validad ese nit
                if($siatTipoDoc->commandVerified == 'verificarNit') { // Es un documento que debe ser validado en impuesto
                    $codigoExcepcion = ValidateNit::isValid($saleForm->numeroDocumento);
                    if($codigoExcepcion == 0) {
                        return parent::sendResponse([
                            'statusCode' => 422,
                            'message' => "El documento '" . $siatTipoDoc->descripcion . "' con el numero '" . $saleForm->numeroDocumento . "' no es valido con Impuestos SIAT!",
                        ]);
                    } 
                }
            }

            // validar cliente
            if (!$saleForm->idcustomer || $saleForm->idcustomer == null) { // en caso de que no se envie el id guardar
                $customer = new Customer();
                $customer->razonSocial = $saleForm->razonSocial;
                $customer->name = $saleForm->razonSocial;
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
            // recorre los productos y debe registrar en caso de que no este registrado un producto
            $products = $this->saveProducts($saleForm->products, $user);
            if (isset($products['statusCode']) && $products['statusCode'] == 500) {
                return parent::sendResponse($products);
            }

            // Continuar con el proceso de registro de la venta
            $modelSale = new Sale();

            $modelSale->attributes = $saleForm->attributes;
            $modelSale->montoTotal = $saleForm->total;
            $modelSale->discountamount = $saleForm->discountamount;
            $modelSale->subTotal = isset($saleForm->subTotal) ? $saleForm->subTotal : $saleForm->total - $saleForm->discountamount;
            $modelSale->montoRecibido = isset($saleForm->montoRecibido) ? $saleForm->montoRecibido : $saleForm->total;
            $modelSale->numeroDocumento = $saleForm->numeroDocumento;
            $modelSale->codigoTipoDocumentoIdentidad = $saleForm->codigoTipoDocumentoIdentidad;
            $modelSale->razonSocial = $saleForm->razonSocial;
            $modelSale->phone = $saleForm->phone;
            $modelSale->codigoMetodoPago = $saleForm->codigoMetodoPago;
            $modelSale->codigoDocumentoSector = 1; // siat factura compra y venta
            //
            //complete data for invoice
            $modelSystemPoint = SystemPoint::getModelCurrent();
            $modelSale->codigoModalidad = $modelSystemPoint->idsiatBranch0->codigoModalidad;
            $modelSale->idsystemPoint = $modelUser->idsystemPoint;

            if (!$modelSale->save()) {
                return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to save sale',
                            'errors' => $modelSale->errors
                ]);
            }

            //
            //// Guardar el documento
            $modelDocument = new Document();

            $modelDocument->iddocumentType = DocumentType::$idTypeSALE; // Tipo de documento venta = 3
            $modelDocument->comment = 'VENTA Nro ' . $model->number . ' ';
            $modelDocument->iduser = $user->iduser;
            $modelDocument->idsale = $model->id;

            if (!$modelDocument->save()) {
                return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to save Document',
                            'errors' => $modelDocument->errors
                ]);
            }
            
            $modelSale->iddocument=$modelDocument->id;

            if (!$modelSale->save()) {
                return parent::sendResponse([
                            'statusCode' => 500,
                            'message' => 'Failed to save sale iddocument',
                            'errors' => $modelSale->errors
                ]);
            }

            $errors = $this->saveDocument($modelDocument, $products); // Actualizamos el stock 
            // Guardar los documentos y productos relacionados con la venta
            $productsResult = [];

            if ($model->invoice == 1 && ($model->codigoModalidad == SiatModalidad::$codigoModalidadCOMPUTARIZADA || $model->codigoModalidad == SiatModalidad::$codigoModalidadELECTRONICA)) {

                // $model->cafc = $cafc;
                $model->codigoExcepcion = 0;
                $codigoExcepcion;
                $modelInvoice = new Invoice;
                
                
                // if ($resultINVOICE['success'] == false || ($modelInvoice->codigoEmision == 1 && !( $modelInvoice->transaccion == true))) {
                //     $success = false;
                //     //$modelInvoice->sentToFile();
                //     //$transaction->rollback();
                //     echo System::hasErrors('VENTA NO REGISTRADA, Intente nuenamente. <br>' . $resultINVOICE['message']);
                //     return;
                // } 
            }

            // Todo se ha guardado exitosamente
            return parent::sendResponse([
                        'statusCode' => 201,
                        'message' => 'Invoice created successfully',
            ]);
        } else {
            // Si la validaci�n del formulario de venta falla, retornar errores
            return parent::sendResponse([
                        'statusCode' => 500,
                        'message' => 'Validation failed',
                        'errors' => $saleForm->errors
            ]);
        }
    }

    public function saveDocument($modelDocument, $products) {
        $xml = '';
        $iddocument = $modelDocument->id;
        $model = new Productstock();
        $success = true;
        $message = '';

        if ($iddocument != Null && isset($model) && isset($products)) {
            $productosWS = array();
            foreach ($products as $fila) {
                $model = new Productstock();
                $model->nprocess=1;
                $model->quantityoutput = $fila['quantity'];
                $model->price = $fila['price'];

                $model->idproduct = $fila['id'];

                if ($fila['quantity'] * 1 == 0) {
                    $success = false;
                    $message .= "La cantidad Producto [" . $fila['name'] . "], debe ser mayor a 0";
                    break;
                }

                // if ($modelDocument->iddocumentType == DocumentType::$idTypeSALE && $fila['price'] * 1 == 0) {   
                //     $success = false;
                //     $message .= "La precio P/U Producto [" . $fila['name'] . "], debe ser mayor a 0";
                //     break;
                // }


                if ($model->comment != null) {
                    $model->comment = trim($model->comment);
                    if ($model->comment == '')
                        $model->comment = null;
                }
                if ($model->cost == null || $model->cost == '')
                    $model->cost = null;

                if ($model->price == null || $model->price == '')
                    $model->price = null;
                $inc = 1;
                if ($modelDocument->iddocumentType0->action == 1) {
                    $model->quantityinput = $fila['quantity'];
                    $inc = 1;
                }
                if ($modelDocument->iddocumentType0->action == -1) {
                    $model->quantityoutput = $fila['quantity'];
                    $inc = -1;
                }

                $model->iddocument = $iddocument;

                if ($modelDocument->idsale != null)
                    $model->idsale = $modelDocument->idsale;

                if ($modelDocument->idpurchase != null)
                    $model->idpurchase = $modelDocument->idpurchase;

                if ($modelDocument->idproductionOrder != null)
                    $model->idproductionOrder = $modelDocument->idproductionOrder;

                $idstore = 1;
                // echo "[$modelDocument->idstore]";
                if ($modelDocument->idstore != null) {

                    $idstore = $model->idstore = $modelDocument->idstore;
                }

                // echo "[idstore=$idstore]";


                /* VERIFICA STOCK DE PRODUCTO EN SUCURSAL 
                 */


                $modelProductStore = ProductStore::findOne(['id' => $model->idproduct, 'idstore' => $idstore]);
                $modelProductBranch = ProductBranch::findOne(['id' => $model->idproduct]);

                if ($modelProductStore == null) {
                    $modelProductStore = new ProductStore();
                    $modelProductStore->id = $model->idproduct;

                    $modelProductStore->stock = 0;
                    $modelProductStore->idstore = $idstore;
                    if (!$modelProductStore->save()) {
                        print_r($modelProductStore->getErrors());
                        $success = false;
                        $message .= "<br> problema: Al INICIAR EL STOCK en la Sucursal del Producto[" . $fila['name'] . "]";
                        break;
                    }
                }

                $previousStock = $modelProductStore->stock;
                $modelProductStore->stock += $inc * $fila['quantity'];

                //$prod=$modelProductBranch->product->typeService;

                $dd = $modelProductBranch->controlInventory;

                if ($inc == -1 && $modelProductBranch != null && $modelProductBranch->controlInventory == true && $modelProductStore->stock < 0) {
                    $missingQuantity = $previousStock < 0 ? $fila['quantity'] : $modelProductStore->stock * -1;
                    $success = false;
                    $message .= "<br>Producto [" . $fila['name'] . "], la cantidad faltante es [$missingQuantity]";
                    $message .= "<br>Almac�n [" . $modelProductStore->idstore0->name . "]";
                    break;
                }

               
                /* FIN VERIFICA */

                if ($model->montoDescuento == null)
                    $model->montoDescuento = 0;
                
             
                if ($model->save()) {

                    if (!$modelProductStore->update()) {
                        $success = false;
                        $message .= "<br>Problema: Al actualizar el stock de producto[" . $fila['name'] . "]";
                        break;
                    } 
                } else {
                    $success = false;
                    $message .= "<br>Problema: Al registrar Movimiento de Producto[" . $fila['name'] . "]";
                    print_r($model->getErrors());
                    break;
                }
                
            }

            return array('success' => $success, 'message' => $message, 'xmlDetailProducts' => $xml);
        } else {
            return array('success' => false, 'message' => $message);
            throw new CrugeException("<br>Error al registrar el documento Nro $modelDocument->number.", 483);
        }
    }

    // actualizar el stock de un producto
    protected function saveProductStock11s($modelDcument, $product) {
        echo $modelDcument->id;

        if (isset($products) && is_array($products)) {
            foreach ($products as $productData) {

                // $errors = $this->updateStock($documentType, $productData); // Actualizamos el stock
                // if ($errors != null) {
                //     return parent::sendResponse([
                //                 'statusCode' => 500,
                //                 'message' => 'Failed to update stock',
                //                 'errors' => $errors
                //     ]);
                // }

                // // Guardar el producto
                $product = new Productstock();
                $product->attributes = $productData;
                $product->nprocess = 1;
                $product->quantityoutput = $productData['quantity'];
                $product->price = $productData['quantity'] * $productData['price'];
                $product->iduser = $user->iduser;
                $product->idproduct = $productData['id'];
                $product->iddocument = $modelDocument->id;  // Aqu� se agrega el id del document registrado
                $product->idsale = $model->id;

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

    public function actionCancelar($idsale) {
        $sale = $this->modelClass::find()->where(['id' => $idsale])->with('productStocks')->one();
        
        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Sale with ID $idsale not found.",
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
