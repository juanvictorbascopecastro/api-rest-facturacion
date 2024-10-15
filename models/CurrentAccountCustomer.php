<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currentAccountCustomer".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property float|null $debit
 * @property float|null $credit
 * @property int|null $idsale
 * @property int|null $idreceipt
 * @property string|null $comment
 * @property int|null $idcustomer
 */
class CurrentAccountCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currentAccountCustomer';
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
            [['recycleBin'], 'boolean'],
            [['iduser', 'idsale', 'idreceipt', 'idcustomer'], 'default', 'value' => null],
            [['iduser', 'idsale', 'idreceipt', 'idcustomer'], 'integer'],
            [['debit', 'credit'], 'number'],
            [['comment'], 'string'],
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
            'iduser' => 'Iduser',
            'debit' => 'Debit',
            'credit' => 'Credit',
            'idsale' => 'Idsale',
            'idreceipt' => 'Idreceipt',
            'comment' => 'Comment',
            'idcustomer' => 'Idcustomer',
        ];
    }
}
