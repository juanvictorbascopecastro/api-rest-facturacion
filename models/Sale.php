<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sale".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $idcustomer
 * @property string|null $razonSocial
 * @property string|null $numeroDocumento
 * @property int|null $idstatus
 * @property string|null $comment
 * @property int|null $number
 * @property int|null $iddocument
 * @property float|null $discountpercentage
 * @property float|null $discountamount
 * @property float|null $montoTotal
 * @property int|null $iduser
 * @property string|null $numeroFactura
 * @property float|null $subTotal
 * @property int|null $idsystemPoint
 * @property int|null $codigoModalidad
 * @property bool|null $invoice
 * @property int|null $codigoMetodoPago
 * @property int|null $codigoMoneda
 * @property float|null $tipoCambio
 * @property float|null $montoMoneda
 * @property float|null $montoRecibido
 * @property float|null $montoCambio
 * @property int|null $idcash
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $idcardService
 * @property string|null $numeroTarjeta
 * @property int|null $idcashDocument
 * @property int|null $codigoTipoDocumentoIdentidad
 * @property int|null $idorder
 * @property float|null $montoGiftCard
 * @property int|null $idpriceSheet
 * @property int|null $idtypeCharge
 * @property int|null $codigoDocumentoSector
 * @property int|null $idcustomer2
 * @property string|null $razonSocial2
 * @property string|null $numeroDocumento2
 * @property string|null $phone2
 * @property string|null $email2
 * @property int|null $codigoTipoDocumentoIdentidad2
 * @property string|null $waiter
 * @property bool|null $delivery
 * @property bool|null $delivered
 *
 * @property Productstock[] $productstocks
 */
class Sale extends \yii\db\ActiveRecord
{
    private static $customDb;

    public static function tableName()
    {
        return 'sale';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Gets query for [[Productstocks]].
     *
     * @return \yii\db\ActiveQuery|ProductstockQuery
     */
    public function getProductstocks()
    {
        return $this->hasMany(Productstock::class, ['idsale' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return SaleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SaleQuery(get_called_class());
    }

    // public static function getDb()
    // {
    //     return Yii::$app->get('empresa8_sb0');
    // }
    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
}
