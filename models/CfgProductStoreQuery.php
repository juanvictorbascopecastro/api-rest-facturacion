<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgProductStore]].
 *
 * @see CfgProductStore
 */
class CfgProductStoreQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgProductStore[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgProductStore|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
