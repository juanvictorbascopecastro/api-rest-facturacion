<?php

namespace app\modules\apiv1\models;
use app\modules\apiv1\models\Productstock;

class Purchase extends \app\models\Purchase
{
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'idvendor',
            'nameVendor',
            'numeroDocumento',
            'idstatus',
            'comment',
            'number',
            'iddocument',
            'discountpercentage' => function ($model) {
                return floatval($model->discountpercentage);
            },
            'discountamount' => function ($model) {
                return floatval($model->discountamount);
            },
            'montoTotal' => function ($model) {
                return floatval($model->montoTotal);
            },
            'iduser',
            'subTotal' => function ($model) {
                return floatval($model->subTotal);
            },
            'idinvoice',
            'numeroFactura',
            'attachedDocument',
            'broadcastDateDocument',
            'cuf',
            'idstore',
            'productStocks',
        ];
    }

    public function getProductStocks() 
    {
        return $this->hasMany(Productstock::class, ['idpurchase' => 'id']);
    }
}