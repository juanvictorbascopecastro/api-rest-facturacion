<?php

namespace app\modules\service\models;

class SincronizarActividades extends \app\models\SincronizarActividades {

    public function fields() {
        return [
            'id',
            // 'dateCreate',
            // 'recycleBin',
            // 'iduser',
            'actived',
            'descripcion',
            'tipoActividad',
            'codigoCaeb',
        ];
    }

}