<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "table".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property string|null $table
 * @property float|null $x
 * @property float|null $y
 * @property string|null $joined
 * @property bool|null $actived
 */
class Table extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'table';
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
            [['iduser'], 'integer'],
            [['x', 'y'], 'number'],
            [['table', 'joined'], 'string', 'max' => 255],
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
            'iduser' => 'User ID',
            'table' => 'Table Name',
            'x' => 'X Coordinate',
            'y' => 'Y Coordinate',
            'joined' => 'Joined',
            'actived' => 'Actived',
        ];
    }
}