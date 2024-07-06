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

use sizeg\jwt\Jwt;

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
        $db = $this->prepareData();
        Product::setCustomDb($db);
        Customer::setCustomDb($db);
        Unit::setCustomDb($db);
        // Category::::setCustomDb($db);
    
        $saleForm = new SaleForm();
        $saleForm->attributes = Yii::$app->request->post();
        
        $user = Yii::$app->user->identity;    
        if ($saleForm->validate()) {
            foreach ($saleForm->attributes['products'] as $productData) {
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
    
                $products[] = $newProduct;
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
            } else if(!empty($saleForm->idcustomer)) {
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
    
            // Guardar los documentos y productos relacionados con la venta
            $products = [];
            if (isset($saleForm->attributes['products']) && is_array($saleForm->attributes['products'])) {
                foreach ($saleForm->attributes['products'] as $productData) {
                    // Guardar el documento
                    Document::setCustomDb($db);
                    $document = new Document();
                    $document->attributes = $productData;
                    $document->idcliente = $saleForm->idcustomer;
                    $document->iddocumentType = 3; // tipo de documento venta = 3
                    $document->number = $productData['count'];
                    $document->iduser = $user->iduser;
                    $document->idsale = $sale->id;
    
                    if (!$document->save()) {
                        return [
                            'status' => 500,
                            'message' => 'Failed to save Document',
                            'errors' => $document->errors
                        ];
                    }
    
                    // Guardar el producto
                    Productstock::setCustomDb($db);
                    $product = new Productstock();
                    $product->attributes = $productData;
                    $product->nprocess = 1;
                    $product->quantityinput = $productData['count'];
                    $product->price = $productData['count'] * $productData['price'];
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
                    $products[] = $product;
                }
    
                // Guardar cada producto de la venta
                foreach ($products as $product) {
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

}
