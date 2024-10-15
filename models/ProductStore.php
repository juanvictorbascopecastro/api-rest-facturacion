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

    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'idstore']);
    }

    public static function find()
    {
        return new ProductStoreQuery(get_called_class());
    }

    // antes de guardar poner el usuario por defecto
    public function beforeSave($insert) {
        if (!$this->iduser) {
            $this->iduser = Yii::$app->user->getId();
        }
        return true; 
    }
}
