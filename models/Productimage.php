<?php

namespace app\models;

use Yii;

class Productimage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'productimage';
    }

    public static function getDb()
    {
        return Yii::$app->iooxsRoot;
    }

    public function rules()
    {
        return [
            [['idproduct', 'imagepath'], 'required'],
            [['idproduct', 'order'], 'default', 'value' => null],
            [['idproduct', 'order'], 'integer'],
            [['imagepath'], 'string'],
            [['datecreated'], 'safe'],
            [['recyclebin'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['idproduct'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['idproduct' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idproduct' => 'Idproduct',
            'name' => 'Name',
            'imagepath' => 'Imagepath',
            'datecreated' => 'Datecreated',
            'recyclebin' => 'Recyclebin',
            'order' => 'Order',
        ];
    }

    public function getIdproduct0()
    {
        return $this->hasOne(Product::class, ['id' => 'idproduct']);
    }

    public static function find()
    {
        return new ProductimageQuery(get_called_class());
    }
}
