<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.userSystemPoint".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int $iduserEnabled
 * @property int $idsystemPoint
 * @property int $idstatus
 * @property int|null $iduser
 */
class UserSystemPoint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.userSystemPoint';
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
            [['iduserEnabled', 'idstatus'], 'required'],
            [['iduserEnabled', 'idsystemPoint', 'idstatus', 'iduser'], 'default', 'value' => null],
            [['iduserEnabled', 'idsystemPoint', 'idstatus', 'iduser'], 'integer'],
            [['idsystemPoint'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSystemPoint::class, 'targetAttribute' => ['idsystemPoint' => 'id']],
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
            'iduserEnabled' => 'Iduser Enabled',
            'idsystemPoint' => 'Idsystem Point',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
        ];
    }
}
