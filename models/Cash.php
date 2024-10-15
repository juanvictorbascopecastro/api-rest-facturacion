<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cash".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $number
 * @property string|null $dateOpen
 * @property string|null $dateClose
 * @property string|null $idreceiptType
 * @property string|null $observation
 * @property float|null $input
 * @property float|null $output
 * @property int|null $idcashDocumentInput
 * @property int|null $idcashDocumentOutput
 * @property int|null $idstatus
 * @property float|null $cashAmount
 * @property float|null $totalCashSale
 * @property float|null $leftOverAmount
 * @property int|null $idcashDocumentDifference
 * @property float|null $cashAmountAvailable
 * @property float|null $totalCashExpense
 * @property int|null $idcashDocumentInputMAIN
 * @property int|null $idcashDocumentOutputMAIN
 * @property int|null $idcashDocumentInputSale
 * @property int|null $idcashDocument
 * @property float|null $totalSale
 * @property float|null $amountReturn
 *
 * @property CashDocument $idcashDocumentInput0
 * @property CashDocument $idcashDocumentOutput0
 * @property Sale[] $sales
 */
class Cash extends \yii\db\ActiveRecord {

    const STATUS_INICIAR = 1;
    const STATUS_ABIERTO = 3;
    const STATUS_CERRADO = 55;
    const STATUS_ANULADO = 80;

     public $user;
     
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cash';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['dateCreate', 'dateOpen', 'dateClose'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'number', 'idcashDocumentInput', 'idcashDocumentOutput', 'idstatus', 'idcashDocumentDifference', 'idcashDocumentInputMAIN', 'idcashDocumentOutputMAIN', 'idcashDocumentInputSale', 'idcashDocument'], 'default', 'value' => null],
            [['iduser', 'number', 'idcashDocumentInput', 'idcashDocumentOutput', 'idstatus', 'idcashDocumentDifference', 'idcashDocumentInputMAIN', 'idcashDocumentOutputMAIN', 'idcashDocumentInputSale', 'idcashDocument'], 'integer'],
            [['observation'], 'string'],
            [['input', 'output', 'cashAmount', 'totalCashSale', 'leftOverAmount', 'cashAmountAvailable', 'totalCashExpense', 'totalSale', 'amountReturn'], 'number'],
            [['idreceiptType'], 'string', 'max' => 30],
            [['idcashDocumentInput'], 'exist', 'skipOnError' => true, 'targetClass' => CashDocument::class, 'targetAttribute' => ['idcashDocumentInput' => 'id']],
            [['idcashDocumentOutput'], 'exist', 'skipOnError' => true, 'targetClass' => CashDocument::class, 'targetAttribute' => ['idcashDocumentOutput' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'Iduser',
            'number' => 'Number',
            'dateOpen' => 'Date Open',
            'dateClose' => 'Date Close',
            'idreceiptType' => 'Idreceipt Type',
            'observation' => 'Observation',
            'input' => 'Input',
            'output' => 'Output',
            'idcashDocumentInput' => 'Idcash Document Input',
            'idcashDocumentOutput' => 'Idcash Document Output',
            'idstatus' => 'Idstatus',
            'cashAmount' => 'Cash Amount',
            'totalCashSale' => 'Total Cash Sale',
            'leftOverAmount' => 'Left Over Amount',
            'idcashDocumentDifference' => 'Idcash Document Difference',
            'cashAmountAvailable' => 'Cash Amount Available',
            'totalCashExpense' => 'Total Cash Expense',
            'idcashDocumentInputMAIN' => 'Idcash Document Input Main',
            'idcashDocumentOutputMAIN' => 'Idcash Document Output Main',
            'idcashDocumentInputSale' => 'Idcash Document Input Sale',
            'idcashDocument' => 'Idcash Document',
            'totalSale' => 'Total Sale',
            'amountReturn' => 'Amount Return',
        ];
    }

    /**
     * Gets query for [[IdcashDocumentInput0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdcashDocumentInput0() {
        return $this->hasOne(CashDocument::class, ['id' => 'idcashDocumentInput']);
    }

    /**
     * Gets query for [[IdcashDocumentOutput0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdcashDocumentOutput0() {
        return $this->hasOne(CashDocument::class, ['id' => 'idcashDocumentOutput']);
    }

    /**
     * Gets query for [[Sales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSales() {
        return $this->hasMany(Sale::class, ['idcash' => 'id']);
    }
}
