<?php

namespace app\models;

use Yii;

class Unit extends \yii\db\ActiveRecord
{
    public static $customDb;
    public static function tableName()
    {
        return 'unit';
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
            [['name'], 'string'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'enabledDecimal'], 'boolean'],
            [['iduser', 'idunitSiat', 'idstatus', 'order'], 'default', 'value' => null],
            [['iduser', 'idunitSiat', 'idstatus', 'order'], 'integer'],
            [['symbol'], 'string', 'max' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'symbol' => 'Symbol',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'enabledDecimal' => 'Enabled Decimal',
            'iduser' => 'Iduser',
            'idunitSiat' => 'Idunit Siat',
            'idstatus' => 'Idstatus',
            'order' => 'Order',
        ];
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['idunit' => 'id']);
    }

    public static function find()
    {
        return new UnitQuery(get_called_class());
    }
}
