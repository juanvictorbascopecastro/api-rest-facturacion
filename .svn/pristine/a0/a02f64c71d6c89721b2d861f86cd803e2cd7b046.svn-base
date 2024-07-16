<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatSystem".
 *
 * @property int $id reference BD access cfg.ioSystem field id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property string|null $codigoSistema SIAT
 * @property string $active
 * @property string|null $desactive
 * @property string|null $v0_siatDescripcion SIAT ws sincronizarActividades
 * @property string|null $v0_siatTipoActividad SIAT ws sincronizarActividades
 * @property string|null $v0_siatCodigoCaeb
 * @property int $v0_codigoModalidad
 * @property int $v0_codigoAmbiente
 * @property int|null $v0_nit
 * @property int $idstatus
 * @property int|null $iduser
 * @property string|null $v0_razonSocial
 * @property bool|null $v0_masivaFactura
 */
class SiatSystem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatSystem';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('iooxsRoot');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active', 'v0_codigoModalidad', 'v0_codigoAmbiente', 'idstatus'], 'required'],
            [['id', 'v0_codigoModalidad', 'v0_codigoAmbiente', 'v0_nit', 'idstatus', 'iduser'], 'default', 'value' => null],
            [['id', 'v0_codigoModalidad', 'v0_codigoAmbiente', 'v0_nit', 'idstatus', 'iduser'], 'integer'],
            [['dateCreate', 'active', 'desactive'], 'safe'],
            [['recycleBin', 'v0_masivaFactura'], 'boolean'],
            [['codigoSistema', 'v0_siatDescripcion', 'v0_razonSocial'], 'string'],
            [['v0_siatTipoActividad'], 'string', 'max' => 2],
            [['v0_siatCodigoCaeb'], 'string', 'max' => 20],
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
            'dateCreate' => 'Date Create',
            'recycleBin' => 'Recycle Bin',
            'codigoSistema' => 'Codigo Sistema',
            'active' => 'Active',
            'desactive' => 'Desactive',
            'v0_siatDescripcion' => 'V0 Siat Descripcion',
            'v0_siatTipoActividad' => 'V0 Siat Tipo Actividad',
            'v0_siatCodigoCaeb' => 'V0 Siat Codigo Caeb',
            'v0_codigoModalidad' => 'V0 Codigo Modalidad',
            'v0_codigoAmbiente' => 'V0 Codigo Ambiente',
            'v0_nit' => 'V0 Nit',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
            'v0_razonSocial' => 'V0 Razon Social',
            'v0_masivaFactura' => 'V0 Masiva Factura',
        ];
    }
}
