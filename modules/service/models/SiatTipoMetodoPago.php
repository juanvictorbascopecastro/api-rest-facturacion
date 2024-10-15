<?php

namespace app\modules\service\models;

class SiatTipoMetodoPago extends \app\models\SiatTipoMetodoPago {

    public function fields() {
        return [
            'id',
            // 'dateCreate',
            // 'recycleBin',
            // 'iduser',
            'descripcion',
            'codigoClasificador',
        ];
    }

}