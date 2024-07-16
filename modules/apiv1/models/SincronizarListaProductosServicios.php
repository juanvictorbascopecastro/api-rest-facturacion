<?php

namespace app\modules\apiv1\models;

class SincronizarListaProductosServicios extends \app\models\SincronizarListaProductosServicios {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'codigoActividad',
            'codigoProducto',
            'descripcionProducto',
            'iduser'
        ];
    }

}