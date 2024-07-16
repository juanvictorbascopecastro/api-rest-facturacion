<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatCuis".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $usuario
 * @property string $cuis
 * @property string $fechaVigencia
 * @property int $idsystemPoint ref codigoPuntoVenta SIAT
 * @property int $idstatus
 * @property int|null $iduser
 * @property int|null $numeroFactura
 * @property int|null $codigoModalidad
 * @property int|null $codigoAmbiente
 * @property string|null $respSiat
 */
class SiatCuis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatCuis';
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
            [['dateCreate', 'fechaVigencia'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['cuis', 'fechaVigencia', 'idsystemPoint', 'idstatus'], 'required'],
            [['idsystemPoint', 'idstatus', 'iduser', 'numeroFactura', 'codigoModalidad', 'codigoAmbiente'], 'default', 'value' => null],
            [['idsystemPoint', 'idstatus', 'iduser', 'numeroFactura', 'codigoModalidad', 'codigoAmbiente'], 'integer'],
            [['respSiat'], 'string'],
            [['usuario'], 'string', 'max' => 30],
            [['cuis'], 'string', 'max' => 50],
            [['idsystemPoint'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSystemPoint::class, 'targetAttribute' => ['idsystemPoint' => 'id']],
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
            'usuario' => 'Usuario',
            'cuis' => 'Cuis',
            'fechaVigencia' => 'Fecha Vigencia',
            'idsystemPoint' => 'Idsystem Point',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
            'numeroFactura' => 'Numero Factura',
            'codigoModalidad' => 'Codigo Modalidad',
            'codigoAmbiente' => 'Codigo Ambiente',
            'respSiat' => 'Resp Siat',
        ];
    }
}
