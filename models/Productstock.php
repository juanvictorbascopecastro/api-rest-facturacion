<?php

namespace app\models;
use yii\data\ActiveDataProvider;

use app\models\Product; 

use Yii;

class Productstock extends \yii\db\ActiveRecord {

    public static $customDb;

    public static function tableName() {
        return 'productstock';
    }

    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    public static function setCustomDb($db) {
        self::$customDb = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iddocument', 'idsale', 'idpurchase', 'idproduct', 'nprocess', 'iduser', 'idstore', 'idproductionOrder', 'idproductstock'], 'default', 'value' => null],
            [['iddocument', 'idsale', 'idpurchase', 'idproduct', 'nprocess', 'iduser', 'idstore', 'idproductionOrder', 'idproductstock'], 'integer'],
            [['quantityinput', 'quantityoutput', 'cost', 'price', 'montoDescuento'], 'number'],
            [['comment'], 'string'],
            [['delivery'], 'boolean'],
            [['lot'], 'string'],
            [['lotDateExp'], 'date', 'format' => 'php:Y-m-d'],
            [['idpurchase'], 'exist', 'skipOnError' => true, 'targetClass' => Purchase::class, 'targetAttribute' => ['idpurchase' => 'id']],
            [['idsale'], 'exist', 'skipOnError' => true, 'targetClass' => Sale::class, 'targetAttribute' => ['idsale' => 'id']],
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
            'lot' => 'Lote',
            'idproductstock' => 'Id Product Stock',
            'delivery' => 'Delivery',
            'lotDateExp' => 'Lot Date Expiry',
        ];
    }

    public function getSale() {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }

    public function getPurchase() {
        return $this->hasOne(Purchase::class, ['id' => 'idpurchase']);
    }
    /**
     * Gets query for [[Idpurchase0]].
     *
     * @return \yii\db\ActiveQuery|PurchaseQuery
     */
    public function getIdpurchase0() {
        return $this->hasOne(Purchase::class, ['id' => 'idpurchase']);
    }

    /**
     * Gets query for [[Idsale0]].
     *
     * @return \yii\db\ActiveQuery|SaleQuery
     */
    public function getIdsale0() {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }
    
    public function getIdproduct0() {
        return $this->hasOne(Product::class, ['id' => 'idproduct']);
    }

    /**
     * {@inheritdoc}
     * @return ProductstockQuery the active query used by this AR class.
     */
    public static function find() {
        return new ProductstockQuery(get_called_class());
    }

    public function getDocument($idDocument) {
        $query = self::find()
                ->where(['iddocument' => $idDocument])
                ->andWhere(['nprocess' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        return $dataProvider;
    }

    public function getItemDocument()
    {
        return $this->hasOne(Document::class, ['id' => 'iddocument']);
    }
}
