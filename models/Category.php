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
            [['name'], 'required'],  // Nombre requerido
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'idcategory'], 'default', 'value' => null],
            [['iduser', 'idcategory'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['symbol'], 'string', 'max' => 5],
        ];
    }
    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'symbol' => 'Symbol',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'Iduser',
            'idcategory' => 'Idcategory',
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
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
}
