<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "metodoPago".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property string|null $descripcion
 * @property bool|null $activedSiat
 * @property bool|null $cardService
 * @property bool|null $actived
 */
class SiatTipoMetodoPago extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'siat.siatTipoMetodoPago';
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
            [['id'], 'required'],
            [['id', 'iduser'], 'default', 'value' => null],
            [['id', 'iduser', 'codigoClasificador'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['descripcion'], 'string'],
            [['id'], 'unique'],
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
            'codigoClasificador' => 'codigoClasificador',
        ];
    }

    /**
     * {@inheritdoc}
     * @return MetodoPagoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SiatTipoMetodoPagoQuery(get_called_class());
    }
}
