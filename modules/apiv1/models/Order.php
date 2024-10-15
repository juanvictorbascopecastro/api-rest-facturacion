<?php

namespace app\modules\apiv1\models;
use app\modules\apiv1\models\ProductOrder;
use app\models\Table;
use app\models\Status;

class Order extends \app\models\Order
{
    public function fields()
    {
        return [
            'id',
            'dateCreate',
            'recycleBin',
            'iduser',
            'idcustomer',
            'nameCustomer',
            'codigoTipoDocumentoIdentidad',
            'numeroDocumento',
            'idstatus',
            'idpriceSheet',
            'number',
            'discountpercentage' => function ($model) {
                return floatval($model->discountpercentage);
            },
            'discountamount' => function ($model) {
                return floatval($model->discountamount);
            },
            'montoTotal' => function ($model) {
                return floatval($model->montoTotal);
            },
            'subTotal' => function ($model) {
                return floatval($model->subTotal);
            },
            'codigoMoneda',
            'comment',
            'idtable',
            'sendPrint',
            'daysLimit',
            'phone',
            'email',
            'productOrder',
            'printBill',
            'tableData' => function ($model) {
                return $model->getTableData();
            }
        ];
    }

    public function getProductOrder() 
    {
        return $this->hasMany(ProductOrder::class, ['idorder' => 'id']);
    }

    public function getTableData() 
    {
        $table = $this->hasOne(Table::class, ['id' => 'idtable'])->one(); 

        if ($table) {
            $isOccupied = Order::find()
                ->where(['idtable' => $table->id])
                ->andWhere(['idstatus' => 30])
                ->exists();

            return [
                'id' => $table->id,
                'dateCreate' => $table->dateCreate,  
                'recycleBin' => $table->recycleBin,  
                'iduser' => $table->iduser, 
                'table' => $table->table,  
                'x' => doubleval($table->x),
                'y' => doubleval($table->y),
                'joined' => $table->joined,  
                'actived' => $table->actived, 
                'idstatus' => $isOccupied ? (new Status())->EN_PROCESO : (new Status())->FINALIZADO
            ];
        }

        return null;  // Si no hay una mesa asociada
    }

}