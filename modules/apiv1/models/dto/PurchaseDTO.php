<?php

namespace app\modules\apiv1\models\dto;

use Yii;
use yii\base\Model;
use app\models\Product;
use app\models\Vendor;
use app\models\Store;
use app\models\ProductBranch;
use app\models\UserSystemPoint;

class PurchaseDTO extends Model
{
    public $nameVendor; 
    public $numeroDocumento; 
    public $products; 
    public $discountpercentage;
    public $discountamount; 
    public $total; 
    public $idvendor; 
    public $idstore;
    public $numeroFactura; 
    public $attachedDocument; 
    public $comment; 

    public function rules()
    {
        return [
            [['nameVendor', 'idvendor', 'idstore', 'products'], 'required'],
            [['numeroDocumento', 'numeroFactura', 'attachedDocument', 'comment'], 'string'],
            [['products'], 'validateProductsArray'],
            ['discountpercentage', 'number', 'min' => 0, 'max' => 100],
            ['discountamount', 'number', 'min' => 0],
            ['discountpercentage', 'default', 'value' => 0],
            ['discountamount', 'default', 'value' => 0],
            ['total', 'number'],
            ['idvendor', 'validateVendor'],
            ['idstore', 'validateStore'],
        ];
    }

    public function validateVendor($attribute, $params)
    {
        if (!Vendor::findOne($this->$attribute)) {
            $this->addError($attribute, 'El código del proveedor no es válido.');
        }
    }

    public function validateStore($attribute, $params)
    {
        if (!Store::findOne($this->$attribute)) {
            $this->addError($attribute, 'El id del almacén no es válido.');
        }

        // $user = Yii::$app->user->identity;
        // // Verificar si ese usuario esta restringido para vender de un almacen en espesifico
        // // en caso de que el idstoreMain = null este usuario no tiene restricciones
        // $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);
        // if($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreMain)) {
        //     // $this->idstore = $modelUserSystemPoint->idstoreMain;
        //     $this->addError($attribute, 'Este usuario no puede realizar una compra!');
        // }
    }

    public function validateProductsArray($attribute, $params)
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'El campo Productos debe ser un array.');
            return;
        }

        if (count($this->$attribute) == 0) {
            $this->addError($attribute, 'El campo Productos debe contener al menos un producto.');
            return;
        }

        $total = 0;
        foreach ($this->$attribute as $index => $product) {
            if (!isset($product['quantity']) || !isset($product['cost']) || !isset($product['id'])) {
                $this->addError($attribute, "El producto en la posición $index debe tener los campos 'id', 'quantity' y 'cost'.");
                continue;
            }

            if (!is_numeric($product['quantity']) || $product['quantity'] <= 0) {
                $this->addError($attribute, "El campo 'quantity' del producto en la posición $index debe ser un número mayor que cero.");
            }

            if (!is_numeric($product['cost']) || $product['cost'] <= 0) {
                $this->addError($attribute, "El campo 'cost' del producto en la posición $index debe ser un número mayor que cero.");
            }
    
            $total += $product['quantity'] * $product['cost'];

            // validar si el id del producto enviado existe en la base de datos
            $existingProduct = Product::findOne($product['id']);
            if (empty($existingProduct)) {
                $this->addError($attribute, "El producto con el id {$product['id']} en la posición $index no existe en la base de datos.");
            }
 
            $productBranch = ProductBranch::findOne($product['id']);
            if (empty($productBranch)) {
                $this->addError($attribute, "El producto con el id {$product['id']} en la posición $index no no tiene la configuracion para control de inventario.");
            }

            if(empty($productBranch->controlInventory)) {
                $this->addError($attribute, "El producto con el id {$product['id']} en la posición $index no tiene activo el control de inventario");
            }
        }

        // Asignar el total calculado al atributo total
        $this->total = $total;
    }
}