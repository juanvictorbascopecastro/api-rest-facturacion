<?php

namespace app\modules\apiv1\models;

class Productimage extends \app\models\Productimage {
  
    public function fields() {
        return [
            'id',
            'idproduct',
            'imagepath',
            'datecreated',
            'recyclebin',
            'name',
            'order'
        ];
    }
}
