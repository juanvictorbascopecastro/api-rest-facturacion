<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\Product;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use app\models\CfgIoSystemBranchUser;
use app\modules\apiv1\models\CfgIoSystemBranch;
use app\modules\apiv1\models\ProductStore; // stock de los productos
use app\modules\apiv1\models\ProductBranch; // configuracion de los productos

use sizeg\jwt\Jwt;

class ProductimageController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\ProductImage';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];   // personalizar metodo "actionListar" sera el actual ahora

        return $actions;
    }

    public function actionListar()
    {
        return 'path image';
    }

    public function actionInsert()
    {
       return 'hhh';
    }

    public function actionEdit($id)
    {
        return 'gggg';
    }

    public function actionRemove($id)
    {
        return 'vvv';
    }

    public function actionSearchByName($name)
    {
        $query = Product::find()
            ->where(['ILIKE', 'name', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
