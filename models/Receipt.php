<?php

namespace app\models;

use Yii;

class Receipt extends \yii\db\ActiveRecord
{
   
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->iooxsBranch;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['iduser', 'idcustomer', 'idstatus', 'number', 'codigoMetodoPago'], 'integer'],
            [['montoTotal'], 'number'],
            [['comment'], 'string'],
            [['number'], 'integer'],
            [['recycleBin'], 'default', 'value' => false],
            [['iduser'], 'default', 'value' => 1],
            [['codigoMetodoPago'], 'default', 'value' => 1],
            
            [['idsale'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Created',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'User ID',
            'idcustomer' => 'Customer ID',
            'idstatus' => 'Status ID',
            'comment' => 'Comment',
            'number' => 'Number',
            'montoTotal' => 'Total Amount',
            'codigoMetodoPago' => 'Payment Method Code',
        ];
    }

    public static function find()
    {
        return new ReceiptQuery(get_called_class());
    }

    public function beforeSave($insert) {
        
        if ($this->scenario == 'default') {
            $this->idstatus = 40;
        }

        $this->iduser = Yii::$app->user->getId();

        if(!$this->number) {
            $this->setNumber();
        }
        
        

        return true;
    }

    public function setNumber() {
        $q = 'select max(number) from receipt where "recycleBin"=false';
        $command = Yii::$app->iooxsBranch->createCommand($q);
        $number = $command->queryScalar();
        $number = $number == null ? 1 : $number + 1;

        $this->number = $number;
    }
}
