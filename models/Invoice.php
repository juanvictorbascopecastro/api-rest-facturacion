<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string|null $dateCreate
 * @property bool|null $recycleBin
 * @property int|null $iduser
 * @property int|null $codigoModalidad
 * @property int|null $idsale
 * @property int|null $idpurchase
 * @property string|null $cufd
 * @property string|null $codigoControl
 * @property string|null $cuis
 * @property string|null $numeroFactura
 * @property int|null $codigoEmision
 * @property string|null $cuf
 * @property int|null $codigoAmbiente
 * @property int|null $codigoPuntoVenta
 * @property string|null $codigoSistema
 * @property int|null $codigoSucursal
 * @property int|null $codigoDocumentoSector
 * @property int|null $tipoFacturaDocumento
 * @property string|null $archivo
 * @property string|null $fechaEnvio
 * @property string|null $hashArchivo
 * @property int|null $codigoEstado
 * @property string|null $codigoRecepcion
 * @property bool|null $transaccion
 * @property string|null $codigoDescripcion
 * @property string|null $codigosRespuestas
 * @property int|null $nitEmisor
 * @property string|null $razonSocialEmisor
 * @property string|null $municipio
 * @property string|null $telefono
 * @property string|null $direccion
 * @property string|null $fechaEmision
 * @property string|null $nombreRazonSocial
 * @property int|null $codigoTipoDocumentoIdentidad
 * @property string|null $numeroDocumento
 * @property string|null $complemento
 * @property string|null $codigoCliente
 * @property int|null $codigoMetodoPago
 * @property string|null $numeroTarjeta
 * @property float|null $montoTotal
 * @property float|null $montoTotalSujetoIva
 * @property float|null $montoGiftCard
 * @property float|null $descuentoAdicional
 * @property int|null $codigoExcepcion
 * @property string|null $cafc
 * @property int|null $codigoMoneda
 * @property float|null $tipoCambio
 * @property float|null $montoTotalMoneda
 * @property string|null $leyenda
 * @property string|null $usuario
 * @property string|null $fechaLimiteEmision
 * @property string|null $cufdAnulacion
 * @property string|null $responseAnulacion
 * @property string|null $codigoDescripcionAnulacion
 * @property int|null $codigoEstadoAnulacion
 * @property bool|null $transaccionAnulacion
 * @property int|null $idcontingencia
 * @property float|null $montoTotalDevuelto
 * @property float|null $montoDescuentoCreditoDebito
 * @property float|null $montoEfectivoCreditoDebito
 * @property int|null $idinvoice
 * @property bool|null $masivaFactura
 * @property int|null $idmasivaFactura
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
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
            [['dateCreate', 'fechaEnvio', 'fechaEmision', 'fechaLimiteEmision'], 'safe'],
            [['recycleBin', 'transaccion', 'transaccionAnulacion', 'masivaFactura'], 'boolean'],
            [['iduser', 'codigoModalidad', 'idsale', 'idpurchase', 'codigoEmision', 'codigoAmbiente', 'codigoPuntoVenta', 'codigoSucursal', 'codigoDocumentoSector', 'tipoFacturaDocumento', 'codigoEstado', 'nitEmisor', 'codigoTipoDocumentoIdentidad', 'codigoMetodoPago', 'codigoExcepcion', 'codigoMoneda', 'codigoEstadoAnulacion', 'idcontingencia', 'idinvoice', 'idmasivaFactura'], 'default', 'value' => null],
            [['iduser', 'codigoModalidad', 'idsale', 'idpurchase', 'codigoEmision', 'codigoAmbiente', 'codigoPuntoVenta', 'codigoSucursal', 'codigoDocumentoSector', 'tipoFacturaDocumento', 'codigoEstado', 'nitEmisor', 'codigoTipoDocumentoIdentidad', 'codigoMetodoPago', 'codigoExcepcion', 'codigoMoneda', 'codigoEstadoAnulacion', 'idcontingencia', 'idinvoice', 'idmasivaFactura'], 'integer'],
            [['cufd', 'codigoControl', 'cuis', 'cuf', 'codigoSistema', 'archivo', 'hashArchivo', 'codigoRecepcion', 'codigoDescripcion', 'codigosRespuestas', 'razonSocialEmisor', 'municipio', 'telefono', 'direccion', 'nombreRazonSocial', 'numeroDocumento', 'complemento', 'numeroTarjeta', 'cafc', 'leyenda', 'cufdAnulacion', 'responseAnulacion', 'codigoDescripcionAnulacion'], 'string'],
            [['montoTotal', 'montoTotalSujetoIva', 'montoGiftCard', 'descuentoAdicional', 'tipoCambio', 'montoTotalMoneda', 'montoTotalDevuelto', 'montoDescuentoCreditoDebito', 'montoEfectivoCreditoDebito'], 'number'],
            [['numeroFactura', 'codigoCliente'], 'string', 'max' => 15],
            [['usuario'], 'string', 'max' => 20],
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
            'codigoModalidad' => 'Codigo Modalidad',
            'idsale' => 'Idsale',
            'idpurchase' => 'Idpurchase',
            'cufd' => 'Cufd',
            'codigoControl' => 'Codigo Control',
            'cuis' => 'Cuis',
            'numeroFactura' => 'Numero Factura',
            'codigoEmision' => 'Codigo Emision',
            'cuf' => 'Cuf',
            'codigoAmbiente' => 'Codigo Ambiente',
            'codigoPuntoVenta' => 'Codigo Punto Venta',
            'codigoSistema' => 'Codigo Sistema',
            'codigoSucursal' => 'Codigo Sucursal',
            'codigoDocumentoSector' => 'Codigo Documento Sector',
            'tipoFacturaDocumento' => 'Tipo Factura Documento',
            'archivo' => 'Archivo',
            'fechaEnvio' => 'Fecha Envio',
            'hashArchivo' => 'Hash Archivo',
            'codigoEstado' => 'Codigo Estado',
            'codigoRecepcion' => 'Codigo Recepcion',
            'transaccion' => 'Transaccion',
            'codigoDescripcion' => 'Codigo Descripcion',
            'codigosRespuestas' => 'Codigos Respuestas',
            'nitEmisor' => 'Nit Emisor',
            'razonSocialEmisor' => 'Razon Social Emisor',
            'municipio' => 'Municipio',
            'telefono' => 'Telefono',
            'direccion' => 'Direccion',
            'fechaEmision' => 'Fecha Emision',
            'nombreRazonSocial' => 'Nombre Razon Social',
            'codigoTipoDocumentoIdentidad' => 'Codigo Tipo Documento Identidad',
            'numeroDocumento' => 'Numero Documento',
            'complemento' => 'Complemento',
            'codigoCliente' => 'Codigo Cliente',
            'codigoMetodoPago' => 'Codigo Metodo Pago',
            'numeroTarjeta' => 'Numero Tarjeta',
            'montoTotal' => 'Monto Total',
            'montoTotalSujetoIva' => 'Monto Total Sujeto Iva',
            'montoGiftCard' => 'Monto Gift Card',
            'descuentoAdicional' => 'Descuento Adicional',
            'codigoExcepcion' => 'Codigo Excepcion',
            'cafc' => 'Cafc',
            'codigoMoneda' => 'Codigo Moneda',
            'tipoCambio' => 'Tipo Cambio',
            'montoTotalMoneda' => 'Monto Total Moneda',
            'leyenda' => 'Leyenda',
            'usuario' => 'Usuario',
            'fechaLimiteEmision' => 'Fecha Limite Emision',
            'cufdAnulacion' => 'Cufd Anulacion',
            'responseAnulacion' => 'Response Anulacion',
            'codigoDescripcionAnulacion' => 'Codigo Descripcion Anulacion',
            'codigoEstadoAnulacion' => 'Codigo Estado Anulacion',
            'transaccionAnulacion' => 'Transaccion Anulacion',
            'idcontingencia' => 'Idcontingencia',
            'montoTotalDevuelto' => 'Monto Total Devuelto',
            'montoDescuentoCreditoDebito' => 'Monto Descuento Credito Debito',
            'montoEfectivoCreditoDebito' => 'Monto Efectivo Credito Debito',
            'idinvoice' => 'Idinvoice',
            'masivaFactura' => 'Masiva Factura',
            'idmasivaFactura' => 'Idmasiva Factura',
        ];
    }
}
