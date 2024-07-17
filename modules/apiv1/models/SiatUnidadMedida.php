<?php

namespace app\modules\apiv1\models;

class SiatUnidadMedida extends \app\models\SiatUnidadMedida {

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