<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categoryConfig".
 *
 * @property int $id
 * @property string $dateCreate
 * @property bool $recycleBin
 * @property int|null $iduser
 * @property string|null $config
 * @property int|null $idstatus
 */
class CategoryConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categoryConfig';
    }

    public static function getDb()
    {
        return Yii::$app->get('iooxs_io');
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'], // dateCreate es un timestamp, así que lo marcamos como "safe"
            [['recycleBin'], 'boolean'], // recycleBin es boolean
            [['iduser', 'idstatus'], 'integer'], // iduser e idstatus son enteros
            [['config'], 'string'], // config es de tipo text
            [['dateCreate'], 'default', 'value' => new \yii\db\Expression('NOW()')], // Por defecto, la fecha de creación es "now()"
            [['recycleBin'], 'default', 'value' => false], // recycleBin es false por defecto
            [['iduser'], 'default', 'value' => 1], // iduser por defecto es 1
            [['idstatus'], 'default', 'value' => 10], // idstatus por defecto es 10
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Fecha de Creación',
            'recycleBin' => 'Papelera de Reciclaje',
            'iduser' => 'ID Usuario',
            'config' => 'Configuración',
            'idstatus' => 'ID Estado',
        ];
    }
}
