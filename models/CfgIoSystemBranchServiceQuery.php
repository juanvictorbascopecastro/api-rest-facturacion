<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgIoSystemBranchService]].
 *
 * @see CfgIoSystemBranchService
 */
class CfgIoSystemBranchServiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchService[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchService|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
