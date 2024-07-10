<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CfgIoSystem]].
 *
 * @see CfgIoSystem
 */
class IoSystemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CfgIoSystem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CfgIoSystem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
