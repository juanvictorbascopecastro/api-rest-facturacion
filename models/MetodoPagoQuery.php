<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MetodoPago]].
 *
 * @see MetodoPago
 */
class MetodoPagoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return MetodoPago[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MetodoPago|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
