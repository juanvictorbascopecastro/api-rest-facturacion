<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contingencia".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $codigoAmbiente
 * @property int|null $codigoMotivoEvento
 * @property int|null $codigoPuntoVenta
 * @property string|null $codigoSistema
 * @property int|null $codigoSucursal
 * @property int|null $codigoModalidad
 * @property string|null $cufdEvento
 * @property string|null $cuis
 * @property string|null $descripcion
 * @property string|null $fechaHoraFinEvento
 * @property string|null $fechaHoraInicioEvento
 * @property string|null $nit
 * @property int|null $codigoDocumentoSector
 * @property int|null $codigoEmision
 * @property int|null $tipoFacturaDocumento
 * @property string|null $archivo
 * @property string|null $fechaEnvio
 * @property string|null $hashArchivo
 * @property int|null $cantidadFacturas
 * @property int|null $codigoEvento
 * @property string|null $codigoRecepcion
 * @property string|null $cafc
 * @property bool|null $automaticExecute
 * @property string|null $registroEventoSignificativoCufd
 * @property string|null $registroEventoSignificativoRespuesta
 * @property bool|null $registroEventoSignificativoTransaccion
 * @property string|null $recepcionPaqueteFacturaCufd
 * @property string|null $recepcionPaqueteFacturaRespuesta
 * @property bool|null $recepcionPaqueteFacturaTransaccion
 * @property string|null $validacionRecepcionPaqueteFacturaCufd
 * @property string|null $validacionRecepcionPaqueteFacturaRespuesta
 * @property bool|null $validacionRecepcionPaqueteFacturaTransaccion
 * @property bool|null $execute
 * @property bool|null $executed
 */
class Contingencia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contingencia';
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
            [['dateCreate', 'fechaHoraFinEvento', 'fechaHoraInicioEvento'], 'safe'],
            [['recycleBin', 'automaticExecute', 'registroEventoSignificativoTransaccion', 'recepcionPaqueteFacturaTransaccion', 'validacionRecepcionPaqueteFacturaTransaccion', 'execute', 'executed'], 'boolean'],
            [['iduser', 'codigoAmbiente', 'codigoMotivoEvento', 'codigoPuntoVenta', 'codigoSucursal', 'codigoModalidad', 'codigoDocumentoSector', 'codigoEmision', 'tipoFacturaDocumento', 'cantidadFacturas', 'codigoEvento'], 'default', 'value' => null],
            [['iduser', 'codigoAmbiente', 'codigoMotivoEvento', 'codigoPuntoVenta', 'codigoSucursal', 'codigoModalidad', 'codigoDocumentoSector', 'codigoEmision', 'tipoFacturaDocumento', 'cantidadFacturas', 'codigoEvento'], 'integer'],
            [['codigoSistema', 'cufdEvento', 'cuis', 'descripcion', 'nit', 'archivo', 'fechaEnvio', 'hashArchivo', 'codigoRecepcion', 'cafc', 'registroEventoSignificativoCufd', 'registroEventoSignificativoRespuesta', 'recepcionPaqueteFacturaCufd', 'recepcionPaqueteFacturaRespuesta', 'validacionRecepcionPaqueteFacturaCufd', 'validacionRecepcionPaqueteFacturaRespuesta'], 'string'],
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
            'codigoAmbiente' => 'Codigo Ambiente',
            'codigoMotivoEvento' => 'Codigo Motivo Evento',
            'codigoPuntoVenta' => 'Codigo Punto Venta',
            'codigoSistema' => 'Codigo Sistema',
            'codigoSucursal' => 'Codigo Sucursal',
            'codigoModalidad' => 'Codigo Modalidad',
            'cufdEvento' => 'Cufd Evento',
            'cuis' => 'Cuis',
            'descripcion' => 'Descripcion',
            'fechaHoraFinEvento' => 'Fecha Hora Fin Evento',
            'fechaHoraInicioEvento' => 'Fecha Hora Inicio Evento',
            'nit' => 'Nit',
            'codigoDocumentoSector' => 'Codigo Documento Sector',
            'codigoEmision' => 'Codigo Emision',
            'tipoFacturaDocumento' => 'Tipo Factura Documento',
            'archivo' => 'Archivo',
            'fechaEnvio' => 'Fecha Envio',
            'hashArchivo' => 'Hash Archivo',
            'cantidadFacturas' => 'Cantidad Facturas',
            'codigoEvento' => 'Codigo Evento',
            'codigoRecepcion' => 'Codigo Recepcion',
            'cafc' => 'Cafc',
            'automaticExecute' => 'Automatic Execute',
            'registroEventoSignificativoCufd' => 'Registro Evento Significativo Cufd',
            'registroEventoSignificativoRespuesta' => 'Registro Evento Significativo Respuesta',
            'registroEventoSignificativoTransaccion' => 'Registro Evento Significativo Transaccion',
            'recepcionPaqueteFacturaCufd' => 'Recepcion Paquete Factura Cufd',
            'recepcionPaqueteFacturaRespuesta' => 'Recepcion Paquete Factura Respuesta',
            'recepcionPaqueteFacturaTransaccion' => 'Recepcion Paquete Factura Transaccion',
            'validacionRecepcionPaqueteFacturaCufd' => 'Validacion Recepcion Paquete Factura Cufd',
            'validacionRecepcionPaqueteFacturaRespuesta' => 'Validacion Recepcion Paquete Factura Respuesta',
            'validacionRecepcionPaqueteFacturaTransaccion' => 'Validacion Recepcion Paquete Factura Transaccion',
            'execute' => 'Execute',
            'executed' => 'Executed',
        ];
    }
}
