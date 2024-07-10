<?php

namespace app\modules\apiv1\models;

class SiatTipoDocumentoIdentidad extends \app\models\SiatTipoDocumentoIdentidad {

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