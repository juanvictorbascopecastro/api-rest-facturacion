<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receiptSale".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $idreceipt
 * @property int|null $idsale
 * @property float|null $monto
 */
class ReceiptSale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receiptSale';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'idreceipt', 'idsale'], 'integer'],
            [['monto'], 'number', 'max' => 999999999999.99, 'min' => 0], // 12,2
            [['idreceipt'], 'exist', 'skipOnError' => true, 'targetClass' => Receipt::class, 'targetAttribute' => ['idreceipt' => 'id']],
            [['idsale'], 'exist', 'skipOnError' => true, 'targetClass' => Sale::class, 'targetAttribute' => ['idsale' => 'id']],
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
            'idreceipt' => 'Receipt ID',
            'idsale' => 'Sale ID',
            'monto' => 'Amount',
        ];
    }

    public function getReceipt()
    {
        return $this->hasOne(Receipt::class, ['id' => 'idreceipt']);
    }

    public function getSale()
    {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }

    // se ejecuta antes de registrar
    public function beforeSave($insert) {
        $this->iduser = Yii::$app->user->getId();
        return true;
    }
}
