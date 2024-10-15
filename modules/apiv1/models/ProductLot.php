<?php

namespace app\modules\apiv1\models;

class ProductLot extends Productstock
{
    public $product;

    public function fields()
    {
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
            'lot',
            'idproductstock',
            'delivery',
            'lotDateExp',
            'product' => function ($model) {
                return $model->viewProduct;
            },
        ];
    }

    public function getViewProduct()
    {
        return $this->hasOne(ViewProduct::class, ['id' => 'idproduct'])
            ->with(['productBranch', 'productStores', 'productImages']);

        // return $this->hasOne(ViewProduct::class, ['id' => 'idproduct'])
        //     ->with(['productBranch', 'productStores', 'productImages']);
    }
   

}

