<?php

namespace app\modules\apiv1\models;

class UnidadMedida extends \app\models\UnidadMedida {

    public function fields() {
        return [
            'id', 
            'dateCreate', 
            'recycleBin', 
            'iduser', 
            'descripcion', 
            'codigoClasificador'
        ];
    }

}