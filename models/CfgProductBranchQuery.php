<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgProductBranch]].
 *
 * @see CfgProductBranch
 */
class CfgProductBranchQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgProductBranch[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgProductBranch|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
