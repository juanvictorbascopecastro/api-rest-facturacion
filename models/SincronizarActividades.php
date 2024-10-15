<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "siat.sincronizarActividades".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property bool|null $actived
 * @property string|null $descripcion
 * @property string|null $tipoActividad
 * @property string|null $codigoCaeb
 */
class SincronizarActividades extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.sincronizarActividades';
    }
    
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
            [['recycleBin', 'actived'], 'boolean'],
            [['iduser'], 'default', 'value' => 1],
            [['iduser'], 'integer'],
            [['descripcion'], 'string'],
            [['tipoActividad'], 'string', 'max' => 2],
            [['codigoCaeb'], 'string', 'max' => 20],
            [['id'], 'required'],
            [['id'], 'integer'],
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
            'dateCreate' => 'Fecha de Creación',
            'recycleBin' => 'Papelera de Reciclaje',
            'iduser' => 'ID Usuario',
            'actived' => 'Activo',
            'descripcion' => 'Descripción',
            'tipoActividad' => 'Tipo de Actividad',
            'codigoCaeb' => 'Código CAEB',
        ];
    }
}
