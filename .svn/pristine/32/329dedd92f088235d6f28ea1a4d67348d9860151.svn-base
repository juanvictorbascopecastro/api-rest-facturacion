<?php

namespace app\modules\service\models;

class Productstock extends \app\models\Productstock {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iddocument',
            'idsale',
            'idpurchase',
            'idproduct',
            'quantityinput' => function ($model) {
                return floatval($model->quantityinput);
            },
            'quantityoutput' => function ($model) {
                return floatval($model->quantityoutput);
            },
            'cost' => function ($model) {
                return floatval($model->cost);
            },
            'price' => function ($model) {
                return floatval($model->price);
            },
            'nprocess',
            'iduser',
            'comment',
            'montoDescuento' => function ($model) {
                return floatval($model->montoDescuento);
            },
            'idstore',
            'idproductionOrder',
        ];
    }
}