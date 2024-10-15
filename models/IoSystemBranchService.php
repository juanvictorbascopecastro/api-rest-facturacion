<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cfg.ioSystemBranchService".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int $idioSystemBranch
 * @property string $token
 * @property string $expireToken
 * @property int $iduserActive
 * @property int $idioSystem
 */
class IoSystemBranchService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cfg.ioSystemBranchService';
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
            [['dateCreate', 'expireToken'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'idioSystemBranch', 'iduserActive', 'idioSystem'], 'default', 'value' => null],
            [['iduser', 'idioSystemBranch', 'iduserActive', 'idioSystem'], 'integer'],
            [['idioSystemBranch', 'token', 'expireToken', 'idioSystem'], 'required'],
            [['token'], 'string'],
            [['idioSystem'], 'exist', 'skipOnError' => true, 'targetClass' => IoSystem::class, 'targetAttribute' => ['idioSystem' => 'id']],
            [['idioSystemBranch'], 'exist', 'skipOnError' => true, 'targetClass' => IoSystemBranch::class, 'targetAttribute' => ['idioSystemBranch' => 'id']],
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
            'idioSystemBranch' => 'Idio System Branch',
            'token' => 'Token',
            'expireToken' => 'Expire Token',
            'iduserActive' => 'Iduser Active',
            'idioSystem' => 'Idio System',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IoSystemBranchServiceQuery(get_called_class());
    }

    public static function findByIdUser($id) {
        return static::findOne(['iduserActive' => $id]);
    }
}
