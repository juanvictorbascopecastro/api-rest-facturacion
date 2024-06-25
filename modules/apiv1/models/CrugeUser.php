<?php

namespace app\modules\apiv1\models;

class CrugeUser extends \app\models\CrugeUser {

    public function fields()
    {
        return [
            'iduser',
            'regdate',
            'actdate',
            'logondate',
            'username',
            'email',
            'authkey',
            'state',
            'totalsessioncounter',
            'currentsessioncounter',
            'temporal',
            'fullname',
            'name',
            'lastname',
            'surname',
        ];
    }


}