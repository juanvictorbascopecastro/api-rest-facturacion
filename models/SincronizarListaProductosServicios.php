<?php

namespace app\models;

use Yii;

class SincronizarListaProductosServicios extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'siat.sincronizarListaProductosServicios';
    }

    public static function getDb()
    {
        return Yii::$app->get('iooxsBranch');
    }

    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['codigoProducto', 'iduser', 'order'], 'default', 'value' => null],
            [['codigoProducto', 'iduser', 'order'], 'integer'],
            [['descripcionProducto'], 'string'],
            [['codigoActividad'], 'string', 'max' => 30],
        ];
    }

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
            'order' => 'Order',
        ];
    }

    public static function find()
    {
        return new SincronizarListaProductosServiciosQuery(get_called_class());
    }
}
