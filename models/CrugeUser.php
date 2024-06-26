<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "cruge_user".
 *
 * @property int $iduser
 * @property int|null $regdate
 * @property int|null $actdate
 * @property int|null $logondate
 * @property string|null $username
 * @property string|null $email
 * @property string|null $password
 * @property string|null $authkey
 * @property int|null $state
 * @property int|null $totalsessioncounter
 * @property int|null $currentsessioncounter
 * @property bool $temporal
 * @property string|null $fullname
 * @property string|null $name
 * @property string|null $lastname
 * @property string|null $surname
 *
 * @property CrugeAuthassignment[] $crugeAuthassignments
 * @property CrugeFieldvalue[] $crugeFieldvalues
 * @property CrugeAuthitem[] $itemnames
 */
class CrugeUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cruge_user';
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
            [['regdate', 'actdate', 'logondate', 'state', 'totalsessioncounter', 'currentsessioncounter'], 'default', 'value' => null],
            [['regdate', 'actdate', 'logondate', 'state', 'totalsessioncounter', 'currentsessioncounter'], 'integer'],
            [['temporal'], 'boolean'],
            [['fullname'], 'string'],
            [['username', 'password'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 45],
            [['authkey'], 'string', 'max' => 100],
            [['name', 'lastname', 'surname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iduser' => 'Iduser',
            'regdate' => 'Regdate',
            'actdate' => 'Actdate',
            'logondate' => 'Logondate',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'authkey' => 'Authkey',
            'state' => 'State',
            'totalsessioncounter' => 'Totalsessioncounter',
            'currentsessioncounter' => 'Currentsessioncounter',
            'temporal' => 'Temporal',
            'fullname' => 'Fullname',
            'name' => 'Name',
            'lastname' => 'Lastname',
            'surname' => 'Surname',
        ];
    }
   /**
     * {@inheritdoc}
     */
   
    ////////////////////////////

    /**
     * Gets query for [[CrugeAuthassignments]].
     *
     * @return \yii\db\ActiveQuery|CrugeAuthassignmentQuery
     */
    public function getCrugeAuthassignments()
    {
        return $this->hasMany(CrugeAuthassignment::class, ['userid' => 'iduser']);
    }

    /**
     * Gets query for [[CrugeFieldvalues]].
     *
     * @return \yii\db\ActiveQuery|CrugeFieldvalueQuery
     */
    public function getCrugeFieldvalues()
    {
        return $this->hasMany(CrugeFieldvalue::class, ['iduser' => 'iduser']);
    }

    /**
     * Gets query for [[Itemnames]].
     *
     * @return \yii\db\ActiveQuery|CrugeAuthitemQuery
     */
    public function getItemnames()
    {
        return $this->hasMany(CrugeAuthitem::class, ['name' => 'itemname'])->viaTable('cruge_authassignment', ['userid' => 'iduser']);
    }

    /**
     * {@inheritdoc}
     * @return CrugeUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CrugeUserQuery(get_called_class());
    }
    /////////////////////////
    public static function findIdentity($id)
    {
        return static::findOne($id); // busca por el id
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['iduser' => (string) $token->getClaim('uid')]);
    }

    // public static function findByUsername($username) {
    //     return static::findOne(['username' => $username]);
    // }

    // public static function findByEmail($email) {
    //     return static::findOne(['email' => $email]);
    // }

    public static function findById($id) {
        return static::findOne(['iduser' => $id]);
    }

    public function validatePassword($password) {
        $hashedPassword = md5($password);
        return $hashedPassword === $this->password;
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthKey() {
        return $this->authkey;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    // public function beforeSave($insert)
    // {
    //     if (parent::beforeSave($insert)) {
    //         if ($this->isNewRecord) {
    //             $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    //             $this->authkey = \Yii::$app->security->generateRandomString();
    //             $this->accesstoken = password_hash(random_bytes(15), PASSWORD_DEFAULT);
    //         }
    //         return true;
    //     }
    //     return false;
    // }
}
