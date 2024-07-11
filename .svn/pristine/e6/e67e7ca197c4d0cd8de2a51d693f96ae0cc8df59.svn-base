<?php

namespace app\models;

use Yii;

class CrugeUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName()
    {
        return 'cruge_user';
    }

    public static function getDb()
    {
        return Yii::$app->get('iooxs_access');
    }

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
  
    public function getCrugeAuthassignments()
    {
        return $this->hasMany(CrugeAuthassignment::class, ['userid' => 'iduser']);
    }

    public function getCrugeFieldvalues()
    {
        return $this->hasMany(CrugeFieldvalue::class, ['iduser' => 'iduser']);
    }

    public function getItemnames()
    {
        return $this->hasMany(CrugeAuthitem::class, ['name' => 'itemname'])->viaTable('cruge_authassignment', ['userid' => 'iduser']);
    }

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

    public static function findById($id) {
        return static::findOne(['iduser' => $id]);
    }

    public function validatePassword($password) {
        $hashedPassword = md5($password);
        return $hashedPassword === $this->password;
    }

    public function getId()
    {
        return $this->iduser;
    }


    public function getAuthKey() {
        return $this->authkey;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }
}
