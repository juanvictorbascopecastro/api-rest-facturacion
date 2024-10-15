<?php

namespace app\modules\apiv1\models;

class MetodoPago extends \app\models\MetodoPago {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'descripcion',
            'activedSiat',
            'cardService',
            'actived',
        ];
    }

}