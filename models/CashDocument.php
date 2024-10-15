<?php

namespace app\models;

use Yii;
use app\models\ReceiptType;

/**
 * This is the model class for table "cashDocument".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $number
 * @property int $idreceiptType
 * @property string|null $observation
 * @property float|null $input
 * @property float|null $output
 * @property int|null $idsale
 * @property int|null $idcash
 * @property int|null $idstatus
 * @property int|null $codigoMetodoPago
 * @property int|null $idcardService
 * @property int|null $idexpense
 * @property int|null $mainCash 1: main

  0: cash  daily
 * @property int|null $idcashPetty
 * @property string|null $waiter
 *
 * @property Cash[] $cashes
 * @property Cash[] $cashes0
 */
class CashDocument extends \yii\db\ActiveRecord {

    public $statusPROCESADO = 40;
    public $statusANULADO = 80;
    public $amount;
    public $withCash = 0;
    public $user;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cashDocument';
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
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'number', 'idreceiptType', 'idsale', 'idcash', 'idstatus', 'codigoMetodoPago', 'idcardService', 'idexpense', 'mainCash', 'idcashPetty'], 'default', 'value' => null],
            [['iduser', 'number', 'idreceiptType', 'idsale', 'idcash', 'idstatus', 'codigoMetodoPago', 'idcardService', 'idexpense', 'mainCash', 'idcashPetty'], 'integer'],
            [['idreceiptType'], 'required'],
            [['observation', 'waiter'], 'string'],
            [['input', 'output'], 'number'],
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
            'idreceiptType' => 'Idreceipt Type',
            'observation' => 'Observation',
            'input' => 'Input',
            'output' => 'Output',
            'idsale' => 'Idsale',
            'idcash' => 'Idcash',
            'idstatus' => 'Idstatus',
            'codigoMetodoPago' => 'Codigo Metodo Pago',
            'idcardService' => 'Idcard Service',
            'idexpense' => 'Idexpense',
            'mainCash' => 'Main Cash',
            'idcashPetty' => 'Idcash Petty',
            'waiter' => 'Waiter',
        ];
    }

    /**
     * Gets query for [[Cashes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashes() {
        return $this->hasMany(Cash::class, ['idcashDocumentInput' => 'id']);
    }

    /**
     * Gets query for [[Cashes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashes0() {
        return $this->hasMany(Cash::class, ['idcashDocumentOutput' => 'id']);
    }

    public function getIdreceiptType0() {
        return $this->hasOne(ReceiptType::class, ['id' => 'idreceiptType']);
    }

    public function beforeSave($insert) {

        if ($this->scenario == 'default') {
            $this->iduser = Yii::$app->user->getId();
            $this->observation = strtoupper($this->observation);
            $this->idstatus = $this->statusPROCESADO;
            $this->setNumber();
        }


        if ($this->idreceiptType0->action == 1) {
            $this->input = $this->amount;
            $this->output = null;
        } elseif ($this->idreceiptType0->action == -1) {

            $this->output = $this->amount;
            $this->input = null;
        }


        return true;
    }

    public function setNumber() {
        $q = 'select max(number) from "cashDocument" where "recycleBin"=false';
        $command = Yii::$app->iooxsBranch->createCommand($q);
        $number = $command->queryScalar();

        $number = $number == null ? 1 : $number + 1;

        $this->number = $number;
    }

    public function saveCash($modelCashOpen, $modelSale) {
        $this->idreceiptType = ReceiptType::$SALE;
        $this->idcash = $modelCashOpen->id;
        $this->observation = 'VENTA N°:' . $modelSale->number;
        $this->amount = $modelSale->montoTotal;

        $this->idstatus = $this->statusPROCESADO;
        $this->codigoMetodoPago = $modelSale->codigoMetodoPago;
        $this->montoEfectivo = $modelSale->montoEfectivo;
        $this->idcardService = $modelSale->idcardService;
        $this->idsale = $modelSale->id;
        $this->waiter = $modelSale->waiter;
        if (!$this->save()) {
            return ['error' => true, 'message' => $this->getErrors()];
        } else {
            return ['error' => false];
        }
    }
}
