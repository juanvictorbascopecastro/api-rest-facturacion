<?php

namespace app\models;

use Yii;

class CfgStore extends \yii\db\ActiveRecord
{
    public static $customDb;
    public static function tableName()
    {
        return 'cfg.store';
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
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser'], 'default', 'value' => null],
            [['iduser'], 'integer'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'Iduser',
            'name' => 'Name',
        ];
    }

    public static function find()
    {
        return new CfgStoreQuery(get_called_class());
    }
}
