<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property bool|null $recycleBin
 * @property string $dateCreate
 * @property int|null $iduser
 * @property string|null $name
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
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
            [['recycleBin'], 'boolean'],
            [['dateCreate'], 'safe'],
            [['iduser'], 'default', 'value' => null],
            [['iduser'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recycleBin' => 'Recycle Bin',
            'dateCreate' => 'Date Create',
            'iduser' => 'Iduser',
            'name' => 'Name',
        ];
    }
}
