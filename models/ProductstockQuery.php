<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Productstock]].
 *
 * @see Productstock
 */
class ProductstockQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Productstock[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Productstock|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
