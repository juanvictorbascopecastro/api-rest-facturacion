<?php

namespace app\modules\apiv1\models;

class Invoice extends \app\models\Invoice {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',      
            'numeroFactura',
            'codigoAmbiente',
            'codigoPuntoVenta',
            'codigoSistema',
            'codigoSucursal',
            'codigoDocumentoSector',
            'tipoFacturaDocumento',
        ];
    }
}