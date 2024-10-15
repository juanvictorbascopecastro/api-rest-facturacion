<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orderSale".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int $idsale
 * @property int $idorder
 * @property int|null $idcustomer
 * @property int|null $iduser
 *
 * @property Order $order
 * @property Sale $sale
 */
class OrderSale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orderSale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['idsale', 'idorder'], 'required'],
            [['idsale', 'idorder', 'idcustomer', 'iduser'], 'integer'],
            [['idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['idorder' => 'id']],
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
            'idsale' => 'Sale ID',
            'idorder' => 'Order ID',
            'idcustomer' => 'Customer ID',
            'iduser' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'idorder']);
    }

    /**
     * Gets query for [[Sale]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSale()
    {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }
}
