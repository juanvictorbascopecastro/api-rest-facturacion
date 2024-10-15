<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ViewProduct extends ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.viewProduct';
    }

    public function rules()
    {
        return [
            [
                [
                    'dateCreate', 
                    'recycleBin', 
                    'name', 
                    'tags', 
                    'code', 
                    'barcode', 
                    'idunit', 
                    'idcategory', 
                    'stockcontrol', 
                    'dimensionwidth', 
                    'dimensionlength', 
                    'dimensionheight', 
                    'codeRef', 
                    'weight', 
                    'nameRef', 
                    'idsincronizarListaProductosServicios', 
                    'idstatus', 
                    'iduser', 
                    'description', 
                    'typeBudget', 
                    'price', 
                    'codeSource', 
                    'nameSource', 
                    'idmark', 
                    'activePrinciple', 
                    'rs'
                ], 
                'safe'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            // Otros atributos...
        ];
    }

    public function getProductImages()
    {
        return $this->hasMany(Productimage::class, ['idproduct' => 'id']);
    }

    public function getProductStores()
    {
        return $this->hasMany(ProductStore::class, ['id' => 'id']);
    }

    public function getProductBranch()
    {
        return $this->hasMany(ProductBranch::class, ['id' => 'id']);
    }
}
