<?php

namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;

class Whatsapp extends Model
{
    public $to;
    public $message;

    public function rules()
    {
        return [
            [['to', 'message'], 'required'],
            ['to', 'string'],
            ['message', 'string'],
        ];
    }
}
