<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Customer;
use app\models\Sale;
use app\models\Receipt;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 

use app\models\UserSystemPoint;

use sizeg\jwt\Jwt;

class CustomerController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Customer';
   
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbFilter'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],        
                'insert' => ['POST'],     
                'edit' => ['PUT', 'PATCH'], 
                'remove' => ['DELETE'],   
                'by-id' => ['GET'],
                'search-by-doc' => ['GET'],
                'search-by-name' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $userSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);

        if($userSystemPoint->ownerIduser) {
            $query = $this->modelClass::find()->where(['iduser' => $user->iduser])->orderBy(['id' => SORT_ASC]);
        } else {
            $query = $this->modelClass::find()->orderBy(['id' => SORT_ASC]);
        }
        // $query = $this->modelClass::find()->orderBy(['id' => SORT_ASC]);

        // Configuracion de ActiveDataProvider con la consulta y la paginación
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('pageSize', 10), // Tamaño de página por defecto es 10
            ],
        ]);
    
        // cargar datos
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

    public function actionInsert()
    {
        $user = Yii::$app->user->identity;
        $customer = new Customer();
        $customer->attributes = Yii::$app->request->post();
        $customer->iduser = $user->iduser; 
        if ($customer->save()) {
            $customer = Customer::findOne($customer->id);
            return parent::sendResponse([
                'message' => 'Customer created successfully',
                'statusCode' => 201,
                'data' => $customer
            ]);
        } else {
            return parent::sendResponse([
                'message' => 'Failed to create customer',
                'statusCode' => 400,
                'errors' => $customer->errors
            ]);
        }
    }

    public function actionEdit($id)
    {
        $customer = Customer::findOne($id);
        if (!$customer) {
            return parent::sendResponse([
                'message' => "Customer with ID $id not found.",
                'statusCode' => 404
            ]);
        }

        $customer->attributes = Yii::$app->request->post();
        if ($customer->validate()) {
            if ($customer->save()) {
                return parent::sendResponse([
                    'message' => 'Customer updated successfully',
                    'statusCode' => 201,
                    'data' => $customer
                ]);
            } else {
                return parent::sendResponse([
                    'message' => 'Failed to update customer',
                    'statusCode' => 400,
                    'errors' => $customer->errors
                ]);
            }
        } else {
            return parent::sendResponse([
                'message' => 'Validation failed',
                'statusCode' => 400,
                'errors' => $customer->errors
            ]);
        }
    }

    public function actionById($id)
    {
        $customer = Customer::findOne($id);
        if (!$customer) {
            return parent::sendResponse([
                'message' => "Customer with ID $id not found.",
                'statusCode' => 404
            ]);
        } return $customer;
    }

    public function actionRemove($id)
    {
        $customer = Customer::findOne($id);
        if (!$customer) {
            return parent::sendResponse([
                'message' => "Customer with ID $id not found.",
                'statusCode' => 404
            ]);
        }

        // si hay ventas a su nombre
        $saleCount = Sale::find()
            ->where(['idcustomer' => $id])
            ->count();
        
        if($saleCount > 0) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => "No es posible eliminar el cliente. Hay " . $saleCount . " registros referente a ventas asignados a este cliente.",
            ]);
        }

        // si hay recinos a su nombre
        $receiptCount = Receipt::find()
            ->where(['idcustomer' => $id])
            ->count();
        
        if($receiptCount > 0) {
            return parent::sendResponse([
                'statusCode' => 400,
                'message' => "No es posible eliminar el cliente. Hay " . $receiptCount . " registros referente a recibos asignados a este cliante.",
            ]);
        }

        if ($customer->delete()) {
            return parent::sendResponse([
                'message' => 'Customer deleted successfully',
                'statusCode' => 201
            ]);
        } else {
            return parent::sendResponse([
                'message' => 'Failed to delete customer',
                'statusCode' => 400
            ]);
        }
    }

    public function actionSearchByDoc($doc)
    {
        $user = Yii::$app->user->identity;
        $userSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);

        if($userSystemPoint->ownerIduser) {
            $query = Customer::find()
                ->where(['ILIKE', 'numeroDocumento', $doc])
                ->andWhere(['iduser' => $user->iduser])
                ->limit(20)
                ->all();
        } else {
            $query = Customer::find()
                ->where(['ILIKE', 'numeroDocumento', $doc])
                ->limit(20)
                ->all();
        }

        return $query;
    }

    public function actionSearchByName($name)
    {
        $user = Yii::$app->user->identity;
        $userSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);

        if($userSystemPoint->ownerIduser) {
            $query = Customer::find()
                ->where(['ILIKE', 'razonSocial', $name])
                ->orWhere(['ILIKE', 'name', $name])
                ->andWhere(['iduser' => $user->iduser])
                ->limit(20)
                ->all();
        } else {
            $query = Customer::find()
                ->where(['ILIKE', 'razonSocial', $name])
                ->orWhere(['ILIKE', 'name', $name])
                ->limit(20)
                ->all();
        }

        return $this->asJson($query);
    }

}
