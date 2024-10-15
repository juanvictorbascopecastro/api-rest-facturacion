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
class MetodoPago extends \yii\db\ActiveRecord
{
    private static $customDb;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metodoPago';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'iduser'], 'default', 'value' => null],
            [['id', 'iduser'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'activedSiat', 'cardService', 'actived'], 'boolean'],
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
            'activedSiat' => 'Actived Siat',
            'cardService' => 'Card Service',
            'actived' => 'Actived',
        ];
    }

    /**
     * {@inheritdoc}
     * @return MetodoPagoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetodoPagoQuery(get_called_class());
    }

    public static function getDb()
    {
        return Yii::$app->iooxsRoot;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }
}
