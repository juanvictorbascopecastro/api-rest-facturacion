<?php

namespace app\modules\apiv1\models;
use app\modules\apiv1\models\Productstock;

class Sale extends \app\models\Sale
{
    public function fields()
    {
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
            'discountpercentage' => function ($model) {
                return floatval($model->discountpercentage);
            },
            'discountamount' => function ($model) {
                return floatval($model->discountamount);
            },
            'montoTotal' => function ($model) {
                return floatval($model->montoTotal);
            },
            'iduser',
            'numeroFactura',
            'subTotal' => function ($model) {
                return floatval($model->subTotal);
            },
            'idsystemPoint',
            'codigoModalidad',
            'invoice',
            'codigoMetodoPago',
            'codigoMoneda',
            'tipoCambio' => function ($model) {
                return floatval($model->tipoCambio);
            },
            'montoMoneda' => function ($model) {
                return floatval($model->montoMoneda);
            },
            'montoRecibido' => function ($model) {
                return floatval($model->montoRecibido);
            },
            'montoCambio' => function ($model) {
                return floatval($model->montoCambio);
            },
            'idcash',
            'phone',
            'email',
            'idcardService',
            'numeroTarjeta',
            'idcashDocument',
            'codigoTipoDocumentoIdentidad',
            'idorder',
            'montoGiftCard' => function ($model) {
                return floatval($model->montoGiftCard);
            },
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
            'productStocks'
        ];
    }

    public function getProductStocks() 
    {
        return $this->hasMany(Productstock::class, ['idsale' => 'id']);
    }
}