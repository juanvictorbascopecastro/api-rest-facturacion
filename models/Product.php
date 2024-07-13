<?php

namespace app\models;

use Yii;


class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin', 'stockcontrol', 'typeBudget'], 'boolean'],
            [['name', 'idstatus'], 'required'],
            [['name', 'tags', 'nameRef', 'description', 'nameSource', 'codeSource', 'activePrinciple', 'rs'], 'string'],
            [['idunit', 'idcategory', 'idsincronizarListaProductosServicios', 'idstatus', 'iduser', 'idmark'], 'default', 'value' => null],
            [['idunit', 'idcategory', 'idsincronizarListaProductosServicios', 'idstatus', 'iduser', 'idmark'], 'integer'],
            [['dimensionwidth', 'dimensionlength', 'dimensionheight', 'weight', 'price'], 'number'],
            [['code', 'barcode', 'codeRef'], 'string', 'max' => 20],
            [['idcategory'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['idcategory' => 'id']],
            [['idunit'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::class, 'targetAttribute' => ['idunit' => 'id']],
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
            'name' => 'Name',
            'tags' => 'Tags',
            'code' => 'Code',
            'barcode' => 'Barcode',
            'idunit' => 'Idunit',
            'idcategory' => 'Idcategory',
            'stockcontrol' => 'Stockcontrol',
            'dimensionwidth' => 'Dimensionwidth',
            'dimensionlength' => 'Dimensionlength',
            'dimensionheight' => 'Dimensionheight',
            'codeRef' => 'Code Ref',
            'weight' => 'Weight',
            'nameRef' => 'Name Ref',
            'idsincronizarListaProductosServicios' => 'Idsincronizar Lista Productos Servicios',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
            'description' => 'Description',
            'typeBudget' => 'Type Budget',
            'price' => 'Price',
            'nameSource' => 'Name Source',
            'codeSource' => 'Code Source',
            'idmark' => 'Idmark',
            'activePrinciple' => 'Active Principle',
            'rs' => 'Rs',
        ];
    }

    public function getIdcategory0()
    {
        return $this->hasOne(Category::class, ['id' => 'idcategory']);
    }

    public function getIdunit0()
    {
        return $this->hasOne(Unit::class, ['id' => 'idunit']);
    }

    public function getProductImages()
    {
        return $this->hasMany(Productimage::class, ['idproduct' => 'id']);
    }
    
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public static function getDb()
    {
        return Yii::$app->iooxsRoot;
    }
}
