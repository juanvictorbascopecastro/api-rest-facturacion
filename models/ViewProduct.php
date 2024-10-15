<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ViewProduct extends ActiveRecord
{
    public static function tableName()
    {
        return 'cfg.viewProduct';
    }

    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    public function rules()
    {
        return [
            [
                [
                    'dateCreate', 
                    'recycleBin', 
                    'name', 
                    'tags', 
                    'code', 
                    'barcode', 
                    'idunit', 
                    'idcategory', 
                    'stockcontrol', 
                    'dimensionwidth', 
                    'dimensionlength', 
                    'dimensionheight', 
                    'codeRef', 
                    'weight', 
                    'nameRef', 
                    'idsincronizarListaProductosServicios', 
                    'idstatus', 
                    'iduser', 
                    'description', 
                    'typeBudget', 
                    'price', 
                    'codeSource', 
                    'nameSource', 
                    'idmark', 
                    'activePrinciple', 
                    'rs'
                ], 
                'safe'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
         
        ];
    }

    public static function primaryKey()
    {
        return ['id']; 
    }

    public function getProductImages()
    {
        return $this->hasMany(ViewProductimage::class, ['idproduct' => 'id']);
    }

    public function getProductStores()
    {
        $user = Yii::$app->user->identity;
        $modelUserSystemPoint = UserSystemPoint::findOne(['iduserEnabled' => $user->iduser]);

        if ($modelUserSystemPoint && !empty($modelUserSystemPoint->idstoreMain)) {
            return $this->hasMany(\app\modules\apiv1\models\ProductStore::class, ['id' => 'id'])
                ->andOnCondition(['idstore' => $modelUserSystemPoint->idstoreMain]); // en caso de que el usuario solo esta habilitado a un respectivo almacen
        } else {
            return $this->hasMany(\app\modules\apiv1\models\ProductStore::class, ['id' => 'id']);
        }
    }

    public function getProductBranch()
    {
        return $this->hasOne(\app\modules\apiv1\models\ProductBranch::class, ['id' => 'id']);
    }
}
