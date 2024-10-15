<?php

namespace app\modules\apiv1\models;

class Vendor extends \app\models\Vendor {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'name',
            'numeroDocumento',
            'numberPhone',
            'numberPhone2',
            'iduser',
            'iddocumentNumberType',
        ];
    }
}