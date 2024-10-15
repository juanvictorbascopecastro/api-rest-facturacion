<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\ViewProduct;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\modules\apiv1\controllers\BaseController;

use yii\db\Expression;
use yii\db\Query;
use app\models\ProductStore;
use app\models\Status;
use app\models\UserSystemPoint;

class ViewproductController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\ViewProduct';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [ 
                'index' => ['GET'],
                'control-inventory' => ['GET'],
                'search-by-name' => ['GET'],
                'search-control-inventory' => ['GET'],
                'stock' => ['GET'],
                'all' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
         // Define la consulta para obtener los registros
         $query = ViewProduct::find()->where(['idstatus' => (new Status)->ACTIVO]);
    
         // Configura el ActiveDataProvider con la consulta y la paginación
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => Yii::$app->request->get('pageSize', 10), // Tamaño de página por defecto es 10
             ],
         ]);
     
         // Asegúrate de que los datos se carguen antes de acceder a totalCount
         $dataProvider->prepare();
     
         // Calcula el total de páginas
         $totalCount = $dataProvider->getTotalCount();
         $pageSize = $dataProvider->pagination->pageSize;
         $pageCount = ($totalCount > 0 && $pageSize > 0) ? ceil($totalCount / $pageSize) : 0;
     
         // Retorna la información paginada
         return [
             'totalCount' => $totalCount,
             'pageCount' => $pageCount,
             'currentPage' => $dataProvider->pagination->page + 1, // Se suma 1 porque las páginas están basadas en cero
             'pageSize' => $pageSize,
             'data' => $dataProvider->getModels(),
         ];
    } 
    // productos que tienen control de inventario
    public function actionControlInventory()
    {
        try {
            $user = Yii::$app->user->identity;
            // optenemos los accesos a los usuarios
            $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);
            if($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreSale)) {
                $subQuery = (new Query())
                    ->select(['id', 'totalStock' => 'SUM(stock)'])
                    ->where(['idstore' => $modelUserSystemPoint->idstoreSale])
                    ->from(ProductStore::tableName())
                    ->groupBy('id');
            } else {
                $subQuery = (new Query())
                    ->select(['id', 'totalStock' => 'SUM(stock)'])
                    ->from(ProductStore::tableName())
                    ->groupBy('id');
            }

            // $subQuery = (new Query())
            //     ->select(['id', 'totalStock' => 'SUM(stock)'])
            //     ->from(ProductStore::tableName())
            //     ->groupBy('id');

            $query = ViewProduct::find()
                ->alias('vp')
                ->select(['vp.*',  'sub.totalStock'])
                ->innerJoinWith('productBranch pb', false)
                ->leftJoin(['sub' => $subQuery], 'sub.id = vp.id')
                ->where(['pb.controlInventory' => true])
                ->orderBy(['sub.totalStock' => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->request->get('pageSize', 10), // Tamaño de página por defecto es 10
                ],
            ]);
            
            $dataProvider->prepare();
    
            // Calcula el total de páginas
            $totalCount = $dataProvider->getTotalCount();
            $pageSize = $dataProvider->pagination->pageSize;
            $pageCount = ($totalCount > 0 && $pageSize > 0) ? ceil($totalCount / $pageSize) : 0;
    
            // Retorna la información paginada
            return [
                'totalCount' => $totalCount,
                'pageCount' => $pageCount,
                'currentPage' => $dataProvider->pagination->page + 1, // Se suma 1 porque las páginas están basadas en cero
                'pageSize' => $pageSize,
                'data' => $dataProvider->getModels(),
            ];
        } catch (\Exception $e) {
            return parent::sendResponse([
                'statusCode' => 400, 
                'message' => 'No se pudo listar los productos', 
                'errors' => $e->getMessage()
            ]);
        }
    }
    
    public function actionSearchByName($name)
    {
        // Divide el término de búsqueda en palabras individuales
        $keywords = explode(' ', trim($name));
        $query = ViewProduct::find()->where(['idstatus' => (new Status)->ACTIVO]);
        // Agrega una condición para cada palabra clave
        foreach ($keywords as $keyword) {
            if (!empty($keyword)) {
                $query->andWhere(['ILIKE', 'name', $keyword]);
            }
        }

        $query->limit(40);
        return $query->all();
    }
    // public function actionSearchByName($name)
    // {
    //     $query = ViewProduct::find()
    //         ->where(['ILIKE', 'name', $name])
    //         ->limit(50)
    //         ->all();
            
    //     return $query;
    // }

    public function actionSearchControlInventory($name)
    {
        try {
            $keywords = explode(' ', trim($name));

            $query = ViewProduct::find()
                ->innerJoinWith('productBranch')
                ->where(['productBranch.controlInventory' => true])
                ->orderBy(['name' => SORT_ASC]);
        
            // Agrega una condición para cada palabra clave
            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $query->andWhere(['ILIKE', 'name', $keyword]);
                }
            }
            return $query->all();

        } catch (\Exception $e) {
            return parent::sendResponse([
                'statusCode' => 400, 
                'message' => 'No se pudo listar los productos', 
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function actionStock ($id) {
        $product = ViewProduct::findOne($id);
        if (!$product) {
            return parent::sendResponse([
                'statusCode' => 404, 
                'message' => "Product with ID $id not found.", 
            ]);
        }

        $stock = ProductStore::find()
            ->alias('ps')
            ->leftJoin('cfg.store s', 'ps.idstore = s.id')
            ->where(['ps.id' => $id])
            ->all();

        $response = [];
        foreach ($stock as $item) {
            $response[] = [
                'id' => $item->id,
                'idstore' => $item->idstore,
                'dateCreate' => $item->dateCreate,
                'recycleBin' => $item->recycleBin,
                'iduser' => $item->iduser,
                'stock' => (float) $item->stock,
                'stockReserved' => (float) $item->stockReserved,
                'allow' => $item->allow,
                'store' => $item->store,
            ];
        }

        if (empty($response)) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "No stock information found for Product ID $id.",
            ]);
        }

        return $response;
    }

    public function actionAll()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['idstatus' => (new Status)->ACTIVO])
                ->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
    } 
}
