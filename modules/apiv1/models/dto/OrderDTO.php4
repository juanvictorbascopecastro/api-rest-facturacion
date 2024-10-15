<?php

namespace app\modules\apiv1\models\dto;

use Yii;
use yii\base\Model;
use app\models\Customer;
use app\models\Table;
use app\models\Product;

use app\modules\apiv1\helpers\DataCompany;

class OrderDTO extends Model {
    public $idcustomer; // opcional
    public $nameCustomer; // opcional
    public $table; // necesaria
    public $idtable; // necesaria
    public $note; // opcional
    public $productArr; // necesaria
    public $ioSystemBranch; // se declara en el constructor

    public function __construct($config = [])
    {
        parent::__construct($config);

        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = DataCompany::getSystemBranch($user);
    }

    public function rules()
    {
        return [
            [['table', 'idtable', 'productArr'], 'required', 'message' => 'Este campo es obligatorio.'],
            ['table', 'string', 'message' => 'La mesa debe ser una cadena de caracteres.'],
            ['idtable', 'integer', 'message' => 'El ID de la mesa debe ser un número entero.'],
            ['productArr', 'validateProduct'],
            ['idcustomer', 'validateCustomer'],
            ['nameCustomer', 'string', 'message' => 'El nombre del cliente debe ser una cadena de caracteres.'],
            ['note', 'string', 'message' => 'La nota debe ser una cadena de caracteres.'],
        ];
    }

    // Validar si existe el cliente (opcional)
    public function validateCustomer($attribute, $params)
    {
        if (!empty($this->$attribute)) {
            $customer = Customer::findOne($this->$attribute);

            if ($customer === null) {
                $this->addError($attribute, 'El ID del cliente proporcionado no es válido.');
            }
        }
    }

    // Validar las ventas realizadas (productArr)
    public function validateProduct($attribute, $params)
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'El campo productArr debe ser un array.');
            return;
        }

        if (count($this->$attribute) !== 1) {
            $this->addError($attribute, 'Este recibo solo puede referirse al pago de una venta, pero se están registrando dos ventas en un solo registro!');
            return;
        }

        $total = 0;

        foreach ($this->$attribute as $index => $item) {
            if (!isset($item['idproduct']) || !isset($item['quantityoutput'])) {
                $this->addError($attribute, "El item en la posición $index debe tener los campos 'idproduct' y 'quantityoutput'.");
                continue;
            }

            if (!is_numeric($item['idproduct'])) {
                $this->addError($attribute, "El campo 'idproduct' en la posición $index debe ser un número.");
            }

            if (!is_numeric($item['quantityoutput']) || $item['quantityoutput'] <= 0) {
                $this->addError($attribute, "El campo 'quantityoutput' en la posición $index debe ser un número mayor que cero.");
            }

            $sale = Sale::find()->where(['id' => $item['idproduct']])->one();
            if (!$sale) {
                $this->addError($attribute, "El 'idproduct' en la posición $index con el valor " . $item['idproduct'] . " no existe en la base de datos.");
                continue;
            }
    
            $total += $item['monto'];
        }

        $this->montoTotal = $total;
    }
}
