<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siatCufd".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int $idsiatCuis
 * @property string|null $cufd
 * @property string|null $codigoControl
 * @property string|null $direccion
 * @property string $fechaVigencia
 * @property int $idstatus
 * @property int|null $iduser
 * @property bool|null $backup
 * @property string|null $respSiat
 * @property bool|null $masivaFactura
 * @property int|null $codigoModalidad
 * @property int|null $codigoAmbiente
 */
class SiatCufd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siatCufd';
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
            [['recycleBin', 'backup', 'masivaFactura'], 'boolean'],
            [['idsiatCuis', 'fechaVigencia', 'idstatus'], 'required'],
            [['idsiatCuis', 'idstatus', 'iduser', 'codigoModalidad', 'codigoAmbiente'], 'default', 'value' => null],
            [['idsiatCuis', 'idstatus', 'iduser', 'codigoModalidad', 'codigoAmbiente'], 'integer'],
            [['cufd', 'codigoControl', 'direccion', 'respSiat'], 'string'],
            [['idsiatCuis'], 'exist', 'skipOnError' => true, 'targetClass' => SiatSiatCuis::class, 'targetAttribute' => ['idsiatCuis' => 'id']],
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
            'idsiatCuis' => 'Idsiat Cuis',
            'cufd' => 'Cufd',
            'codigoControl' => 'Codigo Control',
            'direccion' => 'Direccion',
            'fechaVigencia' => 'Fecha Vigencia',
            'idstatus' => 'Idstatus',
            'iduser' => 'Iduser',
            'backup' => 'Backup',
            'respSiat' => 'Resp Siat',
            'masivaFactura' => 'Masiva Factura',
            'codigoModalidad' => 'Codigo Modalidad',
            'codigoAmbiente' => 'Codigo Ambiente',
        ];
    }
}
