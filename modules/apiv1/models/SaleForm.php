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

use app\modules\apiv1\models\IoSystemBranch;
use app\models\IoSystemBranchUser;
use app\modules\apiv1\models\SiatBranch;
use app\modules\apiv1\models\Productstock;

use app\models\Cash;

class SaleForm extends Model
{
    public $razonSocial;
    public $numeroDocumento;
    public $phone;
    public $products; 
    public $discountamount;
    public $total;
    public $idcustomer;
    public $invoice; // si factura o no
    public $codigoMetodoPago;
    public $validateCodigoExcepcion;
    public $numeroTarjeta;
    public $codigoTipoDocumentoIdentidad;
    public $ioSystemBranch; // configuracion de la empresa
    public $number; // numero de venta
    public $idtypeCharge; // parametro 1 si es al contado, 2 si es al credito
    public $cashOpen;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = $this->getSystemBranch($user);
        $this->cashOpen = $this->getCash($user);
        if ($this->idtypeCharge === null || !isset($this->idtypeCharge)) {
            $this->idtypeCharge = 1;
        }
    }

    public function rules()
    {
        return [
            [['discountamount', 'codigoMetodoPago', 'invoice'], 'required', 'message' => 'Este campo es obligatorio.'],
            ['codigoTipoDocumentoIdentidad', 'required', 'when' => function ($model) {
                return $model->invoice && ($model->razonSocial || $model->numeroDocumento);
            }, 'message' => 'El código tipo de documento es obligatorio cuando hay una factura.'],
            ['codigoTipoDocumentoIdentidad', 'integer', 'message' => 'El código tipo de documento debe ser un número entero.'],
            ['codigoTipoDocumentoIdentidad', 'validateCodigoTipoDocumento', 'when' => function ($model) {
                return $model->invoice && ($model->razonSocial || $model->numeroDocumento);
            }],
            ['codigoMetodoPago', 'integer', 'message' => 'El código de método de pago debe ser un número entero.'],
            ['codigoMetodoPago', 'validateCodigoMetodoPago'],
            ['razonSocial', 'required', 'when' => function ($model) {
                return $model->invoice;
            }, 'message' => 'La razón social es obligatoria cuando hay una factura.'],
            ['razonSocial', 'string', 'max' => 255, 'message' => 'La razón social no puede exceder 255 caracteres.'],
            ['numeroDocumento', 'required', 'when' => function ($model) {
                return $model->invoice;
            }, 'message' => 'El número de documento es obligatorio cuando hay una factura.'],
            ['invoice', 'boolean', 'message' => 'El campo de factura debe ser un valor booleano.'],
            ['invoice', 'validateInvoice'],
            ['numeroDocumento', 'string', 'max' => 20, 'message' => 'El número de documento no puede exceder 20 caracteres.'],
            ['phone', 'string', 'max' => 20, 'message' => 'El número de teléfono no puede exceder 20 caracteres.'],
            ['discountamount', 'number', 'min' => 0, 'message' => 'El descuento debe ser un número mayor o igual a cero.'],
            ['idcustomer', 'integer', 'message' => 'El ID del cliente debe ser un número entero.'],
            ['products', 'required', 'message' => 'El campo Productos no puede estar vacío.'],
            ['products', 'validateProducts'],
            ['total', 'number', 'min' => 0, 'message' => 'El campo Total debe ser un número mayor o igual a cero.'],
            ['numeroTarjeta', 'validateNumeroTarjeta'],
            ['validateCodigoExcepcion', 'required', 'message' => 'Este campo es obligatorio.'],
            ['validateCodigoExcepcion', 'boolean', 'message' => 'El campo de código de excepción debe ser un valor booleano.'],
            ['codigoTipoDocumentoIdentidad', 'number', 'min' => 1, 'when' => function ($model) {
                return $model->invoice && ($model->razonSocial || $model->numeroDocumento);
            }, 'message' => 'El código tipo de documento debe ser un número mayor o igual a uno.'],
            ['number', 'validateNumberExistence'],
            ['idtypeCharge', 'integer', 'message' => 'El tipo de carga debe ser un número entero.'], 
            ['cashOpen', 'validateCaja'],
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
                $this->addError($attribute, 'El número de venta es necesariamente requerido!.');
            } else {
                $sale = Sale::findOne(['number' => $this->$attribute]);
                if ($sale) {
                    $this->addError($attribute, 'El número de venta ' . $sale->number . ' ya existe!.');
                }
            }
        } else {
            $this->number = null;
        }
    }

    // Valida si esa empresa puede emitir factura
    public function validateInvoice($attribute, $params)
    {
        $siatBranch = SiatBranch::findOne(['id' => $ioSystemBranch->id]);
        if ($this->invoice && $siatBranch && $siatBranch->codigoModalidad != 10) {
            $user = Yii::$app->user->identity;
            $this->addError($attribute, "Esta sucursal, con el usuario {$user->name}, no permite realizar facturas con SIAT en esta modalidad.");
        }
    }

    // Obtener los datos de la empresa
    private function getSystemBranch($user)
    {
        $ioSystemBranchUser = IoSystemBranchUser::findOne(['iduserActive' => $user->iduser]);
        if (!$ioSystemBranchUser) {
            return null;
        }

        return IoSystemBranch::findOne(['id' => $ioSystemBranchUser->idioSystemBranch]);
    }

    // Obtener la caja
    private function getCash($user) {
        return Cash::find()
        ->where(['idstatus' => Cash::STATUS_ABIERTO, 'iduser' => $user->id])
        ->one();
    }

    // Verificar el codigo tipo de documento del cliente
    public function validateCodigoTipoDocumento($attribute, $params)
    {
        if (!SiatTipoDocumentoIdentidad::findOne($this->$attribute)) {
            $this->addError($attribute, 'El codigo tipo de documento no es válido.');
        }
    }

    // valida el formato de una tarjeta
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

    // Metodo de pago 
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

            if ($this->ioSystemBranch->allowLot) {
                $this->validateLote($attribute, $index, $product);
            } else if ($this->ioSystemBranch->allowControlInventory) {
                $this->validateControlInventory($attribute, $index, $product);
            }         
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }

    private function validateLote($attribute, $index, $product)
    {
        // Validar si IoSystemBranch->allowLot = true "POR LOTES" deve validar si existe el idproductstock
        if (isset($product['idproductstock']) && !empty($product['idproductstock'])) {
            $existingProduct = Product::findOne(['id' => $product['id']]);
            if ($existingProduct == null) {
                $this->addError($attribute, "El producto con el id " . $product['id'] . " de nombre " . $product['name'] . " en la posición $index no existe en la base de datos.");
            }

            $productStock = Productstock::findOne(['id' => $product['idproductstock']]);
            if ($productStock == null) {
                $this->addError($attribute, "El lote con el id " . $product['idproductstock'] . " en la posición $index no existe en la base de datos.");
            }

            // if ($productStock->idpurchase == null) {
            //     $this->addError($attribute, "El ID " . $product['idproductstock'] . " del lote en la posición $index no es un registro válido.");
            // }

            $quantityInput = is_numeric($productStock->quantityinput) ? $productStock->quantityinput : 0;
            $quantityOutput = is_numeric($productStock->quantityoutput) ? $productStock->quantityoutput : 0;
            $lote = $quantityInput - $quantityOutput;

            if ($lote < $product['quantity']) {
                $this->addError($attribute, "La cantidad del lote es de $lote del " . $existingProduct->name . " y quiere registrar la cantidad de " . $product['quantity']);
            }
        }
    }

    private function validateControlInventory($attribute, $index, $product)
    {
        $existingProduct = null;

        if (isset($product['id']) && $product['id'] != null) {
            if ($existingProduct == null) $existingProduct = Product::findOne($product['id']);
            if ($existingProduct == null) {
                $this->addError($attribute, "El producto con el id " . $product['id'] . " en la posición $index no existe en la base de datos.");
            }

            // CODIGO PARA VERIFICAR EL STOCK 
            $productBranch = ProductBranch::findOne($product['id']);
            if ($productBranch && $productBranch->controlInventory) { // si esta activo el control de invetario
                // si no se ha proporcionado el idstore obtenemos el por defecto
                if (!isset($product['idstore']) && empty($product['idstore'])) {
                    $store = Store::find()->orderBy(['id' => SORT_ASC])->one();
                    $product['idstore'] = $store->id;
                } else {
                    $store = Store::findOne($product['idstore']);
                }

                // Buscar el registro de ProductStore específico por id y idstore
                $productStore = ProductStore::findOne(['id' => $product['id'], 'idstore' => $store->id]);
                if ($productStore) {
                    if ($productStore->stock < $product['quantity']) {  // Verificar si hay suficiente stock para la cantidad solicitada
                        $this->addError($attribute, 'El stock del producto en el almacen "' . $store->name . '" para el producto "' . $product['name'] . '" es de "' . $productStore->stock . '" y quiere registrar la cantidad de ' . $product['quantity']);
                    }
                } else {
                    $this->addError($attribute, "No se encontró el registro del producto en la tienda ID " . $product['idstore']);
                }
            }
            // if (isset($product['idstore']) && !empty($product['idstore'])) {  // Verificar si se proporciona un idStore
            //     // Buscar el registro de ProductStore específico por id y idstore
            //     $productStore = ProductStore::findOne(['id' => $product['id'], 'idstore' => $product['idstore']]);
            //     if ($productStore) {
            //         if ($productStore->stock < $product['quantity']) {  // Verificar si hay suficiente stock para la cantidad solicitada
            //             $this->addError($attribute, "El stock en la tienda ID " . $product['idstore'] . " para el producto " . $product['name'] . " es de " . $productStore->stock . " y quiere registrar la cantidad de " . $product['quantity']);
            //         }
            //     } else {
            //         $this->addError($attribute, "No se encontró el registro del producto en la tienda ID " . $product['idstore']);
            //     }
            // } else {                        
            //     // Si no se proporciona idstore, verificar el stock total del producto sumando todos los registros de ProductStore
            //     $productStores = ProductStore::findAll(['id' => $product['id']]);
            //     $totalStock = 0;
            //     foreach ($productStores as $productStore) {
            //         $totalStock += floatval($productStore->stock);
            //     }
            //     if ($totalStock < $product['quantity']) {
            //         $this->addError($attribute, "El stock total del producto " . $product['name'] . " es de " . $totalStock . " y quiere registrar la cantidad de " . $product['quantity']);
            //     }
            // }
        }
    }
}