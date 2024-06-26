<?php
namespace app\modules\admin\models;

use yii\base\Model;

class FormularioModel extends Model
{

    public $iduser;
    public $idioSystemBranch;
    public $expireToken;
    public $iduserActive;

    public function rules()
    {
        return [
            [['iduser', 'idioSystemBranch', 'expireToken', 'iduserActive'], 'required'],
            [['iduser', 'idioSystemBranch', 'expireToken', 'iduserActive'], 'integer'],
            [['expireToken'], 'in', 'range' => [1, 3, 12, 24],
            'message' => 'Expire Token must be one of the following values: 1, 3, 12, 24'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'iduser' => 'ID de Usuario',
            'idioSystemBranch' => 'idioSystemBranch es requerido!',
            'expireToken' => 'Expiración del Token',
            'iduserActive' => 'iduserActive es requerido!'
        ];
    }
}