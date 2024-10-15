<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Productimage]].
 *
 * @see Productimage
 */
class ProductimageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Productimage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Productimage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
