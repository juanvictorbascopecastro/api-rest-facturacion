<?php

namespace app\models;

use app\models\SystemPoint;
use Yii;

class Sale extends \yii\db\ActiveRecord {

    public $WindowTitle = '';
    public $editable = true;
    public $statusPROCESADO = 40;
    public $statusANULADO = 80;
    public $idmark;
    public $modelInvoice;
    public $modalidad;
    public $activadoCodigoModalidad = false;
    public $dateBegin;
    public $dateEnd;
    public $numeroTarjetaBegin;
    public $numeroTarjetaEnd;
    public $productSearch;
    public $printTicket = false;
    public $fieldCafc = false;
    public $cafc = null;
    public $codigoExcepcion = 0;
    public $codigoExcepcion2 = 0;
    public $contingencia;
    public $idcontingencia;
    public $activatedSaleMetodoPago = false;
    public $metodosPago;
    public $isInvoice;
    public $nameCustomer;
    public $balanceCustomer;
    public $dataProducts = array();
    public $printDirectly = 0;
    public $masivaFactura = 0;
    //codigoDocumentoSector=02 inmueble
    public $periodoFacturado;
    //codigoDocumentoSector=06 turismo
    public $cantidadHuespedes;
    public $cantidadHabitaciones;
    public $cantidadMayores;
    public $cantidadMenores;
    public $invoiceSecond = false;
    public $numeroFactura2 = null;
    public $idproduct;
    public $product;
    public $date = 2;
    public $dateYear;
    public $dateMonth;
    public $onDELIVERY = false;
    public $deadlineDELIVERY;
    public $idcityDELIVERY;
    public $addressDELIVERY;
    public $phoneDELIVERY;
    public $noteDELIVERY;

    public static function tableName() {
        return 'sale';
    }

    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    public function getProductStocks() {
        return $this->hasMany(Productstock::class, ['idsale' => 'id']);
    }

    public function rules() {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin', 'invoice', 'delivery', 'delivered'], 'boolean'],
            [['idcustomer', 'idstatus', 'number', 'iddocument', 'iduser', 'idsystemPoint', 'codigoModalidad', 'codigoMetodoPago', 'codigoMoneda', 'idcash', 'idcardService', 'idcashDocument', 'codigoTipoDocumentoIdentidad', 'idorder', 'idpriceSheet', 'idtypeCharge', 'codigoDocumentoSector', 'idcustomer2', 'codigoTipoDocumentoIdentidad2'], 'default', 'value' => null],
            [['idcustomer', 'idstatus', 'number', 'iddocument', 'iduser', 'idsystemPoint', 'codigoModalidad', 'codigoMetodoPago', 'codigoMoneda', 'idcash', 'idcardService', 'idcashDocument', 'codigoTipoDocumentoIdentidad', 'idorder', 'idpriceSheet', 'idtypeCharge', 'codigoDocumentoSector', 'idcustomer2', 'codigoTipoDocumentoIdentidad2'], 'integer'],
            [['razonSocial', 'numeroDocumento', 'comment', 'numeroFactura', 'phone', 'email', 'numeroTarjeta', 'razonSocial2', 'numeroDocumento2', 'phone2', 'email2', 'waiter'], 'string'],
            [['discountpercentage', 'discountamount', 'montoTotal', 'subTotal', 'tipoCambio', 'montoMoneda', 'montoRecibido', 'montoCambio', 'montoGiftCard'], 'number'],
        ];
    }

    public function attributeLabels() {
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

    public static function find() {
        return new SaleQuery(get_called_class());
    }

    public function getIdsystemPoint0() {
        return $this->hasOne(SystemPoint::class, ['id' => 'idsystemPoint']);
    }

    public function beforeSave($insert) {

        if ($this->scenario == 'default') {
            $this->iduser = Yii::$app->user->getId();
            $this->setNumber();
        }

        return true; // Permitir que la acción continúe
    }

    public function setNumber() {
        $q = 'select max(number) from sale where "recycleBin"=false';
        $command = Yii::$app->iooxsBranch->createCommand($q);
        $number = $command->queryScalar();
        $number = $number == null ? 1 : $number + 1;

        $this->number = $number;
    }
}
