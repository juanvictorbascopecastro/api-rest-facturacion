<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cfg.statusTable".
 *
 * @property int $id
 * @property string $dateCreate
 * @property bool $recycleBin
 * @property int|null $iduser
 * @property string $colorFree
 * @property string|null $colorOccupied
 * @property string|null $colorReserved
 * @property string|null $colorCleaning
 * @property string|null $colorBlocked
 * @property string|null $colorBilled
 */
class StatusTable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.statusTable';
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
            [['recycleBin'], 'boolean'],
            [['iduser'], 'default', 'value' => 1],
            [['colorFree', 'colorOccupied', 'colorReserved', 'colorCleaning', 'colorBlocked', 'colorBilled'], 'string', 'max' => 100],
            [['colorFree'], 'required'],
            [['colorFree'], 'default', 'value' => '#00FF00'],
            [['colorOccupied'], 'default', 'value' => '#FF0000'],
            [['colorReserved'], 'default', 'value' => '#FFFF00'],
            [['colorCleaning'], 'default', 'value' => '#0000FF'],
            [['colorBlocked'], 'default', 'value' => '#800080'],
            [['colorBilled'], 'default', 'value' => '#FFA500'],
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
            'colorFree' => 'Color Free',
            'colorOccupied' => 'Color Occupied',
            'colorReserved' => 'Color Reserved',
            'colorCleaning' => 'Color Cleaning',
            'colorBlocked' => 'Color Blocked',
            'colorBilled' => 'Color Billed',
        ];
    }
}
