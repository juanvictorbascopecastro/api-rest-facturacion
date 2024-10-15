<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vendor".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $name
 * @property string|null $numeroDocumento segunt FORMATO SIAT
 * @property string|null $numberPhone
 * @property string|null $numberPhone2
 * @property int|null $iduser
 * @property int|null $iddocumentNumberType
 */
class Vendor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vendor';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsRoot');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'iddocumentNumberType'], 'default', 'value' => null],
            [['iduser', 'iddocumentNumberType'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['numeroDocumento'], 'string', 'max' => 20],
            [['numberPhone', 'numberPhone2'], 'string', 'max' => 30],
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
            'numeroDocumento' => 'Numero Documento',
            'numberPhone' => 'Number Phone',
            'numberPhone2' => 'Number Phone2',
            'iduser' => 'Iduser',
            'iddocumentNumberType' => 'Iddocument Number Type',
        ];
    }

    /**
     * {@inheritdoc}
     * @return VendorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VendorQuery(get_called_class());
    }
}
