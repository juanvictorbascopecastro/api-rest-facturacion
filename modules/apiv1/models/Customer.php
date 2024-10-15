<?php

namespace app\modules\apiv1\models;

class Customer extends \app\models\Customer {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'name',
            'numeroDocumento',
            'phone',
            'numberPhone2',
            'iduser',
            'iddocumentNumberType',
            'type',
            'idcustomer',
            'email',
            'idjobPosition',
            'code',
            'idcity',
            'address',
            'codigoTipoDocumentoIdentidad',
            'complemento',
            'razonSocial',
            'allowedCredit',
            'note',
        ];
    }

}