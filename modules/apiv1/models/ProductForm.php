<?php
namespace app\modules\apiv1\models;

use Yii;
use yii\base\Model;
use app\modules\apiv1\models\Unit;
use app\models\Category; 

class ProductForm extends Model {
    public $idcategory;
    public $idunit;
    public $name;
    public $price;
    public $barcode;
    public $recycleBin;
    public $idstatus;
    public $description;
    public $codigoProducto;

    public function rules()
    {
        return [
            [['name', 'price', 'codigoProducto'], 'required'],
            [['price', 'codigoProducto', 'idstatus'], 'number'],
            [['idstatus'], 'default', 'value' => 1],
            [['recycleBin'], 'default', 'value' => false],
            [['idunit'], 'validateIdunit'],
            [['idcategory'], 'validateCategory'],
        ];
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
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->validateIdunit('idunit', []);

        return true;
    }
}