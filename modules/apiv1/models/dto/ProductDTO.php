<?php
namespace app\modules\apiv1\models\dto;

use Yii;
use yii\base\Model;
use app\modules\apiv1\models\Unit;
use app\models\Category; 
use app\models\UserSystemPoint;
use app\models\Product;

use app\modules\apiv1\helpers\DataCompany;

class ProductDTO extends Model {
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $idcategory;
    public $idunit;
    public $name;
    public $price;
    public $barcode;
    public $recycleBin;
    public $idstatus;
    public $description;
    public $codigoProducto;
    public $controlInventory;
    public $enableSale;
    public $cost;
    public $code;
    public $ioSystemBranch; // configuracion de la empresa

    public function __construct($config = [])
    {
        parent::__construct($config);

        $user = Yii::$app->user->identity;
        $this->ioSystemBranch = DataCompany::getSystemBranch($user);
    }

    public function rules()
    {
        return [
            [['name', 'price'], 'required', 'message' => 'El campo {attribute} es obligatorio.'],
            [['codigoProducto', 'controlInventory', 'enableSale'], 'required', 'on' => self::SCENARIO_CREATE, 'message' => 'El campo {attribute} es obligatorio en la creación.'],
            [['price'], 'number', 'message' => 'El campo {attribute} debe ser un número válido.'],
            [['codigoProducto'], 'string'],
            [['recycleBin'], 'default', 'value' => false, 'message' => 'El valor predeterminado de la papelera de reciclaje es false.'],
            [['idunit'], 'validateIdunit', 'message' => 'La unidad proporcionada no es válida.'],
            [['idcategory'], 'validateCategory', 'message' => 'La categoría proporcionada no es válida.'],
            [['controlInventory', 'enableSale'], 'boolean', 'message' => 'El campo {attribute} debe ser verdadero o falso.'],
            [['cost'], 'number', 'message' => 'El campo {attribute} debe ser un número válido.'],

            // Validación de 'code' usando un método separado
            [['code'], 'required', 'on' => self::SCENARIO_CREATE, 'when' => [$this, 'isCodeRequired'], 'message' => 'El campo "code" es obligatorio!'],
            [['code'], 'validateUniqueCode'],
          ];
    }
    
    public function isCodeRequired()
    {
        return !$this->ioSystemBranch->cfgIoSystem->productCodeAuto;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['idcategory', 'idunit', 'name', 'price', 'barcode', 'recycleBin', 'description', 'codigoProducto', 'controlInventory', 'enableSale', 'cost', 'code'];
        $scenarios[self::SCENARIO_UPDATE] = ['idcategory', 'idunit', 'name', 'price', 'barcode', 'recycleBin', 'description', 'controlInventory', 'enableSale', 'cost', 'code']; // Debe incluir 'code'
        return $scenarios;
    }

    public function validateUniqueCode($attribute, $params)
    {
        if(!$this->ioSystemBranch->cfgIoSystem->productCodeAuto && $this->scenario === self::SCENARIO_CREATE) {
            $product = Product::find()->where(['code' => $this->code])->one();

            if ($product !== null) {
                $this->addError($attribute, "El código '{$this->code}' ya está asignado a otro producto.");
            }
        }
    }

    public function validateIdunit($attribute, $params)
    {
        if ($this->idunit === null) {
            $unit = Unit::find()
                ->where(['is not', 'order', null])
                ->orderBy(['order' => SORT_ASC])
                ->one();

            if ($unit !== null) {
                $this->idunit = $unit->id;
            }
        }
    }

    public function validateCategory($attribute, $params)
    {
        if ($this->idcategory !== null) {
            $category = Category::findOne($this->idcategory);

            if ($category === null) {
                $this->addError($attribute, 'El ID de categoría proporcionado no es válido.');
            }
        }
    }

    public function beforeValidate()
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            if ($this->cost === null) {
                $this->cost = 0; // Establece cost a 0 si es nulo en la creación
            }
        } elseif ($this->scenario === self::SCENARIO_UPDATE) {
            if ($this->cost === null) {
                $this->cost = null; 
            }
        }

        // $user = Yii::$app->user->identity;
        // $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);
        // if($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreMain) && $modelUserSystemPoint->idstoreMain > 1) {
        //     $this->addError($attribute, 'Este usuario no puede modificar productos!');
        // }

        if (!parent::beforeValidate()) {
            return false;
        }

        $this->validateIdunit('idunit', []);

        return true;
    }
}
