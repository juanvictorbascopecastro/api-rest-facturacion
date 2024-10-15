<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.sincronizarListaLeyendasFactura".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property string|null $descripcionLeyenda
 */
class SincronizarListaLeyendasFactura extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.sincronizarListaLeyendasFactura';
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
            [['iduser'], 'default', 'value' => null],
            [['iduser'], 'integer'],
            [['descripcionLeyenda'], 'string'],
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
            'descripcionLeyenda' => 'Descripcion Leyenda',
        ];
    }
}
