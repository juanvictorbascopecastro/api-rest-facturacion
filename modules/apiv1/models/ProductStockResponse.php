<?php

namespace app\modules\apiv1\models;

class ProductStockResponse extends Productstock
{
    public $sale; 
    public $purchase;

    public function __construct($productStock, $config = [])
    {
        parent::__construct($config);
        $this->setAttributes($productStock->attributes);
        if ($productStock->id !== null) {
            $this->id = $productStock->id;
        }
        $this->sale = $productStock->getSale()->one() ? $productStock->getSale()->one()->attributes : null;
        $this->purchase = $productStock->getPurchase()->one() ? $productStock->getPurchase()->one()->attributes : null;
    }

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
            'sale',
            'purchase',
        ];
    }
}
