<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatToken".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $token
 * @property int $idsiatBranch
 * @property string $fechadesde
 * @property string $fechahasta
 * @property int $idstatus
 * @property int|null $iduser
 */
class SiatToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatToken';
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
            [['dateCreate', 'fechadesde', 'fechahasta'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['token'], 'string'],
            [['idsiatBranch', 'fechadesde', 'fechahasta', 'idstatus'], 'required'],
            [['idsiatBranch', 'idstatus', 'iduser'], 'default', 'value' => null],
            [['idsiatBranch', 'idstatus', 'iduser'], 'integer'],
            [['idsiatBranch'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSiatBranch::class, 'targetAttribute' => ['idsiatBranch' => 'id']],
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
            'token' => 'Token',
            'idsiatBranch' => 'Idsiat Branch',
            'fechadesde' => 'Fechadesde',
            'fechahasta' => 'Fechahasta',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
        ];
    }
}
