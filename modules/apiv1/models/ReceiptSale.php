<?php

namespace app\modules\apiv1\models;

class ReceiptSale extends \app\models\ReceiptSale
{
    public function fields()
    {
        return [
            'id' ,
            'dateCreate',
            'recycleBin',
            'iduser',
            'idreceipt',
            'idsale',
            'monto' => function ($model) {
                return floatval($model->monto);
            },
        ];
    }
}