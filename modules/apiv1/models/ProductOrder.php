<?php

namespace app\modules\apiv1\models;

class ProductOrder extends \app\models\ProductOrder
{
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iddocument',
            'idorder',
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
            'iduser',
            'comment',
            'previousQuantityoutput' => function ($model) {
                return floatval($model->previousQuantityoutput);
            },
            'newQuantityoutput'
        ];
    }
}