<?php

namespace app\modules\apiv1\models;

class Receipt extends \app\models\Receipt
{
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'idcustomer',
            'idstatus',
            'comment',
            'number',
            'codigoMetodoPago',
            'montoTotal' => function ($model) {
                return floatval($model->montoTotal);
            },
            'saleReceipt',
        ];
    }

    public function getSaleReceipt()
    {
        return $this->hasMany(ReceiptSaleDetails::class, ['idreceipt' => 'id']);
    }
}