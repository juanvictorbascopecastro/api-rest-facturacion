<?php

namespace app\models;

use Yii;

class Sale extends \yii\db\ActiveRecord
{
    public static $customDb;
    
    public static function tableName()
    {
        return 'sale';
    }

    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }

    public function getProductStocks() // Cambiado a 'productStocks' para coincidir con el mensaje de error
    {
        return $this->hasMany(Productstock::class, ['idsale' => 'id']);
    }

    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin', 'invoice', 'delivery', 'delivered'], 'boolean'],
            [['idcustomer', 'idstatus', 'number', 'iddocument', 'iduser', 'idsystemPoint', 'codigoModalidad', 'codigoMetodoPago', 'codigoMoneda', 'idcash', 'idcardService', 'idcashDocument', 'codigoTipoDocumentoIdentidad', 'idorder', 'idpriceSheet', 'idtypeCharge', 'codigoDocumentoSector', 'idcustomer2', 'codigoTipoDocumentoIdentidad2'], 'default', 'value' => null],
            [['idcustomer', 'idstatus', 'number', 'iddocument', 'iduser', 'idsystemPoint', 'codigoModalidad', 'codigoMetodoPago', 'codigoMoneda', 'idcash', 'idcardService', 'idcashDocument', 'codigoTipoDocumentoIdentidad', 'idorder', 'idpriceSheet', 'idtypeCharge', 'codigoDocumentoSector', 'idcustomer2', 'codigoTipoDocumentoIdentidad2'], 'integer'],
            [['razonSocial', 'numeroDocumento', 'comment', 'numeroFactura', 'phone', 'email', 'numeroTarjeta', 'razonSocial2', 'numeroDocumento2', 'phone2', 'email2', 'waiter'], 'string'],
            [['discountpercentage', 'discountamount', 'montoTotal', 'subTotal', 'tipoCambio', 'montoMoneda', 'montoRecibido', 'montoCambio', 'montoGiftCard'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'idcustomer' => 'Idcustomer',
            'razonSocial' => 'Razon Social',
            'numeroDocumento' => 'Numero Documento',
            'idstatus' => 'Idstatus',
            'comment' => 'Comment',
            'number' => 'Number',
            'iddocument' => 'Iddocument',
            'discountpercentage' => 'Discountpercentage',
            'discountamount' => 'Discountamount',
            'montoTotal' => 'Monto Total',
            'iduser' => 'Iduser',
            'numeroFactura' => 'Numero Factura',
            'subTotal' => 'Sub Total',
            'idsystemPoint' => 'Idsystem Point',
            'codigoModalidad' => 'Codigo Modalidad',
            'invoice' => 'Invoice',
            'codigoMetodoPago' => 'Codigo Metodo Pago',
            'codigoMoneda' => 'Codigo Moneda',
            'tipoCambio' => 'Tipo Cambio',
            'montoMoneda' => 'Monto Moneda',
            'montoRecibido' => 'Monto Recibido',
            'montoCambio' => 'Monto Cambio',
            'idcash' => 'Idcash',
            'phone' => 'Phone',
            'email' => 'Email',
            'idcardService' => 'Idcard Service',
            'numeroTarjeta' => 'Numero Tarjeta',
            'idcashDocument' => 'Idcash Document',
            'codigoTipoDocumentoIdentidad' => 'Codigo Tipo Documento Identidad',
            'idorder' => 'Idorder',
            'montoGiftCard' => 'Monto Gift Card',
            'idpriceSheet' => 'Idprice Sheet',
            'idtypeCharge' => 'Idtype Charge',
            'codigoDocumentoSector' => 'Codigo Documento Sector',
            'idcustomer2' => 'Idcustomer2',
            'razonSocial2' => 'Razon Social2',
            'numeroDocumento2' => 'Numero Documento2',
            'phone2' => 'Phone2',
            'email2' => 'Email2',
            'codigoTipoDocumentoIdentidad2' => 'Codigo Tipo Documento Identidad2',
            'waiter' => 'Waiter',
            'delivery' => 'Delivery',
            'delivered' => 'Delivered',
        ];
    }

    public static function find()
    {
        return new SaleQuery(get_called_class());
    }
}
