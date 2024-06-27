<?php

namespace app\modules\apiv1\models;

class Product extends \app\models\Product {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iddocument',
            'idsale',
            'idpurchase',
            'idproduct',
            'quantityinput',
            'quantityoutput',
            'cost',
            'price',
            'nprocess',
            'iduser',
            'comment',
            'montoDescuento',
            'idstore',
            'idproductionOrder',
        ];
    }
}