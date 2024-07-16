<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.systemPoint".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $codigoPuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property int|null $idsiatBranch
 * @property int|null $codigoAmbiente CIAT wsdl [registroPuntoVenta]
 * @property int|null $codigoTipoPuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property string|null $descripcion CIAT wsdl [registroPuntoVenta]
 * @property string|null $nombrePuntoVenta CIAT wsdl [registroPuntoVenta]
 * @property int|null $idsiatCuis para solicitar wsdl punto de venta[codigoPuntoVenta]
 * @property int $idstatus ref MAINdb status table
 * @property string|null $name identificador de punto de venta ,como ref 
 * @property int|null $iduser
 * @property int|null $siatTransaccion
 * @property string|null $siatResponse
 * @property int|null $codigoModalidad
 * @property string|null $respSiat
 */
class SystemPoint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.systemPoint';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['codigoPuntoVenta', 'idsiatBranch', 'codigoAmbiente', 'codigoTipoPuntoVenta', 'idsiatCuis', 'idstatus', 'iduser', 'siatTransaccion', 'codigoModalidad'], 'default', 'value' => null],
            [['codigoPuntoVenta', 'idsiatBranch', 'codigoAmbiente', 'codigoTipoPuntoVenta', 'idsiatCuis', 'idstatus', 'iduser', 'siatTransaccion', 'codigoModalidad'], 'integer'],
            [['descripcion', 'nombrePuntoVenta', 'siatResponse', 'respSiat'], 'string'],
            [['idstatus'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['idsiatBranch'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSiatBranch::class, 'targetAttribute' => ['idsiatBranch' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'codigoPuntoVenta' => 'Codigo Punto Venta',
            'idsiatBranch' => 'Idsiat Branch',
            'codigoAmbiente' => 'Codigo Ambiente',
            'codigoTipoPuntoVenta' => 'Codigo Tipo Punto Venta',
            'descripcion' => 'Descripcion',
            'nombrePuntoVenta' => 'Nombre Punto Venta',
            'idsiatCuis' => 'Idsiat Cuis',
            'idstatus' => 'Idstatus',
            'name' => 'Name',
            'iduser' => 'Iduser',
            'siatTransaccion' => 'Siat Transaccion',
            'siatResponse' => 'Siat Response',
            'codigoModalidad' => 'Codigo Modalidad',
            'respSiat' => 'Resp Siat',
        ];
    }
}
