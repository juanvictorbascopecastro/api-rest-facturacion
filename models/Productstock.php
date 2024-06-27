<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productstock".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iddocument
 * @property int|null $idsale
 * @property int|null $idpurchase
 * @property int|null $idproduct
 * @property float|null $quantityinput
 * @property float|null $quantityoutput
 * @property float|null $cost
 * @property float|null $price
 * @property int $nprocess
 * @property int|null $iduser
 * @property string|null $comment
 * @property float|null $montoDescuento
 * @property int|null $idstore
 * @property int|null $idproductionOrder
 *
 * @property Purchase $idpurchase0
 * @property Sale $idsale0
 */
class Productstock extends \yii\db\ActiveRecord
{
    public static $customDb;

    public static function tableName()
    {
        return 'productstock';
    }

    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iddocument', 'idsale', 'idpurchase', 'idproduct', 'nprocess', 'iduser', 'idstore', 'idproductionOrder'], 'default', 'value' => null],
            [['iddocument', 'idsale', 'idpurchase', 'idproduct', 'nprocess', 'iduser', 'idstore', 'idproductionOrder'], 'integer'],
            [['quantityinput', 'quantityoutput', 'cost', 'price', 'montoDescuento'], 'number'],
            [['comment'], 'string'],
            [['idpurchase'], 'exist', 'skipOnError' => true, 'targetClass' => Purchase::class, 'targetAttribute' => ['idpurchase' => 'id']],
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
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iddocument' => 'Iddocument',
            'idsale' => 'Idsale',
            'idpurchase' => 'Idpurchase',
            'idproduct' => 'Idproduct',
            'quantityinput' => 'Quantityinput',
            'quantityoutput' => 'Quantityoutput',
            'cost' => 'Cost',
            'price' => 'Price',
            'nprocess' => 'Nprocess',
            'iduser' => 'Iduser',
            'comment' => 'Comment',
            'montoDescuento' => 'Monto Descuento',
            'idstore' => 'Idstore',
            'idproductionOrder' => 'Idproduction Order',
        ];
    }

    /**
     * Gets query for [[Idpurchase0]].
     *
     * @return \yii\db\ActiveQuery|PurchaseQuery
     */
    public function getIdpurchase0()
    {
        return $this->hasOne(Purchase::class, ['id' => 'idpurchase']);
    }

    /**
     * Gets query for [[Idsale0]].
     *
     * @return \yii\db\ActiveQuery|SaleQuery
     */
    public function getIdsale0()
    {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }

    /**
     * {@inheritdoc}
     * @return ProductstockQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductstockQuery(get_called_class());
    }

    
}
