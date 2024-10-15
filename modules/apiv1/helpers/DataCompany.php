<?php 
namespace app\modules\apiv1\helpers;

use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\IoSystemBranch;
use app\models\Cash;

class DataCompany {
    // Obtener los datos de la empresa
    public static function getSystemBranch($user)
    {
        $ioSystemBranchUser = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
        if (!$ioSystemBranchUser) {
            return null;
        }

        return IoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);
    }

    // Obtener la caja
    public static function getCash($user) {
        return Cash::find()
            ->where(['idstatus' => Cash::STATUS_ABIERTO, 'iduser' => $user->id])
            ->one();
    }
}