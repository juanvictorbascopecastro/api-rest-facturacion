<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatModalidad".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int $idstatus
 * @property string|null $modalidad
 * @property int|null $codigoModalidad
 */
class SiatModalidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatModalidad';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxs_access');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idstatus'], 'required'],
            [['id', 'iduser', 'idstatus', 'codigoModalidad'], 'default', 'value' => null],
            [['id', 'iduser', 'idstatus', 'codigoModalidad'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['modalidad'], 'string', 'max' => 50],
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
            'idstatus' => 'Idstatus',
            'modalidad' => 'Modalidad',
            'codigoModalidad' => 'Codigo Modalidad',
        ];
    }
}
