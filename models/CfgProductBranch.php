<?php

namespace app\models;

use Yii;

class CfgProductBranch extends \yii\db\ActiveRecord
{
    public static $customDb;
    public static function tableName()
    {
        return 'cfg.productBranch';
    }

    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
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
        return new CfgProductBranchQuery(get_called_class());
    }
}
