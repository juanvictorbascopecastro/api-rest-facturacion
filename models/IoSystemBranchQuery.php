<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgIoSystemBranch]].
 *
 * @see CfgIoSystemBranch
 */
class IoSystemBranchQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranch[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranch|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
