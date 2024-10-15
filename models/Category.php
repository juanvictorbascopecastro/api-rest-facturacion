<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $symbol
 * @property string $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $idcategory
 *
 * @property Product[] $products
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @var Connection
     */
    private static $customDb;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'], 
            [['recycleBin'], 'boolean'],
            [['iduser', 'idcategory', 'idstatus', 'printPart'], 'integer'], 
            [['config', 'name'], 'string'], 
            [['symbol'], 'string', 'max' => 5], 
            [['dateCreate'], 'default', 'value' => new \yii\db\Expression('NOW()')], 
            [['recycleBin'], 'default', 'value' => false], 
            [['iduser'], 'default', 'value' => 1], 
            [['idstatus'], 'default', 'value' => 10], 
            [['printPart'], 'default', 'value' => 1], 
            ['config', 'validateConfig', 'skipOnEmpty' => true]
        ];
    }

    public function validateConfig($attribute, $params)
    {
        if (!empty($this->config)) {
            $categoryConfig = CategoryConfig::findOne(['config' => $this->config]);
            
            if ($categoryConfig === null) {
                $this->addError($attribute, 'La configuración proporcionada no existe.');
            }
        }
    }   

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'symbol' => 'Símbolo',
            'dateCreate' => 'Fecha de Creación',
            'recycleBin' => 'Papelera de Reciclaje',
            'iduser' => 'ID Usuario',
            'idcategory' => 'ID Categoría',
            'idstatus' => 'ID Estado',
            'printPart' => 'Parte de Impresión',
            'config' => 'Configuración',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery|ProductQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['idcategory' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    
    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->iooxsRoot;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
}
