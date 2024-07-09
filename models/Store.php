<?php

namespace app\models;

use Yii;

class Store extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.store';
    }

    public static function getDb()
    {
        return Yii::$app->iooxsBranch;;
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
