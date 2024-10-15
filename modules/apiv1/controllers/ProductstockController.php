<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\models\ViewProduct;
use app\modules\apiv1\models\ProductStockResponse;
use app\modules\apiv1\models\ProductLot;
use app\modules\apiv1\models\Productstock;
use app\modules\apiv1\models\SaleProductStock;
use app\modules\apiv1\models\PurchaseProductStock;
use app\models\Document;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController;

class ProductstockController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Productstock';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [ 
                'index' => ['GET'],
                'lote' => ['GET'],
                'search-lote' => ['GET'],
                'lote-by-id' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        $idproduct = Yii::$app->request->get('idproduct');
        $product = ViewProduct::findOne($idproduct);

        if (!$product) {
            return parent::sendResponse([
                'statusCode' => 404, 
                'message' => "Product with ID $idproduct not found."
            ]);
        }

        $query = Productstock::find()
            ->where(['idproduct' => $idproduct])
            ->joinWith(['sale', 'purchase', 'itemDocument'])
            ->orderBy(['dateCreate' => SORT_ASC]);

        $productStocks = $query->all();

        $response = array_map(function ($productStock) {
            return [
                'id' => $productStock->id,
                'dateCreate' => $productStock->dateCreate,
                'recycleBin' => $productStock->recycleBin,
                'iddocument' => $productStock->iddocument,
                'idsale' => $productStock->idsale,
                'idpurchase' => $productStock->idpurchase,
                'idproduct' => $productStock->idproduct,
                'quantityinput' => floatval($productStock->quantityinput),
                'quantityoutput' => floatval($productStock->quantityoutput),
                'cost' => floatval($productStock->cost),
                'price' => floatval($productStock->price),
                'nprocess' => $productStock->nprocess,
                'iduser' => $productStock->iduser,
                'comment' => $productStock->comment,
                'montoDescuento' => floatval($productStock->montoDescuento),
                'idstore' => $productStock->idstore,
                'idproductionOrder' => $productStock->idproductionOrder,
                // 'sale' => $productStock->getSale()->one() ? new SaleProductStock($productStock->getSale()->one()) : null,
                // 'document' => $productStock->itemDocument ? $productStock->getItemDocument()->one() : null,
                // 'purchase' => $productStock->getPurchase()->one() ? new PurchaseProductStock($productStock->getPurchase()->one()) : null,
                'sale' => $productStock->sale ? new SaleProductStock($productStock->sale) : null,
                'document' => $productStock->itemDocument ? $productStock->itemDocument : null,
                'purchase' => $productStock->purchase ? new PurchaseProductStock($productStock->purchase) : null,
            ];
        }, $productStocks);

        return $response;
    }

    public function actionLote()
    {
        try {
            $query = ProductLot::find()
                ->alias('pl') // Alias para ProductLot
                ->joinWith([
                    'viewProduct vp', // Alias para viewProduct
                    'viewProduct.productBranch pb', // Alias para productBranch
                   // 'viewProduct.productStores ps', // Alias para productStores
                    'viewProduct.productImages pi' // Alias para productImages  
                ])
                ->where(['>', new \yii\db\Expression('pl.quantityinput - pl.quantityoutput'), 0])
                // ->andWhere(['is not', 'pl.idpurchase', null])
                ->andWhere(['pb.controlInventory' => true])
                // Aquí usa ps.idstore para evitar la ambigüedad
                // ->andWhere(['ps.idstore' => 1]) // Especifica el alias 'ps' para evitar ambigüedades
                ->orderBy(['pl.dateCreate' => SORT_DESC]);
    
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->request->get('pageSize', 10),
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
                'currentPage' => $dataProvider->pagination->page + 1,
                'pageSize' => $pageSize,
                'data' => $dataProvider->getModels(),
            ];
    
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            parent::sendResponse([
                'statusCode' => 400, 
                'message' => 'No se pudo listar los productos.',
                'errors' => $e->getMessage()
            ]);
        }
    }
    
    public function actionSearchLote($name)
    {
        try {
            $keywords = explode(' ', trim($name));
            $query = ProductLot::find()
                ->joinWith([
                    'viewProduct.productBranch',
                    //'viewProduct.productStores',
                    'viewProduct.productImages'
                ])
                ->where(['>', new \yii\db\Expression('productstock.quantityinput - productstock.quantityoutput'), 0]);
                // ->andWhere(['is not', 'productstock.idpurchase', null]);
            // $query->andWhere(['ILIKE', 'viewProduct.name', $name]);
            // Agrega condiciones para cada palabra clave
            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $query->andWhere(['ILIKE', 'viewProduct.name', $keyword]);
                }
            }

            $query->orderBy(['productstock.dateCreate' => SORT_DESC]);
            $query->limit(50); // Limita la búsqueda a 50 resultados

            return $query->all();

        } catch (\Exception $e) {
            parent::sendResponse([
                'statusCode' => 400, 
                'message' => 'No se pudo realizar la búsqueda.',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function actionLoteById($id)
    {
        try {
            $query = ProductLot::find()
                ->joinWith([
                    'viewProduct.productBranch',
                    'viewProduct.productStores',
                    'viewProduct.productImages'
                ])
                ->where(['productstock.id' => $id])
                ->andWhere(['>', new \yii\db\Expression('productstock.quantityinput - productstock.quantityoutput'), 0])
                ->orderBy(['productstock.dateCreate' => SORT_DESC]);
    
            $model = $query->one();
    
            if ($model === null) {
                return $this->asJson([
                    'statusCode' => 404,
                    'message' => 'Lote no encontrado.',
                ]);
            }
    
            return $this->asJson($model);
    
        } catch (\Exception $e) {
            parent::sendResponse([
                'statusCode' => 400, 
                'message' => 'No se pudo realizar la búsqueda.',
                'errors' => $e->getMessage()
            ]);
            
        }
    }
}


