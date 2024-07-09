<?php

namespace app\modules\apiv1\models;

class CfgStore extends \app\models\CfgStore {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'name',
        ];
    }
}
