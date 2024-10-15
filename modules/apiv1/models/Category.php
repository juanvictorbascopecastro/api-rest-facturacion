<?php

namespace app\modules\apiv1\models;

class Category extends \app\models\Category {

    public function fields() {
        return [
            'id',
            'name',
            'symbol',
            'dateCreate',
            'recycleBin',
            'iduser',
            'idcategory',
            'idstatus',
            'printPart',
            'config',
        ];
    }
}