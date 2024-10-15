<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $id
 * @property string|null $status
 * @property string|null $description
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 */
class Status extends \yii\db\ActiveRecord
{
       
    public $INICIAR = 1;
    public $INICIADO = 2;
    public $ABIERTO = 3;
    public $EN_ESPERA = 4;
    public $PENDIENTE = 5;
    public $ACTIVO = 10;
    public $ACEPTADO = 15;
    public $PROGRAMADO = 19;
    public $PROCESO_INICIADO = 20;
    public $SIN_RESERVA = 25;
    public $RESERVADO = 26;
    public $EN_PROCESO = 30;
    public $PROCESADO = 40;
    public $CERRADO = 55;
    public $INACTIVO = 60;
    public $UNIDO_A_MESA = 70;
    public $ANULADO = 80;
    public $FINALIZADO = 100;
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxs_io');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'iduser'], 'default', 'value' => null],
            [['id', 'iduser'], 'integer'],
            [['description'], 'string'],
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['status'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'description' => 'Description',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'iduser' => 'Iduser',
        ];
    }
}
