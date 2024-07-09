<?php

namespace app\models;

use Yii;


// para los tipos de movimientos, compras ventas, etc
class DocumentType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'documentType';
    }

    public static function getDb()
    {
        return Yii::$app->get('iooxs_io');
    }

    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['type', 'description'], 'string'],
            [['action', 'iduser'], 'default', 'value' => null],
            [['action', 'iduser'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'type' => 'Type',
            'action' => 'Action',
            'description' => 'Description',
            'iduser' => 'Iduser',
        ];
    }

    public static function find()
    {
        return new DocumentTypeQuery(get_called_class());
    }
}
