<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use app\modules\apiv1\models\dto\OrderDTO;
use app\modules\apiv1\models\Order;
use app\models\ProductOrder;
use app\models\Status;

class OrderController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Order';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'insert' => ['POST'],
                'current' => ['GET'],
                'edit' => ['PUT', 'PATCH'],
            ],
        ];

        return $behaviors;
    }


    public function actionIndex()
    {
        $query = $this->modelClass::find()->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

    public function actionInsert() {
        
        $transaction = Yii::$app->iooxsBranch->beginTransaction();

        $dtoOrder = new OrderDTO();
        $dtoOrder->load(Yii::$app->request->post(), ''); 

        if ($dtoOrder->validate()) {
            $modelOrder = new Order();
            $modelOrder->attributes = $dtoOrder->attributes;
            $modelOrder->idstatus = (new Status())->EN_PROCESO; //  cuando la orden esta en proceso. (new Status())->FINALIZADO cuando la orden ha finalizado;
            $modelOrder->number =  $this->getNextOrderNumber(); // poner el numero reiniciante
            if ($modelOrder->save()) {                
                $result = $this->saveOrderProduct($dtoOrder->productArr, $modelOrder->id);

                if (!$result['success']) {
                    $transaction->rollBack();
                    return parent::sendResponse([
                            'statusCode' => 400,
                            'message' => $result['message'],
                            'errors' => $result['error']
                    ]);
                }

                $transaction->commit();

                $modelOrder = Order::findOne($modelOrder->id);
                return parent::sendResponse([
                            'statusCode' => 201,
                            'message' => 'Order created successfully',
                            'data' => $modelOrder,
                ]);
            } else {
                // Si falló el guardado, revierte la transacción
                $transaction->rollBack();
                return parent::sendResponse([
                            'statusCode' => 400,
                            'message' => 'Failed to save sale',
                            'errors' => $modelOrder->errors
                ]);
            }
        } else {
            $transaction->rollBack();
            return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => '¡No es posible registrar la orden!',
                    'errors' => $dtoOrder
                ]);
        }
    }

    private function saveOrderProduct($products, $idorder) {
        foreach ($products as $item) {
            $modelProductOrder = new ProductOrder();
            $modelProductOrder->idorder = $idorder;
            $modelProductOrder->idproduct = $item['idproduct'];
            $modelProductOrder->price = $item['price'];
            $modelProductOrder->comment = $item['comment'];
            $modelProductOrder->quantityoutput = $item['quantityoutput'];

            if (!$modelProductOrder->save()) {
                return [
                    'success' => false,
                    'message' => 'No fue posible registrar el pago de la venta con id ' . $item['idproduct'] . ' con el monto de ' . $item['monto'],
                    'error' => $modelProductOrder->errors
                ];
            }
        }
        return ['success' => true];
    }

    private function getNextOrderNumber() {
        $todayStart = (new \DateTime('today'))->format('Y-m-d H:i:s');
        $todayEnd = (new \DateTime('tomorrow'))->format('Y-m-d H:i:s');
    
        $lastOrder = Order::find()
            ->where(['between', 'dateCreate', $todayStart, $todayEnd])
            ->orderBy(['number' => SORT_DESC])
            ->one();
    
        if ($lastOrder) {
            return $lastOrder->number + 1;
        } else {
            return 1;
        }
    }
    
    public function actionEdit($id) {
        $modelOrder = $this->modelClass::findOne($id);
        if (!$modelOrder) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "The orden with ID $id not found.",
            ]);
        }

        $transaction = Yii::$app->iooxsBranch->beginTransaction();

        $dtoOrder = new OrderDTO();
        $dtoOrder->isEdit = true; // este parametro indica en modo edicion
        $dtoOrder->load(Yii::$app->request->post(), ''); 

        if ($dtoOrder->validate()) {
            // $modelOrder->attributes = $dtoOrder->attributes;
            $modelOrder->idtable = $dtoOrder->idtable;
            $modelOrder->montoTotal = $dtoOrder->montoTotal;
            if($modelOrder->idcustomer) {
                $modelOrder->idcustomer = $dtoOrder->idcustomer;
            }
            if($modelOrder->comment) {
                $modelOrder->comment = $dtoOrder->comment;
            }

            // Eliminar productos anteriores asociados a la orden
            // ProductOrder::deleteAll(['idorder' => $modelOrder->id]);
            
            $result = $this->updateOrderProduct($dtoOrder->productArr, $modelOrder->id);

            if ($modelOrder->save()) {                
              

                if (!$result['success']) {
                    $transaction->rollBack();
                    return parent::sendResponse([
                            'statusCode' => 400,
                            'message' => $result['message'],
                            'errors' => $result['error']
                    ]);
                }

                $transaction->commit();

                $modelOrder = Order::findOne($modelOrder->id);
                return parent::sendResponse([
                            'statusCode' => 201,
                            'message' => 'Order updated successfully',
                            'data' => $modelOrder,
                ]);
            } else {
                // Si falló el guardado, revierte la transacción
                $transaction->rollBack();
                return parent::sendResponse([
                            'statusCode' => 400,
                            'message' => 'Failed to save sale',
                            'errors' => $modelOrder->errors
                ]);
            }
        } else {
            $transaction->rollBack();
            return parent::sendResponse([
                    'statusCode' => 400,
                    'message' => '¡No es posible actualizar la orden!',
                    'errors' => $dtoOrder
                ]);
        }
    }

    private function updateOrderProduct($products, $idorder) {
        // Obtener todos los productos existentes en la orden
        $existingProducts = ProductOrder::find()->where(['idorder' => $idorder])->all();
        $existingProductMap = [];
        
        // Crear un mapa de productos existentes usando idproduct como clave
        foreach ($existingProducts as $product) {
            $existingProductMap[$product->idproduct] = $product;
        }
        
        foreach ($products as $item) {
            $idProduct = $item['idproduct'];
            
            if (isset($existingProductMap[$idProduct])) {
                // El producto ya existe en la base de datos
                $modelProductOrder = $existingProductMap[$idProduct];
                
                // Actualizar el campo previousQuantityoutput con el valor actual de quantityoutput
                $modelProductOrder->previousQuantityoutput = $modelProductOrder->quantityoutput;
                
                // Actualizar el nuevo valor de quantityoutput
                $modelProductOrder->quantityoutput = $item['quantityoutput'];
                
                // Actualizar otros campos
                $modelProductOrder->price = $item['price'];
                $modelProductOrder->comment = $item['comment'];
                
                // Eliminar el producto del mapa para después detectar cuáles han sido eliminados
                unset($existingProductMap[$idProduct]);
            } else {
                // El producto es nuevo, crear un nuevo registro
                $modelProductOrder = new ProductOrder();
                $modelProductOrder->idorder = $idorder;
                $modelProductOrder->idproduct = $idProduct;
                $modelProductOrder->price = $item['price'];
                $modelProductOrder->comment = $item['comment'];
                
                // Poner quantityoutput como el valor actual y previousQuantityoutput en 0
                $modelProductOrder->quantityoutput = $item['quantityoutput'];
                $modelProductOrder->previousQuantityoutput = 0;
            }
    
            // Guardar el producto (tanto los nuevos como los actualizados)
            if (!$modelProductOrder->save()) {
                return [
                    'success' => false,
                    'message' => 'No fue posible registrar el producto con id ' . $idProduct,
                    'error' => $modelProductOrder->errors
                ];
            }
        }
    
        // Para los productos que quedan en el mapa, significa que ya no existen en el nuevo pedido
        // Por lo tanto, se les debe poner quantityoutput a 0 y actualizar previousQuantityoutput
        foreach ($existingProductMap as $removedProduct) {
            $removedProduct->previousQuantityoutput = $removedProduct->quantityoutput;
            $removedProduct->quantityoutput = 0;
            
            if (!$removedProduct->save()) {
                return [
                    'success' => false,
                    'message' => 'No fue posible actualizar el producto eliminado con id ' . $removedProduct->idproduct,
                    'error' => $removedProduct->errors
                ];
            }
        }
    
        return ['success' => true];
    }    

    public function actionCurrent()
    {
        $statusProcessed = (new Status())->EN_PROCESO;

        $orders = Order::find()
            ->andWhere(['idstatus' => $statusProcessed])
            // ->orWhere(['idstatus' => null])
            ->orderBy(['dateCreate' => SORT_ASC])
            ->all();

        return $orders; 
    }

}