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
            if (!isset($product['name']) || !isset($product['quantity']) || !isset($product['price']) || !isset($product['idunit'])) {
                $this->addError($attribute, "El producto en la posición $index debe tener los campos 'name', 'quantity', 'price' y 'idunit'.");
                continue;
            }

            if (!is_string($product['name'])) {
                $this->addError($attribute, "El campo 'name' del producto en la posición $index debe ser una cadena de texto.");
            }

            if (!is_numeric($product['quantity']) || $product['quantity'] <= 0) {
                $this->addError($attribute, "El campo 'quantity' del producto en la posición $index debe ser un número mayor que cero.");
            }

            if (!is_numeric($product['price']) || $product['price'] <= 0) {
                $this->addError($attribute, "El campo 'price' del producto en la posición $index debe ser un número mayor que cero.");
            }

            if (!is_numeric($product['idunit']) || $product['idunit'] <= 0) {
                $this->addError($attribute, "El campo 'idunit' del producto en la posición $index debe ser un número mayor que cero.");
            }

            // Calcular el total basado en el precio y la cantidad
            $total += $product['quantity'] * $product['price'];

            // validar si el id del producto enviado existe en la base de datos
            if (isset($product['id']) && !empty($product['id'])) {
                $existingProduct = Product::findOne($product['id']);
                if ($existingProduct === null) {
                    $this->addError($attribute, "El producto en la posición $index no existe en la base de datos.");
                }
                
                $productBranch = CfgProductBranch::findOne($product['id']);
                if ($productBranch && $productBranch->controlInventory) { // si esta activo el control de invetario
                    // Verificar si se proporciona un idStore
                    if (isset($product['idStore']) && !empty($product['idStore'])) {
                        // Buscar el registro de CfgProductStore específico por id y idStore
                        $productStore = CfgProductStore::findOne(['id' => $product['id'], 'idstore' => $product['idStore']]);
                        if ($productStore) {
                            if ($productStore->stock < $product['quantity']) {  // Verificar si hay suficiente stock para la cantidad solicitada
                                $this->addError($attribute, "El stock en la tienda ID " . $product['idStore'] . " para el producto " . $product['name'] . " es de " . $productStore->stock . " y quiere registrar la cantidad de " . $product['quantity']);
                            }
                        } else {
                            $this->addError($attribute, "No se encontró el registro del producto en la tienda ID " . $product['idStore']);
                        }
                    } else { // Si no se proporciona idStore, verificar el stock total del producto sumando todos los registros de CfgProductStore
                        $productStores = CfgProductStore::findAll(['id' => $product['id']]);
                        $totalStock = 0;
                        foreach ($productStores as $productStore) {
                            $totalStock += floatval($productStore->stock);
                        }
                        if ($totalStock < $product['quantity']) {
                            $this->addError($attribute, "El stock total del producto " . $product['name'] . " es de " . $totalStock . " y quiere registrar la cantidad de " . $product['quantity']);
                        }
                    }
                }
            }
            
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }
}