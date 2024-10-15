<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $idcustomer
 * @property string|null $nameCustomer
 * @property int|null $codigoTipoDocumentoIdentidad
 * @property string|null $numeroDocumento
 * @property int|null $idstatus
 * @property int|null $idpriceSheet
 * @property int|null $number
 * @property float|null $discountpercentage
 * @property float|null $discountamount
 * @property float $montoTotal
 * @property float $subTotal
 * @property int|null $codigoMoneda
 * @property string|null $comment
 * @property int|null $idtable
 * @property bool|null $sendPrint
 * @property int|null $daysLimit
 * @property string|null $phone
 * @property string|null $email
 * @property bool|null $printBill
 *
 * @property Table $idtable0
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin', 'sendPrint', 'printBill'], 'boolean'],
            [['iduser', 'idcustomer', 'codigoTipoDocumentoIdentidad', 'idstatus', 'idpriceSheet', 'number', 'codigoMoneda', 'idtable', 'daysLimit'], 'integer'],
            [['nameCustomer', 'comment', 'phone', 'email'], 'string'],
            [['discountpercentage', 'discountamount', 'montoTotal', 'subTotal'], 'number'],
            [['numeroDocumento'], 'string', 'max' => 30],
            [['idtable'], 'exist', 'skipOnError' => true, 'targetClass' => Table::class, 'targetAttribute' => ['idtable' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Created',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'User ID',
            'idcustomer' => 'Customer ID',
            'nameCustomer' => 'Customer Name',
            'codigoTipoDocumentoIdentidad' => 'Document Type Code',
            'numeroDocumento' => 'Document Number',
            'idstatus' => 'Status ID',
            'idpriceSheet' => 'Price Sheet ID',
            'number' => 'Order Number',
            'discountpercentage' => 'Discount Percentage',
            'discountamount' => 'Discount Amount',
            'montoTotal' => 'Total Amount',
            'subTotal' => 'Subtotal',
            'codigoMoneda' => 'Currency Code',
            'comment' => 'comment',
            'idtable' => 'Table ID',
            'sendPrint' => 'Send to Printer',
            'daysLimit' => 'Days Limit',
            'phone' => 'Phone',
            'email' => 'Email',
            'printBill' => 'Print Bill',
        ];
    }

    /**
     * Gets query for [[Idtable0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdtable0()
    {
        return $this->hasOne(Table::class, ['id' => 'idtable']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->iduser = Yii::$app->user->getId();
        }

        return parent::beforeSave($insert);
    }
}
