<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatTipoDocumentoIdentidad".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property string|null $descripcion
 * @property int|null $codigoClasificador
 * @property string|null $simbolo
 * @property string|null $commandVerified
 * @property int|null $codigoExcepcion
 */
class SiatTipoDocumentoIdentidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatTipoDocumentoIdentidad';
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
            [['iduser', 'codigoClasificador', 'codigoExcepcion'], 'default', 'value' => null],
            [['iduser', 'codigoClasificador', 'codigoExcepcion'], 'integer'],
            [['descripcion', 'commandVerified'], 'string'],
            [['simbolo'], 'string', 'max' => 5],
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
            'simbolo' => 'Simbolo',
            'commandVerified' => 'Command Verified',
            'codigoExcepcion' => 'Codigo Excepcion',
        ];
    }

    /**
     * {@inheritdoc}
     * @return SiatSiatTipoDocumentoIdentidadQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SiatTipoDocumentoIdentidadQuery(get_called_class());
    }
    
   
}
