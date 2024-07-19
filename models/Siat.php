<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siat".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property bool|null $connection
 */
class Siat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siat';
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
            [['id'], 'required'],
            [['id', 'iduser'], 'default', 'value' => null],
            [['id', 'iduser'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'connection'], 'boolean'],
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
            'connection' => 'Connection',
        ];
    }
}
