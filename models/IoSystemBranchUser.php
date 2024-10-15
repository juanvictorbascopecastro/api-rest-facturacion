<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cfg.ioSystemBranchUser".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $idioSystemBranch
 * @property int|null $iduserActive
 * @property int|null $iduser
 * @property int|null $idstatus
 * @property int|null $defaultpriority
 * @property int|null $idioSystem
 */
class IoSystemBranchUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.ioSystemBranchUser';
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
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['idioSystemBranch', 'iduserActive', 'iduser', 'idstatus', 'defaultpriority', 'idioSystem'], 'default', 'value' => null],
            [['idioSystemBranch', 'iduserActive', 'iduser', 'idstatus', 'defaultpriority', 'idioSystem'], 'integer'],
            [['idioSystemBranch'], 'exist', 'skipOnError' => true, 'targetClass' => CfgIoSystemBranch::class, 'targetAttribute' => ['idioSystemBranch' => 'id']],
            [['idstatus'], 'exist', 'skipOnError' => true, 'targetClass' => CfgStatus::class, 'targetAttribute' => ['idstatus' => 'id']],
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
            'idioSystemBranch' => 'Idio System Branch',
            'iduserActive' => 'Iduser Active',
            'iduser' => 'Iduser',
            'idstatus' => 'Idstatus',
            'defaultpriority' => 'Defaultpriority',
            'idioSystem' => 'Idio System',
        ];
    }
    // public function getIoSystemBranch()
    // {
    //     return $this->hasOne(IoSystemBranch::class, ['idioSystemBranch' => 'id']);
    // }
    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IoSystemBranchUserQuery(get_called_class());
    }

    public static function findByIdUser($id) {
        return static::findOne(['iduserActive' => $id]);
    }
}
