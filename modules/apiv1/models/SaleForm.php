<?php

namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;
use app\models\Product;
use app\models\Customer;
use app\modules\apiv1\models\Unit;
use app\modules\apiv1\models\SincronizarListaProductosServicios;
use app\modules\apiv1\models\SiatTipoDocumentoIdentidad;
use app\modules\apiv1\models\MetodoPago;

class SaleForm extends Model
{
    public $razonSocial;
    public $numeroDocumento;
    public $phone;
    public $products; 
    public $discountamount;
    public $total;
    public $idcustomer;
    public $invoice;
    public $codigoMetodoPago;
    public $validateCodigoExcepcion;
    public $numeroTarjeta;
    
    public $codigoTipoDocumentoIdentidad;

    public function rules()
    {
        return [
            [['codigoTipoDocumentoIdentidad', 'discountamount', 'codigoMetodoPago', 'invoice'], 'required'],
            ['codigoTipoDocumentoIdentidad', 'required', 'when' => function ($model) {
                return $model->invoice;
            }],
            [['codigoTipoDocumentoIdentidad', 'codigoMetodoPago'], 'integer'],
            ['codigoTipoDocumentoIdentidad', 'validateCodigoTipoDocumento'], 
            ['codigoMetodoPago', 'validateCodigoMetodoPago'], 
            ['razonSocial', 'required', 'when' => function ($model) {
                return $model->invoice;
            }],
            ['razonSocial', 'string', 'max' => 255],
            ['numeroDocumento', 'required', 'when' => function ($model) {
                return $model->invoice;
            }],
            ['invoice', 'boolean'],
            ['numeroDocumento', 'string', 'max' => 20],
            ['phone', 'string', 'max' => 20],
            ['discountamount', 'number', 'min' => 0],
            ['idcustomer', 'integer'],
            ['products', 'required', 'message' => 'El campo Productos no puede estar vacío.'],
            ['products', 'validateProducts'],
            ['total', 'number', 'min' => 0, 'message' => 'El campo Total debe ser un número mayor o igual a cero.'],
            ['numeroTarjeta', 'validateNumeroTarjeta'],
            ['validateCodigoExcepcion', 'required'],
            ['validateCodigoExcepcion', 'boolean'], 
                    
            ['codigoTipoDocumentoIdentidad', 'number', 'min' =>1],
        ];
    }

    public function validateNumeroTarjeta($attribute, $params)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return;
        }

        // Expresión regular para el formato 1234-xxxx-xxxx-5678
        $pattern = '/^\d{4}-x{4}-x{4}-\d{4}$/';

        if (!preg_match($pattern, $this->$attribute)) {
            $this->addError($attribute, 'El formato del número de tarjeta no es válido. Debe ser 1234-xxxx-xxxx-5678.');
        }
    }

    public function validateCodigoTipoDocumento($attribute, $params)
    {
        if (!SiatTipoDocumentoIdentidad::findOne($this->$attribute)) {
            $this->addError($attribute, 'El codigo tipo de documento no es válido.');
        }
    }

    public function validateCodigoMetodoPago($attribute, $params)
    {
        if (!MetodoPago::findOne($this->$attribute)) {
            $this->addError($attribute, 'El código de método de pago no es válido.');
        }
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
            if (!isset($product['name']) || !isset($product['quantity']) || !isset($product['price'])) {
                $this->addError($attribute, "El producto en la posición $index debe tener los campos 'name', 'quantity' y 'price'.");
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

            // if (!is_numeric($product['idunit']) || $product['idunit'] <= 0) {
            //     $this->addError($attribute, "El campo 'idunit' del producto en la posición $index debe ser un número mayor que cero.");
            // }

            if (isset($product['idunit']) && $product['idunit'] != null) {
                $unitExists = Unit::find()->where(['id' => $product['idunit']])->exists();
                if (!$unitExists) {
                    $this->addError($attribute, "El 'idunit' del producto en la posición $index con el valor " . $product['idunit'] . " no existe en la base de datos.");
                }
            }
    
            if (isset($product['codigoProducto']) && $product['codigoProducto'] != null) {
                $productoServicioExists = SincronizarListaProductosServicios::find()->where(['codigoProducto' => $product['codigoProducto']])->exists();
                if (!$productoServicioExists) {
                    $this->addError($attribute, "El 'codigoProducto' del producto en la posición $index con el valor " . $product['codigoProducto'] . " no existe en la base de datos.");
                }
            }

            // Calcular el total basado en el precio y la cantidad
            $total += $product['quantity'] * $product['price'];

            // validar si el id del producto enviado existe en la base de datos
            if (isset($product['id']) && $product['id'] != null) {
                $existingProduct = Product::findOne($product['id']);
                if ($existingProduct === null) {
                    $this->addError($attribute, "El producto en la posición $index no existe en la base de datos.");
                }
                
                // CODIGO PARA VERIFICAR EL STOCK 
                $productBranch = ProductBranch::findOne($product['id']);
                if ($productBranch && $productBranch->controlInventory) { // si esta activo el control de invetario
                    if (isset($product['idStore']) && !empty($product['idStore'])) {  // Verificar si se proporciona un idStore
                        // Buscar el registro de ProductStore específico por id y idStore
                        $productStore = ProductStore::findOne(['id' => $product['id'], 'idstore' => $product['idStore']]);
                        if ($productStore) {
                            if ($productStore->stock < $product['quantity']) {  // Verificar si hay suficiente stock para la cantidad solicitada
                                $this->addError($attribute, "El stock en la tienda ID " . $product['idStore'] . " para el producto " . $product['name'] . " es de " . $productStore->stock . " y quiere registrar la cantidad de " . $product['quantity']);
                            }
                        } else {
                            $this->addError($attribute, "No se encontró el registro del producto en la tienda ID " . $product['idStore']);
                        }
                    } else { // Si no se proporciona idStore, verificar el stock total del producto sumando todos los registros de ProductStore
                        $productStores = ProductStore::findAll(['id' => $product['id']]);
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