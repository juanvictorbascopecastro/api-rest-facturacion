<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $dateCreate
 * @property bool $recycleBin
 * @property string $name
 * @property string|null $tags
 * @property string|null $code
 * @property string|null $barcode
 * @property int|null $idunit
 * @property int|null $idcategory
 * @property bool $stockcontrol
 * @property float|null $dimensionwidth cm
 * @property float|null $dimensionlength cm
 * @property float|null $dimensionheight cm
 * @property string|null $codeRef
 * @property float|null $weight
 * @property string|null $nameRef from factory or provider
 * @property int|null $idsincronizarListaProductosServicios
 * @property int $idstatus
 * @property int|null $iduser
 * @property string|null $description
 * @property bool|null $typeBudget
 * @property float|null $price
 * @property string|null $nameSource
 * @property string|null $codeSource
 *
 * @property Category $idcategory0
 * @property Unit $idunit0
 */
class Product extends \yii\db\ActiveRecord
{
    private static $customDb;
    
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
            [['name', 'tags', 'nameRef', 'description', 'nameSource', 'codeSource'], 'string'],
            [['idunit', 'idcategory', 'idsincronizarListaProductosServicios', 'idstatus', 'iduser'], 'default', 'value' => null],
            [['idunit', 'idcategory', 'idsincronizarListaProductosServicios', 'idstatus', 'iduser'], 'integer'],
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

    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
}
