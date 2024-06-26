<?php

namespace app\modules\auth\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['password'], 'required'],
            [['username', 'email'], 'validateUsernameOrEmail'],
        ];
    }

    public function validateUsernameOrEmail($attribute, $params)
    {
        if (empty($this->username) && empty($this->email)) {
            $this->addError('username', 'Either username or email is required.');
            $this->addError('email', 'Either email or username is required.');
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
