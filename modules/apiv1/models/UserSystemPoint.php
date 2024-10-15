<?php

namespace app\modules\apiv1\models;

class UserSystemPoint extends \app\models\UserSystemPoint {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduserEnabled',
            'idsystemPoint',
            'idstatus',
            'iduser',
            'idstoreMain',
        ];
    }
}
