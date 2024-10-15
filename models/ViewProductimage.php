<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ViewProductImage extends ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.viewProductimage';
    }
    
    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    public function rules()
    {
        return [
            [['idproduct', 'name', 'imagepath', 'datecreated', 'recycleBin', 'order'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idproduct' => 'Product ID',
            'name' => 'Name',
            'imagepath' => 'Image Path',
            'datecreated' => 'Date Created',
            'recycleBin' => 'Recycle Bin',
            'order' => 'Order',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(ViewProduct::class, ['id' => 'idproduct']);
    }
}
