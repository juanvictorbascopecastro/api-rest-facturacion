<?php

namespace app\modules\apiv1\models\dto;

use Yii;
use yii\base\Model;
use app\models\Customer;
use app\models\Table;
use app\models\Product;
use app\models\ProductBranch;
use app\models\Order;
use app\models\Status;

use app\modules\apiv1\helpers\DataCompany;

class OrderDTO extends Model {
    public $idcustomer; // opcional
    public $nameCustomer; // opcional
    public $idtable; // necesaria
    public $comment; // opcional
    public $productArr; // necesaria
    public $ioSystemBranch; // se declara en el constructor
    public $montoTotal;
    public $isEdit = false; // Nuevo indicador para saber si es una edición

    public function __construct($config = [])
    {
        parent::__construct($config);
        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = DataCompany::getSystemBranch($user);
    }

    public function rules()
    {
        return [
            [['idtable', 'productArr'], 'required', 'message' => 'Este campo es obligatorio.'],
            ['idtable', 'integer', 'message' => 'El ID de la mesa debe ser un número entero.'],
            ['productArr', 'validateProduct'],
            ['idcustomer', 'validateCustomer'],
            ['nameCustomer', 'string', 'message' => 'El nombre del cliente debe ser una cadena de caracteres.'],
            ['comment', 'string', 'message' => 'La nota debe ser una cadena de caracteres.'],
            ['idtable', 'validateTable'],
        ];
    }
    
    // Validar si existe la mesa y completar el campo table
    public function validateTable($attribute, $params)
    {
        // Encuentra la mesa por el ID proporcionado
        $myTable = Table::findOne($this->$attribute);

        // Verifica si la mesa existe
        if (!$myTable) {
            $this->addError($attribute, 'El idtable no es válido.');
            return;
        }

        if ($this->isEdit) {
            // Para la edición, necesitamos verificar si la mesa está asociada a un pedido
            $idstatus = (new Status())->EN_PROCESO; 
            $order = Order::find()
                ->where(['idtable' => $this->$attribute])
                ->andWhere(['idstatus' => $idstatus])
                ->one();

            if (!$order) {
                // No hay un pedido asociado con la mesa, por lo que la edición no es válida
                $this->addError($attribute, $myTable->table . ' no tiene un pedido asociado para editar.');
            }
        } else {
            // Modo creación: Verificar si la mesa está ocupada
            $idstatus = (new Status())->EN_PROCESO; 

            $order = Order::find()
                ->where(['idtable' => $this->$attribute, 'idstatus' => $idstatus])
                ->one();

            if ($order) {
                $this->addError($attribute, $myTable->table . ' está ocupada actualmente!');
            }
        }
    }   

    // Validar si existe el cliente (opcional)
    public function validateCustomer($attribute, $params)
    {
        if (!empty($this->$attribute)) {
            $customer = Customer::findOne($this->$attribute);

            if ($customer === null) {
                $this->addError($attribute, 'El ID del cliente proporcionado no es válido.');
            }

            if(empty($nameCustomer)) {
                $nameCustomer = $customer->name;
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

            $product = Product::find()->where(['id' => $item['idproduct']])->one();
            if (!$product) {
                $this->addError($attribute, "El 'idproduct' en la posición $index con el valor " . $item['idproduct'] . " no existe en la base de datos.");
                continue;
            }

            if(empty($item['price'])) {
                $productBranch = ProductBranch::findOne(['id' => $item['idproduct']]);
                $this->$attribute[$index]['price'] = floatval($productBranch->price);
            }

            $total += $item['quantityoutput'] * $this->$attribute[$index]['price'];
        }

        $this->montoTotal = $total;
    }
}
