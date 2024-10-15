<?php

namespace app\models;

use Yii;

class ProductBranch extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.productBranch';
    }

    public static function getDb()
    {
        return Yii::$app->iooxsBranch;;
    }

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'iduser', 'idstatus'], 'default', 'value' => null],
            [['id', 'iduser', 'idstatus'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'priceChange', 'controlInventory', 'enableSale'], 'boolean'],
            [['price', 'cost', 'stockMin', 'stockMax'], 'number'],
            [['id'], 'unique'],
        ];
    }

    
     public function getIdstore0()  
    {
        return $this->hasOne(Store::class, ['id' => 'idstore']);
    }

    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'Iduser',
            'idstatus' => 'Idstatus',
            'price' => 'Price',
            'priceChange' => 'Price Change',
            'cost' => 'Cost',
            'controlInventory' => 'Control Inventory',
            'enableSale' => 'Enable Sale',
            'stockMin' => 'Stock Min',
            'stockMax' => 'Stock Max',
        ];
    }

    public static function find()
    {
        return new ProductBranchQuery(get_called_class());
    }

    // antes de guardar poner el usuario por defecto
    public function beforeSave($insert) {
        if (!$this->iduser) {
            $this->iduser = Yii::$app->user->getId();
        }
        return true; 
    }    
}
