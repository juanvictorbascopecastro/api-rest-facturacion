<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgIoSystemBranchUser]].
 *
 * @see CfgIoSystemBranchUser
 */
class CfgIoSystemBranchUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystemBranchUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
