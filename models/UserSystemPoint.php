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
    public static $statusACTIVO = 10;
    public static $statusINACTIVO = 60;
    public static $statusBLOQUEADO = 70;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.userSystemPoint';
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
            [['iduserEnabled', 'idsystemPoint', 'idstatus'], 'required'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'ownerIduser'], 'boolean'],
            [['iduserEnabled', 'idsystemPoint', 'idstatus', 'iduser', 'idstoreMain'], 'integer'],
            [['idsystemPoint'], 'exist', 'skipOnError' => true, 'targetClass' => SystemPoint::class, 'targetAttribute' => ['idsystemPoint' => 'id']],
            [['dateCreate'], 'default', 'value' => new \yii\db\Expression('NOW()')],
            [['recycleBin'], 'default', 'value' => false],
            [['idsystemPoint'], 'default', 'value' => 1],
            [['iduser'], 'default', 'value' => 1],
            [['idstoreMain'], 'default', 'value' => 1],
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
            'iduserEnabled' => 'User Enabled',
            'idsystemPoint' => 'System Point',
            'idstatus' => 'Status',
            'iduser' => 'User',
            'idstoreMain' => 'Store Sale',
            'ownerIduser' => 'ownerIduser'
        ];
    }

    /**
     * Gets query for [[SystemPoint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdsystemPoint0() {
        return $this->hasOne(SystemPoint::class, ['id' => 'idsystemPoint']);
    }

    public function getModel() {
        return UserSystemPoint::find()
                        ->where(['iduserEnabled' => Yii::$app->user->getId()])
                        ->andWhere(['idstatus' => UserSystemPoint::$statusACTIVO])
                        ->one();
    }

    
}