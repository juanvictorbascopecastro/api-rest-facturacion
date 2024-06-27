<?php

namespace app\modules\apiv1\models;

use app\models\Productstock;

class Sale extends \app\models\Sale {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'idcustomer',
            'razonSocial',
            'numeroDocumento',
            'idstatus',
            'comment',
            'number',
            'iddocument',
            'discountpercentage',
            'discountamount',
            'montoTotal',
            'iduser',
            'numeroFactura',
            'subTotal',
            'idsystemPoint',
            'codigoModalidad',
            'invoice',
            'codigoMetodoPago',
            'codigoMoneda',
            'tipoCambio',
            'montoMoneda',
            'montoRecibido',
            'montoCambio',
            'idcash',
            'phone',
            'email',
            'idcardService',
            'numeroTarjeta',
            'idcashDocument',
            'codigoTipoDocumentoIdentidad',
            'idorder',
            'montoGiftCard',
            'idpriceSheet',
            'idtypeCharge',
            'codigoDocumentoSector',
            'idcustomer2',
            'razonSocial2',
            'numeroDocumento2',
            'phone2',
            'email2',
            'codigoTipoDocumentoIdentidad2',
            'waiter',
            'delivery',
            'delivered',
            'productStocks' // este parametro esta declarado en su modelo base, lo hereda para usarlo
        ];
    }
}