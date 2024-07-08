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

use app\modules\apiv1\models\CfgProductStore; // stock de los productos
use app\modules\apiv1\models\CfgProductBranch; // configuracion de los productos
use app\models\DocumentType;

class SaleController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Sale';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];
        return $actions;
    }

    public function actionListar()
    {
        $db = $this->prepareData(true);
        Productstock::setCustomDb($db);

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
        // Conexión a la base de datos raíz
        $db = $this->prepareData();
        Product::setCustomDb($db);
        Customer::setCustomDb($db);
        Unit::setCustomDb($db);
        // Conexión a la base de datos de la sucursal para verificar el stock
        $db = $this->prepareData(true);
        CfgProductStore::setCustomDb($db);
        CfgProductBranch::setCustomDb($db);

        $saleForm = new SaleForm();
        $saleForm->load(Yii::$app->request->post(), '');

        $user = Yii::$app->user->identity;
        if ($saleForm->validate()) {
            // Se verifica y se registra el producto
            $products = $this->saveProducts($saleForm->products, $user);
            if (isset($products['status']) && $products['status'] == 500) {
                return $products;
            }

            if (!$saleForm->idcustomer && $saleForm->idcustomer !== '' && !empty($saleForm->razonSocial) && !empty($saleForm->numeroDocumento)) {
                $customer = new Customer();
                $customer->razonSocial = $saleForm->razonSocial;
                $customer->numeroDocumento = $saleForm->numeroDocumento;
                $customer->iddocumentNumberType = $saleForm->idtypeDocument;
                $customer->phone = $saleForm->phone;
                $customer->iduser = $user->iduser;

                if ($customer->save()) {
                    $saleForm->idcustomer = $customer->id;
                } else {
                    return [
                        'status' => 500,
                        'message' => 'Failed to save customer',
                        'errors' => $customer->errors
                    ];
                }
            } else if (!empty($saleForm->idcustomer)) {
                $existingCustomer = Customer::findOne($saleForm->idcustomer);
                if (!$existingCustomer) {
                    return [
                        'status' => 500,
                        'message' => 'Customer specified does not exist.',
                    ];
                }
            }

            // Continuar con el proceso de registro de la venta
            $db = $this->prepareData(true);
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
                return [
                    'status' => 500,
                    'message' => 'Failed to save sale',
                    'errors' => $sale->errors
                ];
            }

            $documentType = DocumentType::findOne(['type' => 'VENTA']); // Obtener el tipo de salida
            // Guardar los documentos y productos relacionados con la venta
            $productsResult = [];
            if (isset($products) && is_array($products)) {
                foreach ($products as $productData) {
                    // Guardar el documento
                    Document::setCustomDb($db);
                    $document = new Document();
                    $document->attributes = $productData;
                    $document->idcliente = $saleForm->idcustomer;
                    $document->iddocumentType = $documentType->id; // Tipo de documento venta = 3
                    $document->number = $productData['quantity'];
                    $document->iduser = $user->iduser;
                    $document->idsale = $sale->id;

                    if (!$document->save()) {
                        return [
                            'status' => 500,
                            'message' => 'Failed to save Document',
                            'errors' => $document->errors
                        ];
                    }

                    $errors = $this->updateStock($documentType, $productData); // Actualizamos el stock
                    if ($errors != null) {
                        return [
                            'status' => 500,
                            'message' => 'Failed to update stock',
                            'errors' => $errors
                        ];
                    }

                    // Guardar el producto
                    Productstock::setCustomDb($db);
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
                        return [
                            'status' => 500,
                            'message' => 'Validation failed for Product',
                            'errors' => $product->errors
                        ];
                    }
                    $productsResult[] = $product;
                }

                // Guardar cada producto de la venta
                foreach ($productsResult as $product) {
                    if (!$product->save()) {
                        return [
                            'status' => 500,
                            'message' => 'Failed to save Product',
                            'errors' => $product->errors
                        ];
                    }
                }
            }

            // Todo se ha guardado exitosamente
            return [
                'status' => 201,
                'message' => 'Invoice created successfully',
            ];
        } else {
            // Si la validación del formulario de venta falla, retornar errores
            return [
                'status' => 500,
                'message' => 'Validation failed',
                'errors' => $saleForm->errors
            ];
        }
    }
 
    
    public function actionProductsBySale($idsale)
    {
        $db = $this->prepareData(true);
        Productstock::setCustomDb($db);

        $sale = $this->modelClass::find()->where(['id' => $idsale])->with('productStocks')->one();

        if (!$sale) {
            throw new NotFoundHttpException("Sale with ID $idsale not found.");
        }

        $productIds = [];
        foreach ($sale->productStocks as $productStock) {
            $productIds[] = $productStock->idproduct;
        }

        $db = $this->prepareData();
        Product::setCustomDb($db);

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
            $newProduct->idunit = $productData['idunit'] ?? null; // Asegúrate de manejar el caso si idunit no está definido
            $newProduct->idstatus = 1;
            $newProduct->iduser = $user->iduser;
    
            // Validar y guardar el producto
            if (!$newProduct->validate()) {
                return [
                    'status' => 500,
                    'message' => 'Validation failed for Product',
                    'errors' => $newProduct->errors
                ];
            }
      
            if (!$newProduct->save()) {
                return [
                    'status' => 500,
                    'message' => 'Failed to save Product',
                    'errors' => $newProduct->errors
                ];
            }
            // Añadir el id del producto registrado a productData
            $productData['id'] = $newProduct->id;
            $products[] = $productData; 
        }
    
        return $products;
    }
    

    // actualizar el stock de un producto
    protected function updateStock($typeDocument, $product)
    {
        $productBranch = CfgProductBranch::findOne($product['id']);   
        if ($productBranch && $productBranch->controlInventory) {
            $cfgProductStores = CfgProductStore::findAll(['id' => $product['id']]);
    
            if (!$cfgProductStores) {
                return 'No se encontró el registro del producto en CfgProductStore para el producto ID ' . $product['id'];
            }
    
            // Verificar si se proporciona un idStore
            if (isset($product['idStore']) && !empty($product['idStore'])) {
                $cfgProductStore = CfgProductStore::findOne(['id' => $product['id'], 'idstore' => $product['idStore']]);
                if ($cfgProductStore) {
                    if ($typeDocument->action == 1) {
                        $cfgProductStore->stock = floatval($cfgProductStore->stock) + $product['quantity'];
                    } else if ($typeDocument->action == -1) {
                        $cfgProductStore->stock = floatval($cfgProductStore->stock) - $product['quantity'];
                    }
                    if ($cfgProductStore->save()) {
                        return null;
                    } else {
                        return 'Error al actualizar el stock para el producto ID ' . $product['id'] . ' en la tienda ID ' . $product['idStore'] . ': ' . json_encode($cfgProductStore->errors);
                    }
                } else {
                    return 'No se encontró el registro del producto en la tienda ID ' . $product['idStore'];
                }
            } else { // Si no se proporciona idStore, decrementar la cantidad total entre los registros disponibles
                $remainingQuantity = $product['quantity'];
                foreach ($cfgProductStores as $cfgProductStore) {
                    if ($remainingQuantity <= 0) {
                        break; // Salir del bucle si ya se ha cubierto toda la cantidad
                    }
                    // Actualizar el stock segun la acción del tipo de documento
                    if ($typeDocument->action == 1) {
                        $cfgProductStore->stock = floatval($cfgProductStore->stock) + $remainingQuantity;
                        $remainingQuantity = 0; // Toda la cantidad ha sido añadida
                    } else if ($typeDocument->action == -1) {
                        if ($cfgProductStore->stock >= $remainingQuantity) {
                            $cfgProductStore->stock = floatval($cfgProductStore->stock) - $remainingQuantity;
                            $remainingQuantity = 0; // Toda la cantidad ha sido restada
                        } else {
                            $remainingQuantity -= $cfgProductStore->stock; // Restar solo lo disponible en este registro
                            $cfgProductStore->stock = 0;
                        }
                    }

                    if (!$cfgProductStore->save()) {
                        return 'Error al actualizar el stock para el producto ID ' . $product['id'] . ' en la tienda ID ' . $cfgProductStore->idstore . ': ' . json_encode($cfgProductStore->errors);
                    }
                }
                return null;
            }
        } else {
            return null;
        }
    }
    
    
}
