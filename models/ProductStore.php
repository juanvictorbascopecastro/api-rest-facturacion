<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cfg.productStore".
 *
 * @property int $id
 * @property string $dateCreate
 * @property bool $recycleBin
 * @property int|null $iduser
 * @property float|null $stock
 * @property int $idstore
 * @property float|null $stockReserved
 * @property bool|null $allow
 */
class ProductStore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.productStore';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('empresa0_api');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idstore'], 'required'],
            [['id', 'iduser', 'idstore'], 'default', 'value' => null],
            [['id', 'iduser', 'idstore'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'allow'], 'boolean'],
            [['stock', 'stockReserved'], 'number'],
            [['id', 'idstore'], 'unique', 'targetAttribute' => ['id', 'idstore']],
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
            'stock' => 'Stock',
            'idstore' => 'Idstore',
            'stockReserved' => 'Stock Reserved',
            'allow' => 'Allow',
        ];
    }

    /**
     * {@inheritdoc}
     * @return ProductStoreQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductStoreQuery(get_called_class());
    }
}
