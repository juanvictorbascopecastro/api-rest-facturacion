<?php

namespace app\modules\apiv1\models;

class ViewProduct extends \app\models\ViewProduct {

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
            'idmark', 
            'activePrinciple', 
            'rs',
            'productImages',            
            'productStores',
            'productBranch',
        ];
    }
}
