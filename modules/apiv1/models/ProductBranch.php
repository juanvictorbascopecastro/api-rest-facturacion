<?php

namespace app\modules\apiv1\models;

class ProductBranch extends \app\models\ProductBranch {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'idstatus',
            'price' => function ($model) {
                return floatval($model->price);
            },
            'priceChange',
            'cost' => function($model) {
                return floatval($model->cost);
            },
            'controlInventory',
            'enableSale',
            'stockMin' => function($model) {
                return floatval($model->stockMin);
            },
            'stockMax' => function($model) {
                return floatval($model->stockMax);
            },
        ];
    }
}
