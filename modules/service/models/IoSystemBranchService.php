<?php

namespace app\modules\service\models;

use app\models\CfgIoSystem;

class IoSystemBranchService extends \app\models\IoSystemBranchService {
    public function fields()
    {
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
