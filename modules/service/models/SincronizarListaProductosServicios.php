<?php

namespace app\modules\service\models;

class SincronizarListaProductosServicios extends \app\models\SincronizarListaProductosServicios {

    public function fields() {
        return [
            'id',
            // 'dateCreate',
            // 'recycleBin',
            'codigoActividad',
            'codigoProducto',
            'descripcionProducto',
            // 'iduser',
        ];
    }

}