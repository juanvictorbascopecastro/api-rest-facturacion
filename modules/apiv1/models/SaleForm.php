<?php

namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;
use app\models\Product;
use app\models\Customer;

class SaleForm extends Model
{
    public $idtypeDocument;
    public $razonSocial;
    public $numeroDocumento;
    public $phone;
    public $products; 
    public $discountamount;
    public $total;
    public $idcustomer;
    public $isInvoice;
    public $codigoMetodoPago;

    public function rules()
    {
        return [
            [['idtypeDocument', 'discountamount', 'codigoMetodoPago', 'isInvoice'], 'required'],
            ['idtypeDocument', 'required', 'when' => function ($model) {
                return $model->isInvoice;
            }],
            [['idtypeDocument', 'codigoMetodoPago'], 'integer'],
            ['razonSocial', 'required', 'when' => function ($model) {
                return $model->isInvoice;
            }],
            ['razonSocial', 'string', 'max' => 255],
            ['numeroDocumento', 'required', 'when' => function ($model) {
                return $model->isInvoice;
            }],
            ['isInvoice', 'boolean'],
            ['numeroDocumento', 'string', 'max' => 20],
            ['phone', 'string', 'max' => 20],
            ['discountamount', 'number', 'min' => 0],
            ['idcustomer', 'integer'],
            ['products', 'required', 'message' => 'El campo Productos no puede estar vacío.'],
            ['products', 'validateProducts'],
            ['total', 'number', 'min' => 0, 'message' => 'El campo Total debe ser un número mayor o igual a cero.'],
        ];
    }

    public function validateProducts($attribute, $params)
    {
        if (!is_array($this->$attribute) || count($this->$attribute) === 0) {
            $this->addError($attribute, 'El campo Productos debe contener al menos un producto.');
            return;
        }

        $total = 0;

        $user = Yii::$app->user->identity;

        foreach ($this->$attribute as $index => $product) {
            if (!isset($product['name']) || !isset($product['count']) || !isset($product['price'])) {
                $this->addError($attribute, "El producto en la posición $index debe tener los campos 'name', 'count' y 'price'.");
                continue;
            }

            if (!is_string($product['name'])) {
                $this->addError($attribute, "El campo 'name' del producto en la posición $index debe ser una cadena de texto.");
            }

            if (!is_numeric($product['count']) || $product['count'] <= 0) {
                $this->addError($attribute, "El campo 'count' del producto en la posición $index debe ser un número mayor que cero.");
            }

            if (!is_numeric($product['price']) || $product['price'] <= 0) {
                $this->addError($attribute, "El campo 'price' del producto en la posición $index debe ser un número mayor que cero.");
            }

            if (!is_numeric($product['idunit']) || $product['idunit'] <= 0) {
                $this->addError($attribute, "El campo 'idunit' del producto en la posición $index debe ser un número mayor que cero.");
            }

            // Calcular el total basado en el precio y la cantidad
            $total += $product['count'] * $product['price'];

            if (isset($product['id']) && !empty($product['id'])) {
                $existingProduct = Product::findOne($product['id']);
                if ($existingProduct === null) {
                    $this->addError($attribute, "El producto en la posición $index no existe en la base de datos.");
                }
            }
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }
}