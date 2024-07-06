<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ProductStore]].
 *
 * @see ProductStore
 */
class ProductStoreQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductStore[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductStore|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
