<?php

namespace app\modules\apiv1\models;

class SiatBranch extends \app\models\SiatBranch {

    public function fields() {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'codigoSucursal' => function ($model) {
                return floatval($model->codigoSucursal);
            },
            'idsiatSystem' => function ($model) {
                return floatval($model->idsiatSystem);
            },
            'iduser' => function ($model) {
                return floatval($model->iduser);
            },
            'codigoSistema' => function ($model) {
                return strval($model->codigoSistema);
            },
            'codigoModalidad'  => function ($model) {
                return floatval($model->codigoModalidad);
            },
            'codigoAmbiente' => function ($model) {
                return floatval($model->codigoAmbiente);
            },
            'nit' => function ($model) {
                return strval($model->nit);
            },
            'razonSocial',
            'signerPassword' => function ($model) {
                return strval($model->signerPassword);
            },
        ];
    }
}