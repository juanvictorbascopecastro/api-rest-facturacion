<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 

use sizeg\jwt\Jwt;

class CustomerController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\Customer';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'actionListar'];   // personalizar metodo "actionListar" sera el actual ahora

        return $actions;
    }

    public function actionListar()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
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

    public function actionRemove($id)
    {
        $customer = Customer::findOne($id);
        if (!$customer) {
            return parent::sendResponse([
                'message' => "Customer with ID $id not found.",
                'statusCode' => 404
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
        $query = Customer::find()
            ->where(['ILIKE', 'numeroDocumento', $doc])
            ->limit(20)
            ->all();

        return $query;
    }

    public function actionSearchByName($name)
    {
        $query = Customer::find()
            ->where(['ILIKE', 'razonSocial', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
