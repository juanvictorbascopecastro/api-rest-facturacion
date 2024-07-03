<?php

namespace app\models;

use Yii;

class Invoice extends \yii\db\ActiveRecord
{
    private static $customDb;

    public static function tableName()
    {
        return 'invoice';
    }

    public static function getDb()
    {
        return self::$customDb ?: Yii::$app->db;
    }

    public static function setCustomDb($db)
    {
        self::$customDb = $db;
    }

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

    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }
}
