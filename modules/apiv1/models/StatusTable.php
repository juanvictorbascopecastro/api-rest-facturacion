<?php

namespace app\modules\apiv1\models;

class StatusTable extends \app\models\StatusTable {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'colorFree',
            'colorOccupied',
            'colorReserved',
            'colorCleaning',
            'colorBlocked',
            'colorBilled',
        ];
    }
}