<?php 

namespace app\modules\service\models;

class SincronizarListaLeyendasFactura extends \app\models\SincronizarListaLeyendasFactura {

    public function fields() {
        return [
            'id',
            // 'dateCreate',
            // 'recycleBin',
            // 'iduser',
            'descripcionLeyenda',
        ];
    }

}