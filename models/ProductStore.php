<?php

namespace app\models;

use Yii;

class ProductStore extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.productStore';
    }

    public static function getDb()
    {
        return Yii::$app->iooxsBranch;;
    }

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
     * @return CfgProductStoreQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductStoreQuery(get_called_class());
    }
}
