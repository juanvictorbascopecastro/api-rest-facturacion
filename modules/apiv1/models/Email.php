<?php

namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;

class Email extends Model
{
    public $to;
    public $subject;
    public $body;
    public $altBody;

    public function rules()
    {
        return [
            [['to', 'subject', 'body'], 'required'],
            [['to'], 'email'],
            [['body', 'altBody'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'to' => 'Para',
            'subject' => 'Asunto',
            'body' => 'Cuerpo HTML',
            'altBody' => 'Cuerpo Texto Plano',
        ];
    }
}
