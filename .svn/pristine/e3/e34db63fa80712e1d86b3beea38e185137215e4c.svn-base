<?php

namespace app\models;
use app\modules\ioLib\helpers\WsdlSiat;

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
    public static $statusACTIVO = 10;
    public static $statusINACTIVO = 60;
    public static $statusFINALIZADO = 100;
    public $idcontingencia = -1;
    
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
    
    
    public static function getSWDL($modelSystemPoint) {
        $params = array(
            'SolicitudCufd' => array(
                'codigoAmbiente' => $modelSystemPoint->idsiatBranch0->codigoAmbiente,
                'codigoModalidad' => $modelSystemPoint->idsiatBranch0->codigoModalidad,
                'codigoPuntoVenta' => $modelSystemPoint->codigoPuntoVenta,
                'codigoSucursal' => $modelSystemPoint->idsiatBranch0->codigoSucursal,
                'codigoSistema' => $modelSystemPoint->idsiatBranch0->codigoSistema,
                'cuis' => $modelSystemPoint->SiatCuisActive()->cuis,
                'nit' => $modelSystemPoint->idsiatBranch0->nit
            )
        );

        //print_r($params);
        //for ($i = 1; $i <= 100; $i++) {
        $wsdlSiat = new wsdlSiat('FacturacionCodigos');
        $model = new SiatCufd();
        if ($wsdlSiat->success()) {
            $respons = $wsdlSiat->run('cufd', $params, false);
            if ($respons != false && $respons->RespuestaCufd->codigoControl!=null) {
                $model->codigoModalidad = $modelSystemPoint->idsiatBranch0->codigoModalidad;
                $model->codigoAmbiente = $modelSystemPoint->idsiatBranch0->codigoAmbiente;
                $model->cufd = $respons->RespuestaCufd->codigo;
                $model->codigoControl = $respons->RespuestaCufd->codigoControl;
                $model->direccion = $respons->RespuestaCufd->direccion;
                $model->fechaVigencia = $respons->RespuestaCufd->fechaVigencia;
                $model->idstatus = SiatCuis::$statusACTIVO;
                $model->idsiatCuis = $modelSystemPoint->SiatCuisActive()->id;

                $model->respSiat = print_r($respons, true);
                
                echo '<div style="font-size:15px;  font-weight:bold; background:#e0eac8;  text-align: center; padding: 10px;  " > Nuevo Código SIAT "CUFD"  ▶ [GENERADO CORRECTAMENTE]</div>';

                if (!$model->save()) {
                    echo "[no]";
                    print_r($model->getErrors());
                }
            }else{
                echo print_r($respons, true);
                
            }
        }
        //}

        return $model;
    }
}
