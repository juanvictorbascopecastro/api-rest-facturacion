<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatUnidadMedida".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property string|null $descripcion
 * @property int|null $codigoClasificador
 */
class UnidadMedida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatUnidadMedida';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
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
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'codigoClasificador'], 'default', 'value' => null],
            [['iduser', 'codigoClasificador'], 'integer'],
            [['descripcion'], 'string'],
            [['codigoClasificador'], 'unique'],
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
            'iduser' => 'Iduser',
            'descripcion' => 'Descripcion',
            'codigoClasificador' => 'Codigo Clasificador',
        ];
    }

    /**
     * {@inheritdoc}
     * @return SiatUnidadMedidaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UnidadMedidaQuery(get_called_class());
    }
}
