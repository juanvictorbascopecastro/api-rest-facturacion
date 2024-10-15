<?php

namespace app\modules\service\models;

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
        ];
    }

}