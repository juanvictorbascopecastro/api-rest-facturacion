<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receiptType".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $type
 * @property int|null $action
 * @property string|null $description
 * @property int|null $iduser
 */
class ReceiptType extends \yii\db\ActiveRecord {

    public static $OPEN_MAIN_CASH = 1;
    public static $CLOSE_MAIN_CASH = 2;
    public static $OPEN_CASH = 3;
    public static $CLOSE_CASH = 4;
    public static $WITHDRAWAL_BANK_DEPOSIT = 5;
    public static $CASH_OPEN_CLOSE = 6;
    public static $SALE = 7;
    public static $SALE_ANNUL = 8;
    public static $EXPENSE = 9;
    public static $EXPENSE_ANNUL = 10;
    public static $MAIN_OUT = 11;
    public static $MAIN_IN = 12;
    public static $MISSING_AMOUNT_CASH = 13;
    public static $LEFT_OVER_AMOUNT_CASH = 14;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'receiptType';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('iooxs_io');
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['action', 'iduser'], 'default', 'value' => null],
            [['action', 'iduser'], 'integer'],
            [['description'], 'string'],
            [['type'], 'string', 'max' => 30],
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
            'type' => 'Type',
            'action' => 'Action',
            'description' => 'Description',
            'iduser' => 'Iduser',
        ];
    }
}
