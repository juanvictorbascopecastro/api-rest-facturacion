<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siat.siat".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property bool|null $connection
 */
class Siat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siat.siat';
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
            [['id'], 'required'],
            [['id', 'iduser'], 'default', 'value' => null],
            [['id', 'iduser'], 'integer'],
            [['dateCreate'], 'safe'],
            [['recycleBin', 'connection'], 'boolean'],
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
            'iduser' => 'Iduser',
            'connection' => 'Connection',
        ];
    }
    
    
    public function cfg() {
        $model = Siat::findOne(1);
        if ($model == null) {
            $model = new Siat();
            $model->id = 1;
            $model->save();
            //model->connention=1;
        }
        return $model;
    }
    
    public function conn($valueConnection = -1) {
        
        $model = Siat::findOne(1);
        if ($model == null) {
            $model = new Siat();
            $model->id = 1;
            $model->save();
            //model->connention=1;
        }

        $model = Siat::findOne(1);
        
        if ($valueConnection != -1) {
            $model->connection = $valueConnection;
            $model->save();
        }
        return $model->connection;  
    }

    public function sincronizarFechaHora() {
        $wsdlSiat = new wsdlSiat('FacturacionSincronizacion');
        $codigoPuntoVenta = 0;
        $modelSystemPoint = SystemPoint::model()->find('"codigoPuntoVenta"=' . $codigoPuntoVenta);

        $params = array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente' => $modelSystemPoint->idsiatBranch0->codigoAmbiente,
                'codigoPuntoVenta' => $modelSystemPoint->codigoPuntoVenta,
                'cuis' => $modelSystemPoint->SiatCuisActive()->cuis,
                'codigoSistema' => $modelSystemPoint->idsiatBranch0->codigoSistema,
                'codigoSucursal' => $modelSystemPoint->idsiatBranch0->codigoSucursal,
                'nit' => $wsdlSiat::$nit
        ));
        $date = null;
        if ($wsdlSiat->success()) {
            $respons = $wsdlSiat::run('sincronizarFechaHora', $params);
            if ($respons != false) {
                $date = $respons->RespuestaFechaHora->fechaHora;
            }
        }
        if ($date == null)
            $date = Yii::app()->iooxsBranch->createCommand("select CAST(now() AS timestamp) + CAST('00:00:1.850' AS time)")->queryScalar();
        return $date . '-04';
    }
}
