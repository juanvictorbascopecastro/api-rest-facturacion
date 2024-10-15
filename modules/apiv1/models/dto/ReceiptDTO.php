<?php

namespace app\modules\apiv1\models\dto;

use Yii;
use yii\base\Model;
use app\models\Customer;
use app\models\MetodoPago;
use app\models\Receipt;
use app\models\Sale;

use app\modules\apiv1\helpers\DataCompany;

class ReceiptDTO extends Model {
    public $idcustomer;
    public $comment;
    public $number;
    public $montoTotal;
    public $salesArr;
    public $codigoMetodoPago;

    public $cashOpen; 
    public $ioSystemBranch;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = DataCompany::getSystemBranch($user);
        $this->cashOpen = DataCompany::getCash($user);
    }

    public function rules()
    {
        return [
            [['idcustomer', 'montoTotal', 'salesArr', 'codigoMetodoPago'], 'required', 'message' => 'Este campo es obligatorio.'],
            ['idcustomer', 'validateCustomer'],
            ['number', 'string'],
            ['salesArr', 'validateSalesArr'],
            ['codigoMetodoPago', 'integer', 'message' => 'El código de método de pago debe ser un número entero.'],
            ['codigoMetodoPago', 'validateCodigoMetodoPago'],
            ['cashOpen', 'validateCaja'],
            ['number', 'validateNumberExistence'],
        ];
    }

    // validar si hay caja aperturada
    public function validateCaja() {
        $user = Yii::$app->user->identity;
        if ($this->cashOpen == null) {
            $this->addError($attribute, 'Debe realizar una  "APERTURA DE CAJA VENTA" previamente');
        }
    }
    // Valida si existe el numero de venta. En caso de que la empresa deba registrar el numero de venta
    public function validateNumberExistence($attribute, $params)
    {
        if(!$this->ioSystemBranch->allowAutoNumberSale) {
            if(!isset($this->$attribute) && empty($this->$attribute)) {
                $this->addError($attribute, 'El número de recibo es necesariamente requerido!.');
            } else {
                $receipt = Receipt::findOne(['number' => $this->$attribute]);
                if ($receipt) {
                    $this->addError($attribute, 'El número de recibo ' . $receipt->number . ' ya existe!.');
                }
            }
        } else {
            $this->number = null;
        }
    }
    // validar si eciste el cliente
    public function validateCustomer($attribute, $params)
    {
        if ($this->$attribute !== null) {
            $customer = Customer::findOne($this->$attribute);

            if ($customer === null) {
                $this->addError($attribute, 'El ID del cliente proporcionado no es válido.');
            }
        }
    }
    // Metodo de pago 
    public function validateCodigoMetodoPago($attribute, $params)
    {
        if (!MetodoPago::findOne($this->$attribute)) {
            $this->addError($attribute, 'El código de método de pago no es válido.');
        }
    }
    // validar sobre que ventas esta realizando pagos
    public function validateSalesArr($attribute, $params)
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'El campo salesArr debe ser un array.');
            return;
        }

        if (count($this->$attribute) !== 1) {
            $this->addError($attribute, 'Este recibo solo puede referirse al pago de una venta, pero se están registrando dos ventas en un solo registro!');
            return;
        }

        $total = 0;

        foreach ($this->$attribute as $index => $item) {
            if (!isset($item['idsale']) || !isset($item['monto'])) {
                $this->addError($attribute, "El monto en la posición $index debe tener los campos 'idsale', 'monto'.");
                continue;
            }

            if (!is_numeric($item['idsale'])) {
                $this->addError($attribute, "El campo 'idsale' del la venta en la posición $index debe ser un número.");
            }

            if (!is_numeric($item['monto']) || $item['monto'] <= 0) {
                $this->addError($attribute, "El campo 'monto' del pago en la posición $index debe ser un número mayor que cero.");
            }

            $sale = Sale::find()->where(['id' => $item['idsale']])->one();
            if (!$sale) {
                $this->addError($attribute, "El 'idsale' de la venta en la posición $index con el valor " . $item['idsale'] . " no existe en la base de datos.");
                continue;
            }
            
            // validar si debe algun monto
            if($sale->idtypeCharge != 2) {
                $this->addError($attribute, "El 'idsale' en la posición $index, valor " . $item['idsale'] . ", no está registrado como una venta a crédito.");
            }
    
            $total += $item['monto'];      
        }

        $this->montoTotal = $total;
    }
}
