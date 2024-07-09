<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SiatUnidadMedida]].
 *
 * @see SiatUnidadMedida
 */
class SiatUnidadMedidaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SiatUnidadMedida[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SiatUnidadMedida|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
