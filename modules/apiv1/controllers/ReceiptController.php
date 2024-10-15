<?php

namespace app\modules\apiv1\controllers;

use Yii;
use yii\filters\AccessControl;
use app\modules\apiv1\controllers\BaseController;
use app\modules\apiv1\models\Receipt;
use app\modules\apiv1\models\ReceiptSale;
use app\modules\apiv1\models\Customer;
use app\modules\apiv1\models\dto\ReceiptDTO;
use app\modules\apiv1\models\Sale;
use yii\db\Query;
use app\modules\apiv1\helpers\Pdf;

use app\models\CurrentAccountCustomer;

class ReceiptController extends BaseController {

    public $modelClass = 'app\modules\apiv1\models\Receipt';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'insert' => ['POST'],
                'receipt-sale' => ['POST'],
                'deudores' => ['GET'],
                'by-customer' => ['GET'],
                'remove' => ['DELETE'],
                'by-customer-data' => ['GET'],
                'by-id' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $query = Receipt::find()->orderBy(['dateCreate' => SORT_ASC]);
        return $query->all();
    }

    public function actionInsert() {
        $transaction = Yii::$app->iooxsBranch->beginTransaction();

        $dtoReceipt = new ReceiptDTO();
        $dtoReceipt->load(Yii::$app->request->post(), ''); 

        if ($dtoReceipt->validate()) {
            $modelReceipt = new Receipt();
            $modelReceipt->attributes = $dtoReceipt->attributes;

            foreach ($dtoReceipt->salesArr as $sale) {
                $modelReceipt->idsale = $sale['idsale'];
                break;
            }

            if ($modelReceipt->save()) {
                
                $model_cac = new CurrentAccountCustomer();
                $model_cac->idcustomer = $modelReceipt->idcustomer;
                $model_cac->credit = $modelReceipt->montoTotal;
                $model_cac->idreceipt = $modelReceipt->id;
                $model_cac->comment = 'NRO RECIBO:  ' . $modelReceipt->number;
                $model_cac->save();
                
                // No es necesario guardar en cashDocument, ya hay un trigger que lo hace
                $result = $this->saveReceiptSale($dtoReceipt->salesArr, $modelReceipt->id);

                if (!$result['success']) {
                    // Si ocurre un error en saveReceiptSale, revierte la transacción
                    $transaction->rollBack();
                    return parent::sendResponse([
                                'statusCode' => 400,
                                'message' => $result['message'],
                                'errors' => $result['error']
                    ]);
                }

                $transaction->commit();

                $modelReceipt = Receipt::findOne($modelReceipt->id);
                return parent::sendResponse([
                            'statusCode' => 201,
                            'message' => 'Receipt created successfully',
                            'data' => $modelReceipt,
                ]);
            } else {
                // Si falló el guardado, revierte la transacción
                $transaction->rollBack();
                return parent::sendResponse([
                            'statusCode' => 400,
                            'message' => 'Failed to save sale',
                            'errors' => $modelReceipt->errors
                ]);
            }
        } else {
            $transaction->rollBack();
            return parent::sendResponse([
                        'statusCode' => 400,
                        'message' => '¡Información no válida!',
                        'errors' => $dtoReceipt->errors
            ]);
        }
    }

    private function saveReceiptSale($sales, $idreceipt) {
        foreach ($sales as $item) {
            $modelReceiptSale = new ReceiptSale();
            $modelReceiptSale->idreceipt = $idreceipt;
            $modelReceiptSale->idsale = $item['idsale'];
            $modelReceiptSale->monto = $item['monto'];

            if (!$modelReceiptSale->save()) {
                return [
                    'success' => false,
                    'message' => 'No fue posible registrar el pago de la venta con id ' . $item['idsale'] . ' con el monto de ' . $item['monto'],
                    'error' => $modelReceiptSale->errors
                ];
            }
        }
        return ['success' => true];
    }

    public function actionDeudores() {
        $query = Sale::find()
                ->alias('s')
                ->select(['s.*', 'totalPagos' => 'COALESCE(SUM(rs.monto), 0)'])
                ->leftJoin('receiptSale rs', 's.id = rs.idsale')
                ->where(['s.idtypeCharge' => 2])
                ->orderBy(['dateCreate' => SORT_ASC])
                ->groupBy(['s.id', 's.montoTotal'])
                ->having('COALESCE(SUM(rs.monto), 0) < ("s"."montoTotal" - "s"."discountamount")');

        $sales = $query->all();

        return $sales;
    }

    public function actionByCustomer($idcustomer) {
        $customer = Customer::findOne($idcustomer);
        if (!$customer) {
            return parent::sendResponse([
                    'message' => "Customer with ID $idcustomer not found.",
                    'statusCode' => 404
            ]);
        }

        $query = Sale::find()
                ->alias('s')
                ->select(['s.*', 'totalPagos' => 'COALESCE(SUM(rs.monto), 0)'])
                ->leftJoin('receiptSale rs', 's.id = rs.idsale')
                ->where(['s.idtypeCharge' => 2, 's.idcustomer' => $idcustomer])
                ->orderBy(['dateCreate' => SORT_ASC])
                ->groupBy(['s.id', 's.montoTotal'])
                ->having('COALESCE(SUM(rs.monto), 0) < ("s"."montoTotal" - "s"."discountamount")');

        $sales = $query->all();

        return $sales;
    }

    public function actionRemove($id) {
        return parent::sendResponse([
            'statusCode' => 400,
            'message' => "Por el momento no es posible anular un recibo!",
        ]);
    }

    public function actionByCustomerData($idcustomer) {
        $customer = Customer::findOne($idcustomer);
        if (!$customer) {
            return parent::sendResponse([
                        'message' => "Customer with ID $id not found.",
                        'statusCode' => 404
            ]);
        }

        $query = Receipt::find()
                ->where(['idcustomer' => $idcustomer])
                ->orderBy(['dateCreate' => SORT_DESC]);

        $receipt = $query->all();

        return $receipt;
    }

    // obtener por idreceipt
    public function actionById($id) {
        $receipt = Receipt::findOne($id);

        if (!$receipt) {
            return parent::sendResponse([
                        'message' => "Receipt with ID $id not found.",
                        'statusCode' => 404
            ]);
        }

        return $receipt;
    }
}
