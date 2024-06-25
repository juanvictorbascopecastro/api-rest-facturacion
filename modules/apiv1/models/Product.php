<?php

namespace app\modules\apiv1\models;

class Product extends \app\models\Product {

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
            'price',
            'nameSource',
            'codeSource',
        ];
    }

}