<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productOrder".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iddocument
 * @property int|null $idorder
 * @property int|null $idproduct
 * @property float|null $quantityinput
 * @property float|null $quantityoutput
 * @property float|null $cost
 * @property float|null $price
 * @property int $nprocess
 * @property int|null $iduser
 * @property string|null $comment
 * @property float|null $previousQuantityoutput
 */
class ProductOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productOrder';
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
            [['iddocument', 'idorder', 'idproduct', 'iduser'], 'default', 'value' => null],
            [['iddocument', 'idorder', 'idproduct', 'iduser'], 'integer'],
            [['quantityinput', 'quantityoutput', 'cost', 'price', 'previousQuantityoutput'], 'number'],
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
            'iddocument' => 'Iddocument',
            'idorder' => 'Idorder',
            'idproduct' => 'Idproduct',
            'quantityinput' => 'Quantityinput',
            'quantityoutput' => 'Quantityoutput',
            'cost' => 'Cost',
            'price' => 'Price',
            'iduser' => 'Iduser',
            'comment' => 'Comment',
            'previousQuantityoutput' => 'Previous Quantityoutput',
            'newQuantityoutput' => 'New Quantity Output', 
        ];
    }
    public function beforeSave($insert) {
        
        $this->iduser = Yii::$app->user->getId();

        return true;
    }
}
