<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.sincronizarListaProductosServicios".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $codigoActividad
 * @property int|null $codigoProducto
 * @property string|null $descripcionProducto
 * @property int|null $iduser
 */
class SincronizarListaProductosServicios extends \yii\db\ActiveRecord 
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.sincronizarListaProductosServicios';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['codigoProducto', 'iduser'], 'default', 'value' => null],
            [['codigoProducto', 'iduser'], 'integer'],
            [['descripcionProducto'], 'string'],
            [['codigoActividad'], 'string', 'max' => 30],
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
            'codigoActividad' => 'Codigo Actividad',
            'codigoProducto' => 'Codigo Producto',
            'descripcionProducto' => 'Descripcion Producto',
            'iduser' => 'Iduser',
        ];
    }
}
