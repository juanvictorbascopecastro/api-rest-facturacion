<?php

namespace app\modules\apiv1\models;

class SiatSiatTipoDocumentoIdentidad extends \app\models\SiatSiatTipoDocumentoIdentidad {

    public function fields() {
        return [
           'id' ,
            'dateCreate',
            'recycleBin',
            'iduser',
            'descripcion',
            'codigoClasificador',
            'simbolo',
            'commandVerified',
            'codigoExcepcion',
        ];
    }

}