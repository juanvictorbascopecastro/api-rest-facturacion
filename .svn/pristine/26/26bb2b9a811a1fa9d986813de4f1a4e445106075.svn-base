<?php

namespace app\modules\apiv1\models;

class ProductStore extends \app\models\ProductStore {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'stock' => function ($model) {
                return floatval($model->stock);
            },
            'idstore',
            'stockReserved' => function($model) {
                return floatval($model->stockReserved);
            },
            'allow',
        ];
    }
}
