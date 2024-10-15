<?php

namespace app\modules\service\models\dto;

use Yii;
use yii\base\Model;
use app\models\SincronizarListaProductosServicios;
use app\models\SiatUnidadMedida;
use app\models\SiatTipoDocumentoIdentidad;
use app\models\SiatTipoMetodoPago;

use app\modules\apiv1\helpers\DataCompany;

class SaleDTO extends Model
{
    public $razonSocial; // string
    public $numeroDocumento; // string
    public $phone; // string
    public $codigoTipoDocumentoIdentidad; // int
    public $codigoMetodoPago; // int
    public $validateCodigoExcepcion; // boolean
    public $products; // array de objetos
    public $numeroTarjeta;
    public $total;
    public $discountamount;
    public $ioSystemBranch; // configuracion de la empresa
    public $idtypeCharge; // parametro 1 si es al contado, 2 si es al credito

    public function __construct($config = [])
    {
        parent::__construct($config);

        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = DataCompany::getSystemBranch($user);
        if ($this->idtypeCharge === null || !isset($this->idtypeCharge)) {
            $this->idtypeCharge = 1;
        }
    }

    public function rules()
    {
        return [
            [['razonSocial', 'numeroDocumento', 'phone', 'codigoTipoDocumentoIdentidad', 'codigoMetodoPago', 'validateCodigoExcepcion', 'products', 'total'], 'safe'],
            [['codigoTipoDocumentoIdentidad', 'codigoMetodoPago'], 'required', 'message' => 'Este campo es obligatorio.'],
            [['codigoTipoDocumentoIdentidad', 'codigoMetodoPago'], 'integer', 'message' => 'Este campo debe ser un número entero.'],
            ['razonSocial', 'required', 'message' => 'El campo Razón Social es obligatorio.'],
            ['numeroDocumento', 'required', 'message' => 'El campo Número de Documento es obligatorio.'],
            ['razonSocial', 'string', 'max' => 255, 'message' => 'La Razón Social no puede exceder los 255 caracteres.'],
            ['numeroDocumento', 'string', 'max' => 20, 'message' => 'El Número de Documento no puede exceder los 20 caracteres.'],
            ['phone', 'string', 'max' => 20, 'message' => 'El Teléfono no puede exceder los 20 caracteres.'],
            ['total', 'number', 'min' => 0, 'message' => 'El Total debe ser un número mayor o igual a cero.'],
            ['products', 'required', 'message' => 'El campo Productos no puede estar vacío.'],
            ['products', 'validateProducts', 'message' => 'Los productos contienen errores de validación.'],
            ['codigoTipoDocumentoIdentidad', 'validateCodigoTipoDocumento', 'message' => 'El código tipo de documento no es válido.'],
            ['codigoMetodoPago', 'validateCodigoMetodoPago', 'message' => 'El código de método de pago no es válido.'],
            ['numeroTarjeta', 'required', 'when' => function ($model) {
                return $this->shouldRequestCardNumber($model->codigoMetodoPago);
            }, 'message' => 'El número de tarjeta es obligatorio para este método de pago.'],            
            ['numeroTarjeta', 'validateNumeroTarjeta'],
        ];
    }

    // Verificar el codigo tipo de documento del cliente
    public function validateCodigoTipoDocumento($attribute, $params)
    {
        if (!SiatTipoDocumentoIdentidad::findOne(['codigoClasificador' => $this->$attribute])) {
            $this->addError($attribute, 'El código tipo de documento no es válido.');
        }
    }

    // Verificar si el número de tarjeta es requerido
    protected function shouldRequestCardNumber($codigoMetodoPago)
    {
        $metodoPago = SiatTipoMetodoPago::findOne($codigoMetodoPago);
        if ($metodoPago && stripos($metodoPago->descripcion, 'tarjeta') !== false) {
            return true;
        }
        return false;
    }

    // valida el formato de una tarjeta
    public function validateNumeroTarjeta($attribute, $params)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return;
        }

        // Expresión regular para el formato 1234-xxxx-xxxx-5678
        $pattern = '/^\d{4}-xxxx-xxxx-\d{4}$/';

        if (!preg_match($pattern, $this->$attribute)) {
            $this->addError($attribute, 'El formato del número de tarjeta no es válido para SIAT. Debe tener el siguiente formato "1234-xxxx-xxxx-7890"');
        }
    }

    // Metodo de pago 
    public function validateCodigoMetodoPago($attribute, $params)
    {
        if (!SiatTipoMetodoPago::findOne(["codigoClasificador" => $this->$attribute])) {
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
            if (
                !isset($product['name']) || 
                !isset($product['quantity']) || 
                !isset($product['price']) || 
                !isset($product['siatCodigoUnidadMedida']) || 
                !isset($product['siatCodigoProducto']) || 
                !isset($product['codigoProducto'])) {
                $this->addError($attribute, "El producto en la posición $index debe tener los campos 'name', 'quantity', 'price', 'siatCodigoUnidadMedida', 'siatCodigoProducto', 'codigoProducto'.");
                continue;
            }

            if (empty($product['name'])) {
                $this->addError($attribute, "El campo 'name' del producto en la posición $index no debe estar vacio.");
            }

            if (empty($product['siatCodigoProducto'])) {
                $this->addError($attribute, "El campo 'siatCodigoProducto' del producto en la posición $index no debe estar vacio.");
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

            if (!is_numeric($product['siatCodigoUnidadMedida']) || $product['siatCodigoUnidadMedida'] <= 0) {
                $this->addError($attribute, "El campo 'siatCodigoUnidadMedida' del producto en la posición $index debe ser un número mayor que cero.");
            }

            // Validar si el id del producto enviado existe en la base de datos
            if (isset($product['siatCodigoUnidadMedida']) && $product['siatCodigoUnidadMedida'] != null) {
                $unitExists = SiatUnidadMedida::find()->where(['codigoClasificador' => $product['siatCodigoUnidadMedida']])->exists();
                if (!$unitExists) {
                    $this->addError($attribute, "El 'siatCodigoUnidadMedida' del producto en la posición $index con el valor " . $product['siatCodigoUnidadMedida'] . " no existe en la base de datos.");
                }
            }

            if (!is_numeric($product['siatCodigoProducto'])) {
                $this->addError($attribute, "El campo 'siatCodigoProducto' del producto en la posición $index debe ser una cadena de texto.");
            }

            if (isset($product['siatCodigoProducto']) && $product['siatCodigoProducto'] != null) {
                $productoServicioExists = SincronizarListaProductosServicios::find()->where(['codigoProducto' => $product['siatCodigoProducto']])->exists();
                if (!$productoServicioExists) {
                    $this->addError($attribute, "El 'siatCodigoProducto' del producto en la posición $index con el valor " . $product['siatCodigoProducto'] . " no existe en la base de datos.");
                }
            }

            // Calcular el total basado en el precio y la cantidad
            $total += $product['quantity'] * $product['price'];
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }
}
