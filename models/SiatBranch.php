<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatBranch".
 *
 * @property int $id reference DB access cfg."ioSystemBranch" field  id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int $codigoSucursal codigoSucursal de SIAT
 * @property int $idsiatSystem * id databaseMAIN(postgres)  table ref  ciat.siatsystem
 * @property int|null $iduser
 * @property string|null $codigoSistema
 * @property int|null $codigoModalidad
 * @property int|null $codigoAmbiente
 * @property int|null $nit
 * @property string|null $razonSocial
 * @property int|null $signerPassword
 */
class SiatBranch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatBranch';
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
            [['id', 'codigoSucursal', 'idsiatSystem'], 'required'],
            [['id', 'codigoSucursal', 'idsiatSystem', 'iduser', 'codigoModalidad', 'codigoAmbiente', 'nit', 'signerPassword'], 'default', 'value' => null],
            [['id', 'codigoSucursal', 'idsiatSystem', 'iduser', 'codigoModalidad', 'codigoAmbiente', 'nit', 'signerPassword'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin'], 'boolean'],
            [['codigoSistema', 'razonSocial'], 'string'],
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
            'codigoSucursal' => 'Codigo Sucursal',
            'idsiatSystem' => 'Idsiat System',
            'iduser' => 'Iduser',
            'codigoSistema' => 'Codigo Sistema',
            'codigoModalidad' => 'Codigo Modalidad',
            'codigoAmbiente' => 'Codigo Ambiente',
            'nit' => 'Nit',
            'razonSocial' => 'Razon Social',
            'signerPassword' => 'Signer Password',
        ];
    }
}
