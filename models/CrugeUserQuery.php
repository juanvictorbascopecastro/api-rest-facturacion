<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CrugeUser]].
 *
 * @see CrugeUser
 */
class CrugeUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CrugeUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CrugeUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
