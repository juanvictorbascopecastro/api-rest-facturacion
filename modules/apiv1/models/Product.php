<?php

namespace app\modules\apiv1\models;

class Product extends \app\models\Product {
    public $productStores = []; // definimos un nuevo parametro para el modelo donde estara el stock del producto
    public $productBranch = null; // parametro donde esta las configuraciones del producto

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'name',
            'tags',
            'code',
            'barcode',
            'idunit',
            'idcategory',
            'stockcontrol',
            'dimensionwidth',
            'dimensionlength',
            'dimensionheight',
            'codeRef',
            'weight',
            'nameRef',
            'idsincronizarListaProductosServicios',
            'idstatus',
            'iduser',
            'description',
            'typeBudget',
            'price' => function ($model) {
                return floatval($model->price);
            },
            'nameSource',
            'codeSource',
            'productStores' => function () {
                return $this->productStores;
            },
            'productBranch' => function () {
                return $this->productBranch;
            },
            'productImages'
        ];
    }
}
