<?php

namespace app\models;

use app\models\SincronizarListaProductosServicios;
use app\models\Unit;

use Yii;


class Product extends \yii\db\ActiveRecord
{
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

    public function getIdsincronizarListaProductosServicios0()
    {
        return $this->hasOne(SincronizarListaProductosServicios::class, ['id' => 'idsincronizarListaProductosServicios']);
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

    // public function beforeSave($insert)
    // {
    //     if (parent::beforeSave($insert)) {
    //         $formattedName = System::setStringFormat($this->name);
    //         $this->name = mb_strtoupper($formattedName, 'UTF-8');
            
    //         return true;
    //     }
    //     return false;
    // } 
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Convertir el nombre a mayúsculas utilizando mb_strtoupper
            $this->name = mb_strtoupper($this->name, 'UTF-8');
            if (empty($this->code)) {
                $this->code = $this->generateCode();
            }
            
            return true;
        }
        return false;
    }
   
    public function generateCode()
    {
        $maxCode = (int) Product::find()
            ->select(['max_code' => 'MAX(CAST(code AS INTEGER))'])
            ->where(['~', 'code', '^[0-9]+$'])
            ->scalar();

        if ($maxCode === null) {
            $maxCode = 0;
        }

        $newCodeNumber = $maxCode + 1;
        return str_pad((string)$newCodeNumber, 6, '0', STR_PAD_LEFT);
    }
    
}
