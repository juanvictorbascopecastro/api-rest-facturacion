<?php

namespace app\modules\admin\models;

class CfgIoSystemBranchService extends \app\models\CfgIoSystemBranchService {

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