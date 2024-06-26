<?php

namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;
use app\models\Product;
use app\models\Customer;

class SaleForm extends Model
{
    public $idTypeDocument;
    public $razonSocial;
    public $numeroDocumento;
    public $phone;
    public $products;  // Array de productos
    public $descuento;
    public $idMetodoPago;
    public $total;
    public $idcustomer;

    public function rules()
    {
        return [
            [['idTypeDocument', 'razonSocial', 'numeroDocumento', 'descuento', 'idMetodoPago'], 'required'],
            ['idTypeDocument', 'integer'],
            ['razonSocial', 'string', 'max' => 255],
            ['numeroDocumento', 'string', 'max' => 20],
            ['phone', 'string', 'max' => 20],
            ['descuento', 'number', 'min' => 0],
            ['idMetodoPago', 'integer'],
            ['idcustomer', 'integer'], 
            ['products', 'required', 'message' => 'El campo Productos no puede estar vacío.'],
            ['products', 'validateProducts'],
            // ['idcustomer', 'validateCustomer'],
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

            // Calcular el total basado en el precio y la cantidad
            $total += $product['count'] * $product['price'];

            // Verificar si el id del producto existe en la base de datos
            if (isset($product['id']) && $product['id'] != '') {
                $existingProduct = Product::findOne($product['id']);
                if ($existingProduct === null) {
                    // Si el producto no existe, registrar un nuevo producto
                    $newProduct = new Product();
                    $newProduct->name = $product['name'];
                    $newProduct->price = $product['price'];
                    $newProduct->idstatus = 1;
                    $newProduct->iduser = $user->iduser;

                    if (!$newProduct->save()) {
                        $this->addError($attribute, "El producto en la posición $index no se pudo registrar. Errores: " . json_encode($newProduct->errors));
                    } else {
                        $this->$attribute[$index]['id'] = $newProduct->id;
                    }
                }
            } else {
                // Si el producto no tiene ID, registrar un nuevo producto
                $newProduct = new Product();
                $newProduct->name = $product['name'];
                $newProduct->price = $product['price'];
                $newProduct->idstatus = 1;
                $newProduct->iduser = $user->iduser;

                if (!$newProduct->save()) {
                    $this->addError($attribute, "El producto en la posición $index no se pudo registrar. Errores: " . json_encode($newProduct->errors));
                } else {
                    $this->$attribute[$index]['id'] = $newProduct->id;  // Actualizar el ID del producto en el array de productos
                }
            }
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }
}
