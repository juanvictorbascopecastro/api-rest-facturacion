<?php

namespace app\modules\admin\models;

class IoSystemBranchService extends \app\models\IoSystemBranchService {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'idioSystemBranch',
            'token',
            'expireToken',
            'iduserActive',
            'idioSystem',
        ];
    }

}