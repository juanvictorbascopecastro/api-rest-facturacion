<?php

namespace app\modules\apiv1\models;
use app\models\Status;

class Table extends \app\models\Table
{
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'table',
            'x' => function ($model) {
                return floatval($model->x);
            },
            'y' => function ($model) {
                return floatval($model->y);
            },
            'joined',
            'actived',
            'order'
        ];
    }

    public function getOrder()
    {
        $enProceso = (new Status())->EN_PROCESO;
        
        return $this->hasMany(Order::class, ['idtable' => 'id'])
            ->andWhere(['idstatus' => $enProceso]);
    }
}