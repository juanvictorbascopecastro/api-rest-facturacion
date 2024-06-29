<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\helpers\DbConnection;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use app\modules\apiv1\controllers\BaseController; 
use yii\web\NotFoundHttpException;

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
        $this->prepareData();
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()->orderBy(['id' => SORT_ASC]),
            'pagination' => false,
        ]);
    }


    public function actionInsert()
    {
        $this->prepareData();

        $user = Yii::$app->user->identity;
        $customer = new Customer();
        $customer->attributes = Yii::$app->request->post();
        $customer->iduser = $user->iduser; 
        if ($customer->save()) {
            $customer = Customer::findOne($customer->id);
            return ['status' => 201, 'message' => 'Customer created successfully', 'data' => $category];
        } else {
            return ['status' => 400, 'message' => 'Failed to create category', 'errors' => $category->errors];
        }
    }

    public function actionEdit($id)
    {
        $this->prepareData();

        $customer = Customer::findOne($id);
        if (!$customer) {
            throw new NotFoundHttpException("Customer with ID $id not found.");
        }

        $customer->attributes = Yii::$app->request->post();
        if ($customer->validate()) {
            if ($customer->save()) {
                return ['status' => 201, 'message' => 'Customer updated successfully', 'data' => $category];
            } else {
                return ['status' => 400, 'message' => 'Failed to update category', 'errors' => $category->errors];
            }
        } else {
            return ['status' => 400, 'message' => 'Validation failed', 'errors' => $category->errors];
        }
    }

    public function actionRemove($id)
    {
        $this->prepareData();

        $customer = Customer::findOne($id);
        if (!$customer) {
            throw new NotFoundHttpException("Customer with ID $id not found.");
        }

        if ($customer->delete()) {
            return ['status' => 201, 'message' => 'Customer deleted successfully'];
        } else {
            return ['status' => 400, 'message' => 'Failed to delete customer'];
        }
    }
    public function actionSearchByDoc($doc)
    {
        $this->prepareData();

        $query = Customer::find()
            ->where(['ILIKE', 'numeroDocumento', $doc])
            ->limit(20)
            ->all();

        return $query;
    }

    public function actionSearchByName($name)
    {
        $this->prepareData();

        $query = Customer::find()
            ->where(['ILIKE', 'razonSocial', $name])
            ->limit(20)
            ->all();
            
        return $query;
    }
}
