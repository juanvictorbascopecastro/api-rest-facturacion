<?php

namespace app\models;

use app\modules\ioLib\helpers\WsdlSiat;
use app\models\SincronizarListaLeyendasFactura;
use app\models\Productstock;
use app\models\Siat;
use app\models\Contingencia;
use app\models\SiatCuis;
use yii\data\ActiveDataProvider;
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
class Invoice extends \yii\db\ActiveRecord {

    public $xmlDetailProducts;
    public $email;
    public $phone;
    public $modelProducts;
    public $serviceSWDL;
    public $typeXML;
    public $archivoXml = null;
    //codigoDocumentoSector=2 FACTURACION
    public $periodoFacturado;
    //codigoDocumentoSector=34 FACTURACION
    public $ajusteAfectacionIva;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'invoice';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('iooxsBranch');
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
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
    public function attributeLabels() {
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

    public function getSWDL() {

        $serviceSWDL = $this->serviceSWDL;
        $parameterName = 'SolicitudServicioRecepcionFactura';
        $function = 'recepcionFactura';

        $WsdlSiat = new WsdlSiat($serviceSWDL);

        file_put_contents('factura.xml', $this->archivo);
        $params = array(
            $parameterName => array(
                'codigoAmbiente' => $this->codigoAmbiente,
                'codigoDocumentoSector' => $this->codigoDocumentoSector,
                'codigoEmision' => $this->codigoEmision,
                'codigoModalidad' => $this->codigoModalidad,
                'codigoPuntoVenta' => $this->codigoPuntoVenta,
                'codigoSistema' => $this->codigoSistema,
                'codigoSucursal' => $this->codigoSucursal,
                'cufd' => $this->cufd,
                'cuis' => $this->cuis,
                'nit' => $this->nitEmisor,
                'tipoFacturaDocumento' => $this->tipoFacturaDocumento,
                'archivo' => gzencode($this->archivo),
                // 'archivo' => 'xxx',
                'fechaEnvio' => $WsdlSiat->dateFormat($this->fechaEnvio, true, true),
                'hashArchivo' => $this->hashArchivo
            )
        );

        if ($WsdlSiat->success()) {
            $respons = $WsdlSiat->run($function, $params, false);

            if ($respons == false) {

                $respons = array();
            }
        }
        return $respons;
    }

    public function multiAttachMail($to, $subject, $message, $senderEmail, $senderName, $files = array()) {
        // Sender info  
        $from = $senderName . " <" . $senderEmail . ">";
        $headers = "From: $from";

        // Boundary  
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // Headers for attachment  
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        // Multipart boundary  
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";

        // Preparing attachment 
        if (!empty($files)) {
            for ($i = 0; $i < count($files); $i++) {
                if (is_file($files[$i])) {
                    $file_name = basename($files[$i]);
                    $file_size = filesize($files[$i]);

                    $message .= "--{$mime_boundary}\n";
                    $fp = @fopen($files[$i], "rb");
                    $data = @fread($fp, $file_size);
                    @fclose($fp);
                    $data = chunk_split(base64_encode($data));
                    $message .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"\n" .
                            "Content-Description: " . $file_name . "\n" .
                            "Content-Disposition: attachment;\n" . " filename=\"" . $file_name . "\"; size=" . $file_size . ";\n" .
                            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                }
            }
        }

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $senderEmail;
        // echo "SEND MAIL [$headers][$to]";
        // Send email 
        $mail = mail($to, $subject, $message, $headers, $returnpath);

        // Return true if email sent, otherwise return false 
        if ($mail) {
            return true;
        } else {
            return false;
        }
    }

    public function sendMail() {
        return;
//         $to = 'recipient@example.com';
//         $from = 'sender@example.com';
//         $fromName = 'Sender Name';
        // Recipient 

        $ioModelSystem = SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io;

        $to = $this->idsale0->email;
        $file = "tmpInvoices/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier . "/tmp/";

        // Sender 
        $from = 'business@iooxs.com';
        $fromName = 'business@iooxs.com';

        $subject = 'Correo + Documentos Factura';
//         $dir = "tmpFactura/factura" . $this->cuf . ".fpd";
//         $this->fileModalidadComputarizada($dir);
//         $dir2 = "factura" . $this->cuf . ".xml";
//         $this->fileModalidadComputarizada($dir2);
// Attachment files 
        $files = array(
            $file . $this->cuf . ".pdf",
            $file . $this->cuf . ".xml"
        );

        $htmlContent = ' 
    <h3>GRACIAS POR SU COMPRA</h3> 
    <h4>Este Correo contiene factura y documento xml</h4> 
    <p><b>Total Documentos:</b> ' . count($files) . '</p>';

// Call function and pass the required arguments 
        // $sendEmail = $this->multiAttachMail($to, $subject, $htmlContent, $from, $fromName, $files);

        PHPMail::send(array($ioModelSystem->email, $ioModelSystem->fullNameCompany . ' - SISTEMA "io"'), array($this->idsale0->email, $this->idsale0->razonSocial), 'FACTURACION ', $htmlContent, $files);

// Email sending status 
        if (!$sendEmail) {
            unlink($file . $this->cuf . ".pdf");
            unlink($file . $this->cuf . ".xml");
        }
    }

    public function sendApiWhatsapp() {
        return;
        if ($this->phone == null || $this->phone == '')
            return;
        echo "[$this->phone]";

        $curl = curl_init();

        $data = [
            "messaging_product" => "whatsapp",
            "to" => "" . $this->phone,
            "type" => "template",
            "template" => [
                "name" => "hello_world",
                "language" => [
                    "code" => "en_US"
                ]
            ]
        ];

        print_r($data);
        $token = 'EAANnvfTuYTEBOyKsEe58hrUHCGZANqbK8xRVBsICKqQZBoZB6hyqG4wZCfXaKPTwRKwLeAZAWekKDbvGCRf8eJt3pp8uax6NE4JpzDIdnBpVuo9dNt8ZBYlZB5WgWv1eniQGxMoB5BQ9n3ZCgqdA8FzFCVYCyoVguLyHYUQedEdrtH9bDUGyFayGkEHgDueQgdpem5giqhYi36tTazS7yHsY0gFHR1pkQkJvKsMZD';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v19.0/344100815444333/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }

    public function sendMailAnnul() {
//         $to = 'recipient@example.com';
//         $from = 'sender@example.com';
//         $fromName = 'Sender Name';
        // Recipient 

        $to = $this->idsale0->email;
        $file = "tmpInvoices/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier . "/tmp/";

        // Sender 
        $from = 'business@iooxs.com';
        $fromName = 'business@iooxs.com';

        $subject = 'Correo + Documentos Factura';
//         $dir = "tmpFactura/factura" . $this->cuf . ".fpd";
//         $this->fileModalidadComputarizada($dir);
//         $dir2 = "factura" . $this->cuf . ".xml";
//         $this->fileModalidadComputarizada($dir2);
// Attachment files 
        $files = array(
            $file . $this->cuf . ".pdf",
            $file . $this->cuf . ".xml"
        );

        $files = array();

        $WsdlSiat = new WsdlSiat('Factura');

        $url = $WsdlSiat->url() . '/consulta/QR?nit=' . $this->nitEmisor . "&cuf=$this->cuf&numero=$this->numeroFactura&t=1";
        $htmlContent = ' 
                      <h3>Factura  Anulada</h3> 
                      <p>Factura anulada Nro:' . $this->numeroFactura . ' </p>
                      <p>Código de Autorizacicón:' . $this->cuf . ' </p>
                      <p>Url:' . $url . ' </p>';

// Call function and pass the required arguments 
        $sendEmail = $this->multiAttachMail($to, $subject, $htmlContent, $from, $fromName, $files);

// Email sending status 
//        if (!$sendEmail) {
//            unlink($file . $this->cuf . ".pdf");
//            unlink($file . $this->cuf . ".xml");
//        }
    }

    public function modalidadComputarizadaV0() {

        include('protected/modules/sale/facturacion/datosFactura.php');
        $modelAutorizacion = SiatAutorizacion::model()->findByPk(2);
        $this->cuf = $modelAutorizacion->cuf;
        $this->cuis = $modelAutorizacion->cuis;

        $codigoControl = new datosFactura();

        $q = 'select max(id) from invoice '
                . " where cuf='$modelAutorizacion->cuf' ";
        $command = Yii::app()->iooxsBranch->createCommand($q);
        $idMax = $command->queryScalar();

        $modelInvoice = Invoice::model()->findByPk($idMax);
        $this->numeroFactura = ($modelInvoice == null) ? 1 : $modelInvoice->numeroFactura * 1 + 1;

        $fechaEmision = substr($this->fechaEmision, 0, 10);
        $this->codigoControl = datosFactura::codigoControl($this->cuf, $this->numeroFactura, $this->numeroDocumento, $fechaEmision, $this->montoTotal, $this->cuis);
    }

    public function fileModalidadComputarizada($dir) {

        $model = $this->loadModel($id);
        $modelInvoice = Invoice::model()->findByAttributes(array('idsale' => $model->id));

        $products = new Productstock;
        $products = $products->getDocument($model->iddocument)->getData();

        $fullNameCompany = $model->idsystemPoint0->idsiatBranch0->idsiatSystem0->io->fullNameCompany;

        $razonSocialEmisor = $modelInvoice->razonSocialEmisor;
        $direccion = $modelInvoice->direccion;
        $telefono = $modelInvoice->telefono;
        $municipio = $modelInvoice->municipio;
        $tipoFacturaDocumento = $modelInvoice->tipoFacturaDocumento0->descripcion;
        $fechaEmision = $modelInvoice->fechaEmision;
        $nombreRazonSocial = $modelInvoice->nombreRazonSocial;

        $nitEmisor = $modelInvoice->nitEmisor;

        $numeroDocumento = $modelInvoice->numeroDocumento;

        $pdf = new FPDF('P', 'mm', array(200 + sizeof($products) * 4, 79));
        //Establecemos los márgenes izquierda, arriba y derecha:
        $pdf->SetMargins(6, 7, 7);

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(0, 4, $fullNameCompany, 0, 'C');

        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(0, 4, $razonSocialEmisor, 0, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, $direccion . ' - Tel:' . $telefono, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(0, 4, $municipio, 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        if ($modelInvoice->tipoFacturaDocumento == 1 || $modelInvoice->tipoFacturaDocumento == 2) {
            $tipoFacturaDocumento = str_replace('FACTURA ', '', $tipoFacturaDocumento);
            $pdf->MultiCell(0, 4, 'FACTURA ORIGINAL', 0, 'C');
        }
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->MultiCell(0, 4, $tipoFacturaDocumento, 0, 'C', false);

        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln();
        $pdf->MultiCell(0, 4, 'NIT: ' . $nitEmisor, 0, 'C');

        $pdf->MultiCell(0, 4, 'NRO. FACTURA: ' . $modelInvoice->numeroFactura, 0, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, 'AUTORIZACION: ' . $modelInvoice->cuf, 0, 'C');

        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'NOMBRE/RAZON SOCIAL:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, $nombreRazonSocial, 0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'NIT/CI/CEX:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, $numeroDocumento, 0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'FECHA  EMISION:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, System::dateFormat($fechaEmision, 'd/m/Y'), 0);

        $pdf->SetFont('Arial', '', 7);
        $w = array(10, 31, 11, 12);

        $pdf->Cell($w[0], 4, 'CANT.', 'B', 0, 'L', false);
        $pdf->Cell($w[1], 4, 'DETALLE', 'B', 0, 'L', false);
        $pdf->Cell($w[2], 4, 'P.U.', 'B', 0, 'R', false);
        $pdf->Cell($w[3], 4, 'TOTAL', 'B', 0, 'R', false);
        $pdf->Ln();

        $montoTotal = 0;
        foreach ($products as $productDetail) {
            $subTotal = $productDetail->quantityoutput * $productDetail->price;
            $pdf->Cell($w[0], 4, SGridView::number($productDetail->quantityoutput, 'number(2)'), 0, 0, 'R', false);
            $pdf->Cell($w[1], 4, $productDetail->idproduct0->name, 0, 0, 'L', false);
            $pdf->Cell($w[2], 4, SGridView::number($productDetail->price, 'number(2)'), 0, 0, 'R', false);
            $pdf->Cell($w[3], 4, SGridView::number($subTotal, 'number(2)'), 0, 0, 'R', false);
            $pdf->Ln();
            $montoTotal += $subTotal;
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($w[0], 4, 'TOTAL', 'T', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', 'T', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', 'T', 0, 'R', false);
        $montoTotal = SGridView::number($modelInvoice->montoTotal, 'number(2)');
        $pdf->Cell($w[3], 4, $montoTotal, 'T', 0, 'R', false);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 7);

        $numeroLiteral = NumberToLetter::to_word($montoTotal);
        $total = explode('.', $montoTotal);

        $numeroLiteral = strtoupper($numeroLiteral) . ' ' . (sizeof($total) == 2 ? $total[1] : '00') . '/100 BOLIVIANOS';
        $pdf->MultiCell(0, 4, 'Son: ' . $numeroLiteral, 0);
        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');
        $pdf->Ln();
//         $pdf->SetFont('Arial', '', 9);
//
//         $pdf->MultiCell(0, 4, 'CODIGO CONTROL: ' . $modelInvoice->codigoControl, 0);
//         $pdf->SetFont('Arial', '', 8);
//         $pdf->MultiCell(0, 4, 'FECHA LIMITE DE EMISION: ' . System::dateFormat($modelAutorizacion->fechaLimite, 'd/m/Y'), 0);
        //RQ - INVOICE
        $codeText = "$modelInvoice->nitEmisor|$modelInvoice->numeroFactura|$modelInvoice->cuf|17/03/2015|$montoTotal|$montoTotal|$modelInvoice->codigoControl|$modelInvoice->numeroDocumento|0|0|0|0";
        $fileQR = 'tmp/qr' . $modelInvoice->numeroFactura . '-' . rand(1, 10000000) . '.png';
        QRcode::png($codeText, $fileQR, 'L', 4, 2, true);
//         $pdf->Image($fileQR,10,10,-150);
        $pdf->Cell(0, 27, $pdf->Image($fileQR, 25, $pdf->GetY(), -150), 0, 0, 'R', false);
        // END RQ
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 5);
        $leyenda = 'ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS, EL USO ILICITO SERA SANCIONADO PENALMENTE DE ACUERDO A LEY "' . $modelInvoice->leyenda . '"';
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, 'C');

        $pdf->MultiCell(0, 4, 'USUARIO: CAJERO 01 ', 0);
        $pdf->Output($dir);
    }

    public function contingencia($codigoMotivoEvento = 2, $idcontingencia = -1) {
        $descripcion = 'Inaccesibilidad al Servicio Web de la Administración Tributaria.';
        if ($this->cafc != null) {
            $codigoMotivoEvento = 7;
            $descripcion = 'Corte de suministro de energía eléctrica.';
        }


        $query = Contingencia::find()
                ->where(['cufdEvento' => $this->cufd])
                ->andWhere(['execute' => false])
                ->andWhere(['codigoRecepcion' => null]);

        if ($this->cafc != null) {

            if ($idcontingencia != -1)
                $query->andWhere(['id' => $idcontingencia]);

            $query->andWhere(['cafc' => $this->cafc]);

            if ($idcontingencia == -1) {
                $query->andWhere(['automaticExecute' => true]);
            } else {
                $query->andWhere(['automaticExecute' => false]);
            }
        }

        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        foreach ($models as $modelContingencia) {
            $q = "SELECT COUNT(*) FROM invoice WHERE \"idcontingencia\" = :idContingencia";

            $command = Yii::$app->iooxsBranch->createCommand($q);
            $command->bindValue(':idContingencia', $idContingencia);
            $number = $command->queryScalar() + 1;

            if ($number <= 500) {
                $this->idcontingencia = $modelContingencia->id;
                return $modelContingencia;
            }
        }



        $modelContingencia = new Contingencia;
        $modelCuis = SiatCuis::findOne(['cuis' => $this->cuis]);
        $modelSystemPoint = $modelCuis->idsystemPoint0;
        $modelContingencia->codigoModalidad = $modelSystemPoint->idsiatBranch0->codigoModalidad;
        $modelContingencia->codigoAmbiente = $modelSystemPoint->idsiatBranch0->codigoAmbiente;

        $modelContingencia->codigoPuntoVenta = $modelSystemPoint->codigoPuntoVenta;
        $modelContingencia->codigoSistema = $modelSystemPoint->idsiatBranch0->codigoSistema;
        $modelContingencia->codigoSucursal = $modelSystemPoint->idsiatBranch0->codigoSucursal;

        $modelContingencia->cuis = $this->cuis;

        $modelContingencia->codigoMotivoEvento = $codigoMotivoEvento;

        $modelContingencia->descripcion = $descripcion;
        $modelContingencia->cufdEvento = $this->cufd;
        $modelContingencia->nit = $modelSystemPoint->idsiatBranch0->nit.'';

        if ($this->cafc != null) {
            $modelContingencia->cafc = $this->cafc;
        }

        $modelContingencia->codigoDocumentoSector = $this->codigoDocumentoSector;
        $modelContingencia->tipoFacturaDocumento = $this->tipoFacturaDocumento;

        $modelContingencia->codigoEmision = 2;

        if ($modelContingencia->save()) {
            $this->idcontingencia = $modelContingencia->id;
            $model = Contingencia::findOne($modelContingencia->id);
            
            $modelInvoice = Invoice::findOne($this->id);
            
            $modelContingencia->fechaHoraInicioEvento = $modelInvoice->fechaEnvio;
            $dateTime = explode(' ', $modelContingencia->fechaHoraInicioEvento);
//            if ($this->cafc != null) {
//                $modelContingencia->fechaHoraInicioEvento = $dateTime[0] . 'T' . substr($dateTime[1], 0, 8) . '.000'; // . ':00.000';  
//                echo $modelContingencia->fechaHoraInicioEvento;
//            } else {
//                $modelContingencia->fechaHoraInicioEvento = $dateTime[0] . 'T' . substr($dateTime[1], 0, 12); // . ':00.000';  
//            }

            $modelContingencia->update();
            return $modelContingencia;
        }else{
            print_r($modelContingencia->getErrors());
        }
    }

    public function record(&$modelSale, $codigoMotivoEvento = 2) {

        $modelSiatCuis = $modelSale->idsystemPoint0->SiatCuisActive();

        $this->cafc = $modelSale->cafc; //Código cafc de Autorización de Facturas por Contingencia
        $this->codigoDocumentoSector = $modelSale->codigoDocumentoSector; //factura compra/venta
        $this->masivaFactura = 0; //$modelSale->masivaFactura; //factura compra/venta
        $this->codigoModalidad = $modelSale->codigoModalidad;

        $WsdlSiat = new WsdlSiat();
        //echo "[i codigoDocumentoSector=$this->codigoDocumentoSector]";
        $this->tipoFacturaDocumento = $WsdlSiat->tipoFacturaDocumento($this->codigoDocumentoSector);
        $this->serviceSWDL = $WsdlSiat->serviceSWDL($this->codigoDocumentoSector, $this->codigoModalidad);
        $this->typeXML = $WsdlSiat->typeXML($this->codigoDocumentoSector, $this->codigoModalidad);

        $WsdlSiat = new WsdlSiat($this->serviceSWDL);
        // echo "<span style=\"font-size:10px\">[s=$this->codigoDocumentoSector,tF=$this->tipoFacturaDocumento,SWDL=$this->serviceSWDL,XML=$this->typeXML]</span><br>";

        $this->idsale = $modelSale->id;

        if ($this->cafc == null) {

            $modelSiatCufd = $modelSale->idsystemPoint0->SiatCufdActive();
            if ($this->numeroFactura == null) {

                $modelSiatCuis->numeroFactura++;
                $this->numeroFactura = $modelSiatCuis->numeroFactura . '';
                $modelSale->numeroFactura = $modelSiatCuis->numeroFactura . '';

                $modelSiatCuis->save();
                $modelSale->update();
            }
        } else {
            if ($this->numeroFactura == null) {
                $criteria = new CDbCriteria;
                $criteria->addCondition("t.idstatus=10");
                $modelCafc = SiatCafc::model()->find($criteria);

                $modelCafc->numeroFactura++;

                $this->numeroFactura = $modelCafc->numeroFactura;
                $modelSale->numeroFactura = $modelCafc->numeroFactura;
                $modelCafc->save();
                $modelSale->save();
            } else {
                $modelSale->dateCreate = str_replace('-04', '.001-04', $modelSale->dateCreate);
            }

            $modelSiatCufd = $modelSale->idsystemPoint0->SiatCufdCafc($modelSale->dateCreate);
        }


//echo "[modelSale->numeroFactura2=$this->numeroFactura;]";
        //$this->numeroFactura = $modelSale->numeroFactura;
        $this->codigoMoneda = 1; //BOLIVIANOS
        $this->tipoCambio = 1;
        $this->codigoEmision = 1; //online

        $this->codigoAmbiente = $modelSale->idsystemPoint0->idsiatBranch0->codigoAmbiente;
        $this->codigoPuntoVenta = $modelSale->idsystemPoint0->codigoPuntoVenta;

        $this->codigoSistema = $modelSale->idsystemPoint0->idsiatBranch0->codigoSistema;
        $this->codigoSucursal = $modelSale->idsystemPoint0->idsiatBranch0->codigoSucursal;

        $this->cufd = $modelSiatCufd->cufd;
        $this->codigoControl = $modelSiatCufd->codigoControl;
        $this->cuis = $modelSiatCuis->cuis;

        $this->nitEmisor = $modelSale->idsystemPoint0->idsiatBranch0->nit;
        $this->razonSocialEmisor = $modelSale->idsystemPoint0->idsiatBranch0->razonSocial;

        $this->municipio = $modelSale->idsystemPoint0->idsiatBranch0->io->idcity0->name;
        $this->telefono = $modelSale->idsystemPoint0->idsiatBranch0->idsiatSystem0->io->numberPhone;

        $this->direccion = $modelSale->idsystemPoint0->idsiatBranch0->io->address;

        $this->phone = $modelSale->phone;
        $this->email = $modelSale->email;
        $this->nombreRazonSocial = $modelSale->razonSocial;
        $this->codigoTipoDocumentoIdentidad = $modelSale->codigoTipoDocumentoIdentidad; //NIT - NÚMERO DE IDENTIFICACIÓN TRIBUTARIA  
        $this->numeroDocumento = trim($modelSale->numeroDocumento);

        // echo "[$this->numeroDocumento]";
        $this->complemento = null; //Etiqueta xsi:nil=”true” o Valor que otorga el SEGIP p/ci duplicado
        $this->codigoCliente = ($modelSale->idcustomer != null ? $modelSale->idcustomer : null) . '';
        $this->codigoExcepcion = $modelSale->codigoExcepcion; // 1;
        // $modelSale->codigoExcepcion; //Por defecto, enviar cero (0) o nulo y uno (1) cuando se autorice el registro.

        if ($this->cafc != null && $this->codigoTipoDocumentoIdentidad == 5)
            $this->codigoExcepcion = 1;

        $this->codigoMetodoPago = 1; // $modelSale->codigoMetodoPago;
        $this->numeroTarjeta = null; // $modelSale->numeroTarjeta;

        $this->descuentoAdicional = $modelSale->discountamount;
        $this->montoTotal = $modelSale->montoTotal;
        $this->montoTotalMoneda = $modelSale->montoTotal;

        $this->montoTotalSujetoIva = $WsdlSiat->number($modelSale->montoTotal - $modelSale->montoGiftCard, 'number(2)');

        //echo "<br>[montoTotalSujetoIva =$this->montoTotalSujetoIva ]";

        $this->montoGiftCard = $modelSale->montoGiftCard; //Monto a ser cancelado con una Gift Card

        $this->leyenda = SincronizarListaLeyendasFactura::findOne(rand(1, 4))->descripcionLeyenda;
        $this->usuario = 'Vendedor';

        $products = new Productstock;
        $this->modelProducts = $products->getDocument($modelSale->iddocument)->getModels();

        // Prepare return _______________________________________________________________________________________________________________________________________
        $success = true;
        $message = '';
        // Prepare dateTime for send INVOICE ____________________________________________________________________________________________________________________

        $responseDate = $WsdlSiat->dateTime($modelSiatCufd); // $modelSiatCufd is very important for the date send INVOICE

        if ($modelSale->numeroFactura2 != null) {
            $responseDate = $WsdlSiat->dateTime2($modelSiatCufd);
        }


        //print_r($responseDate);
        if ($responseDate['success'] == false) {
            $success = false;
            $message = $responseDate['message'];
        }
        $dateCreate = $this->cafc == null ? $responseDate['date'] : $modelSale->dateCreate;
//        echo "[dc $dateCreate]"; 
//        $success = false;
        // echo "[dateCreate=$dateCreate]";

        $this->dateCreate = $dateCreate;
        $this->fechaEnvio = $dateCreate;
        $this->fechaEmision = $dateCreate;

        // Prepare Finish INVOICE _______________________________________________________________________________________________________________________________
        if ($success) {

            $this->numeroFactura = $this->numeroFactura . '';
            if (!$this->save()) {
                print_r($this->getErrors());
                $success = false;
                $message = 'Error: Al guardar la FACTURA ' . print_r($this->getErrors(), true);
            } else {

                $this->cuf = $WsdlSiat->cuf($this);
                $WsdlSiat->fileXml($this);

                $this->update();
                $respons = null;

                //return array('success' => false, 'message' => 'ddddmessage');

                if ($this->masivaFactura == false && $this->cafc == null && (new Siat())->cfg()->connection) {//&& Siat::cfg()->connection == 1
                    $respons = $this->getSWDL();

                    if ($respons != null && $respons->RespuestaServicioFacturacion->transaccion == false) {
                        $success = false;
                        $message = "SIAT ERROR [" . $respons->RespuestaServicioFacturacion->mensajesList->descripcion . "]";
                    }

                    $this->codigosRespuestas = print_r($respons, true);
                    $this->update();
                }

//             //RESPUESTA
                if ($respons != null && $respons->RespuestaServicioFacturacion->codigoDescripcion == 'VALIDADA') {
                    $this->codigoEstado = $respons->RespuestaServicioFacturacion->codigoEstado;
                    $this->codigoRecepcion = $respons->RespuestaServicioFacturacion->codigoRecepcion;
                    $this->transaccion = $respons->RespuestaServicioFacturacion->transaccion;
                    $this->codigoDescripcion = $respons->RespuestaServicioFacturacion->codigoDescripcion;
                    //$this->codigosRespuestas = print_r($respons->RespuestaServicioFacturacion->mensajesList, true);
                    $this->update();
                    //$this->sendApiWhatsapp();
                } else {
                    if ($respons == null) {
                        if ($this->codigoTipoDocumentoIdentidad == 5) {
                            $this->codigoExcepcion = 1;
                        }
                        if ($this->masivaFactura)
                            $this->codigoEmision = 3;
                        else
                            $this->codigoEmision = 2; //offline
                        $this->cuf = $WsdlSiat->cuf($this);

                        $WsdlSiat->fileXml($this);

                        if ($this->masivaFactura == 0)
                            $this->contingencia($codigoMotivoEvento, $modelSiatCufd->idcontingencia);

                        $this->update();
                        $this->sendApiWhatsapp();
                    } else {
                        $success = false;
                        $message = $this->codigosRespuestas;
                    }
                }

                //... 
            }

            $modelSale->modelInvoice = $this;
        }
        return array('success' => $success, 'message' => $message);
    }

    public function print($print = 0) {
        $paperPrinter = ucfirst(SystemPoint::getModelCurrent()->idsiatBranch0->io->paperPrinter);
        $codigoDocumentoSector = $this->codigoDocumentoSector * 1;
        if (!class_exists('FPDF', false)) {
            include('protected/modules/sale/facturacion/NumberToLetter.php');
            include('protected/modules/sale/facturacion/qrcode/qrlib.php');
            include('protected/extensions/fpdf/fpdf.php');
        }

        $cmd = '';
        switch ($this->codigoDocumentoSector) {
            case 1:
                $cmd = '$this->print' . $paperPrinter . '_01_FACTURA_COMPRA_VENTA($print)';
                break;
            case 2:
                $cmd = '$this->print' . $paperPrinter . '_02_FACTURA_DE_ALQUILER_DE_BIENES_INMUEBLES($print)';
                break;
            case 3:
                $cmd = '$this->print' . $paperPrinter . '_03_FACTURA_COMERCIAL_DE_EXPORTACION($print)';
                break;
            case 4:
                $cmd = '$this->print' . $paperPrinter . '_04_FACTURA_COMERCIAL_DE_EXPORTACION_EN_LIBRE_CONSIGNACION($print)';
                break;
            case 5:
                $cmd = '$this->print' . $paperPrinter . '_05_FACTURA_DE_ZONA_FRANCA($print)';
                break;
            case 6:
                $cmd = '$this->print' . $paperPrinter . '_06_FACTURA_DE_SERVICIO_TURISTICO_Y_HOSPEDAJE($print)';
                break;
            case 7:
                $cmd = '$this->print' . $paperPrinter . '_07_FACTURA_DE_COMERCIALIZACION_DE_ALIMENTOS_SEGURIDAD($print)';
                break;
            case 8:
                $cmd = '$this->print' . $paperPrinter . '_08_FACTURA_DE_TASA_CERO_POR_VENTA_DE_LIBROS_Y_TRANSPORTE_INTERNACIONAL_DE_CARGA($print)';
                break;
            case 9:
                $cmd = '$this->print' . $paperPrinter . '_09_FACTURA_DE_COMPRA_Y_VENTA_DE_MONEDA_EXTRANJERA($print)';
                break;
            case 10:
                $cmd = '$this->print' . $paperPrinter . '_10_FACTURA_DUTTY_FREE($print)';
                break;
            case 11:
                $cmd = '$this->print' . $paperPrinter . '_11_FACTURA_SECTORES_EDUCATIVOS($print)';
                break;
            case 12:
                $cmd = '$this->print' . $paperPrinter . '_12_FACTURA_DE_COMERCIALIZACION_DE_HIDROCARBUROS($print)';
                break;
            case 13:
                $cmd = '$this->print' . $paperPrinter . '_13_FACTURA_DE_SERVICIOS_BASICOS($print)';
                break;
            case 14:
                $cmd = '$this->print' . $paperPrinter . '_14_FACTURA_PRODUCTOS_ALCANZADOS_POR_EL_ICE($print)';
                break;
            case 15:
                $cmd = '$this->print' . $paperPrinter . '_15_FACTURA_DE_ENTIDADES_FINANCIERAS($print)';
                break;
            case 16:
                $cmd = '$this->print' . $paperPrinter . '_16_FACTURA_DE_HOTELES($print)';
                break;
            case 17:
                $cmd = '$this->print' . $paperPrinter . '_17_FACTURA_DE_HOSPITALES_CLINICAS($print)';
                break;
            case 18:
                $cmd = '$this->print' . $paperPrinter . '_18_FACTURA_DE_JUEGOS_DE_AZAR($print)';
                break;
            case 19:
                $cmd = '$this->print' . $paperPrinter . '_19_FACTURA_HIDROCARBUROS_ALCANZADA_IEHD($print)';
                break;
            case 20:
                $cmd = '$this->print' . $paperPrinter . '_20_FACTURA_COMERCIAL_DE_EXPORTACION_DE_MINERALES($print)';
                break;
            case 21:
                $cmd = '$this->print' . $paperPrinter . '_21_FACTURA_VENTA_INTERNA_MINERALES($print)';
                break;
            case 22:
                $cmd = '$this->print' . $paperPrinter . '_22_FACTURA_TELECOMUNICACIONES($print)';
                break;
            case 23:
                $cmd = '$this->print' . $paperPrinter . '_23_FACTURA_PREVALORADA($print)';
                break;
            case 24:
                $cmd = '$this->print' . $paperPrinter . '_24_NOTA_DE_CREDITO_DEBITO($print)';
                break;
            case 28:
                $cmd = '$this->print' . $paperPrinter . '_28_FACTURA_COMERCIAL_DE_EXPORTACION_DE_SERVICIOS($print)';
                break;
            case 29:
                $cmd = '$this->print' . $paperPrinter . '_29_NOTA_DE_CONCILIACION($print)';
                break;
            case 30:
                $cmd = '$this->print' . $paperPrinter . '_30_BOLETO_AEREO($print)';
                break;
            case 31:
                $cmd = '$this->print' . $paperPrinter . '_31_FACTURA_DE_SUMINISTRO($print)';
                break;
            case 32:
                $cmd = '$this->print' . $paperPrinter . '_32_FACTURA_ICE_ZONA_FRANCA($print)';
                break;
            case 33:
                $cmd = '$this->print' . $paperPrinter . '_33_FACTURA_TASA_CERO_BIENES_CAPITAL($print)';
                break;
            case 34:
                $cmd = '$this->print' . $paperPrinter . '_34_FACTURA_DE_SEGUROS($print)';
                break;
            case 35:
                $cmd = '$this->print' . $paperPrinter . '_35_FACTURA_COMPRA_VENTA_BONIFICACIONES($print)';
                break;
            case 36:
                $cmd = '$this->print' . $paperPrinter . '_36_FACTURA_PREVALORADA_SDCF($print)';
                break;
            case 37:
                $cmd = '$this->print' . $paperPrinter . '_37_FACTURA_DE_COMERCIALIZACION_DE_GNV($print)';
                break;
            case 38:
                $cmd = '$this->print' . $paperPrinter . '_38_FACTURA_HIDROCARBUROS_NO_ALCANZADA_IEHD($print)';
                break;
            case 39:
                $cmd = '$this->print' . $paperPrinter . '_39_FACTURA_COMERCIALIZACION_GN_Y_GLP($print)';
                break;
            case 40:
                $cmd = '$this->print' . $paperPrinter . '_40_FACTURA_DE_SERVICIOS_BASICOS_ZF($print)';
                break;
            case 41:
                $cmd = '$this->print' . $paperPrinter . '_41_FACTURA_COMPRA_VENTA_TASAS($print)';
                break;
            case 42:
                $cmd = '$this->print' . $paperPrinter . '_42_FACTURA_ALQUILER_ZF($print)';
                break;
            case 43:
                $cmd = '$this->print' . $paperPrinter . '_43_FACTURA_COMERCIAL_DE_EXPORTACION_HIDROCARBUROS($print)';
                break;
            case 44:
                $cmd = '$this->print' . $paperPrinter . '_44_FACTURA_IMPORTACION_COMERCIALIZACION_LUBRICANTES($print)';
                break;
            case 45:
                $cmd = '$this->print' . $paperPrinter . '_45_FACTURA_COMERCIAL_DE_EXPORTACION_PRECIO_VENTA($print)';
                break;
            case 46:
                $cmd = '$this->print' . $paperPrinter . '_46_FACTURA_SECTORES_EDUCATIVO_ZONA_FRANCA($print)';
                break;
            case 47:
                $cmd = '$this->print' . $paperPrinter . '_47_NOTA_CREDITO_DEBITO_DESCUENTO($print)';
                break;
            case 48:
                $cmd = '$this->print' . $paperPrinter . '_48_NOTA_CREDITO_DEBITO_ICE($print)';
                break;
            case 49:
                $cmd = '$this->print' . $paperPrinter . '_49_FACTURA_TELECOMUNICACIONES_ZONA_FRANCA($print)';
                break;
            case 50:
                $cmd = '$this->print' . $paperPrinter . '_50_FACTURA_DE_HOSPITALES_CLINICAS_ZONA_FRANCA($print)';
                break;
            case 51:
                $cmd = '$this->print' . $paperPrinter . '_51_FACTURA_ENGARRAFADORAS($print)';
                break;

            default:
                $cmd = '';
        }

        //echo "[$cmd]";
        //$cmd = '$this->print' . $paperPrinter . '_01_FACTURA_COMPRA_VENTA($print)';
        eval($cmd . ';');
    }

    public function printRoll_01_FACTURA_COMPRA_VENTA($print = 0) {

        $model = $this->idsale0;
        if ($print && ($this->idsale0->email == null || $this->idsale0->email == '')) {
            return;
        }
        $modelAutorizacion = SiatAutorizacion::model()->findByAttributes(array('cuf' => $this->cuf));

        $products = new Productstock;
        $products = $products->getDocument($model->iddocument)->getData();

        $fullNameCompany = $model->idsystemPoint0->idsiatBranch0->idsiatSystem0->io->fullNameCompany;
        $nameBranch = $model->idsystemPoint0->idsiatBranch0->io->fullName;

        $razonSocialEmisor = $this->razonSocialEmisor;
        $direccion = $this->direccion;
        $codigoPuntoVenta = $this->codigoPuntoVenta;
//$direccion = 'Claudio Aliaga #1211';
        $telefono = $this->telefono;
// $telefono = '71160952';
        $municipio = $this->municipio;
//$municipio = 'LA PAZ - BOLIVIA';
        $tipoFacturaDocumento = $this->tipoFacturaDocumento0->descripcion;
        $fechaEmision = $this->fechaEmision;
        $nombreRazonSocial = $this->nombreRazonSocial;

        $nitEmisor = $this->nitEmisor;

        $numeroDocumento = $this->numeroDocumento;
        if ($this->complemento != null) {
            $numeroDocumento .= '-' . $this->complemento;
        }

        $pdf = new FPDF('P', 'mm', array(sizeof($products) * 3 + 290, 79));
//Establecemos los márgenes izquierda, arriba y derecha:
        $pdf->SetMargins(6, 7, 7);

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 7);
        $fileLogo = "enterprise/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier . '/logo.jpg';

        if (file_exists($fileLogo)) {
            ;
            $pdf->Cell(10, 4, $pdf->Image($fileLogo, 12, 10), 15, 20, 'R', false);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
        }




        $tipoFacturaDocumento = str_replace('FACTURA ', '', $tipoFacturaDocumento);
        $pdf->MultiCell(0, 4, 'FACTURA', 0, 'C');

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->MultiCell(0, 4, $tipoFacturaDocumento, 0, 'C', false);

        $pdf->SetFont('Arial', 'B', 8);
        $razonSocialEmisor = iconv('utf-8', 'cp1252', $razonSocialEmisor);
        $pdf->MultiCell(0, 4, $razonSocialEmisor, 0, 'C');

        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(0, 4, $nameBranch, 0, 'C');

        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(0, 4, 'Nro Punto de Venta ' . $codigoPuntoVenta, 0, 'C');

        $pdf->SetFont('Arial', '', 9);
        $direccion = $direccion . ' - Tel:' . $telefono;
        $direccion = iconv('utf-8', 'cp1252', $direccion);
        $pdf->MultiCell(0, 4, $direccion, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(0, 4, $municipio, 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);

        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln();
        $pdf->MultiCell(0, 4, 'NIT: ' . $nitEmisor, 0, 'C');

        $pdf->MultiCell(0, 4, 'NRO. FACTURA: ' . $this->numeroFactura, 0, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, 'COD AUTORIZACION: ' . $this->cuf, 0, 'C');

        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'NOMBRE/RAZON SOCIAL:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);
        $nombreRazonSocial = iconv('utf-8', 'cp1252', $nombreRazonSocial);
        $pdf->MultiCell(0, 4, $nombreRazonSocial, 0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'NIT/CI/CEX:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, $numeroDocumento, 0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'COD. CLIENTE:', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 4, $this->codigoCliente, 0);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 4, 'FECHA  EMISION:', 0, 0, 'R');

        $pdf->SetFont('Arial', '', 9);

        $fecha2 = explode(' ', $fechaEmision);
        $fecha2 = $fecha2[1];
        $hora2 = substr($fecha2, 0, 2);
        $hora = substr($fecha2, 0, 5);
        $horario = 'PM';
        if ($hora2 * 1 >= 0 && $hora2 * 1 <= 11)
            $horario = 'AM';

        $pdf->MultiCell(0, 4, System::dateFormat($fechaEmision, 'd/m/Y ') . $hora . ' ' . $horario, 0);

        $pdf->SetFont('Arial', 'B', 9);
        $w = array(10, 31, 11, 12);

        $pdf->MultiCell(0, 4, 'DETALLE', 0, 'C');

        $pdf->SetFont('Arial', '', 7);
        $montoTotal = 0;

        $xml = new SimpleXMLElement($this->archivo);

        foreach ($xml->detalle as $productDetail) {

            $modelUnidadMedida = SiatUnidadMedida::model()->find('"codigoClasificador"=' . $productDetail->unidadMedida);
            // $name = ($productDetail->codigoProducto) . '-' . $productDetail->descripcion . '-' . $modelUnidadMedida->descripcion;
            $name = $productDetail->descripcion . '-' . $modelUnidadMedida->descripcion;
            $name = iconv('utf-8', 'cp1252', $name);
            $pdf->MultiCell(68, 4, $name, 0);
//            $pdf->Cell($w[0], 4, $name, 0, 0, 'L', false);
//
//            $pdf->Ln();

            $subTotal = $productDetail->cantidad * $productDetail->precioUnitario - $productDetail->montoDescuento;

            $pdf->Cell($w[0], 4, SGridView::number($productDetail->cantidad, 'number(2)') . ' X ' . SGridView::number($productDetail->precioUnitario, 'number(2)') . ' - ' . SGridView::number($productDetail->montoDescuento, 'number(2)'), 0, 0, '', false);
            $pdf->Cell($w[1], 4, '', 0, 0, 'L', false);

            $pdf->Cell($w[2], 4, '', 0, 0, 'R', false);
            $pdf->Cell($w[3], 4, SGridView::number($subTotal, 'number(2)'), 0, 0, 'R', false);
            $pdf->Ln();
            $montoTotal += $subTotal;
        }

// 1
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($w[0], 4, 'SUBTOTAL Bs', 'T', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', 'T', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', 'T', 0, 'R', false);
        $montoTotal = SGridView::number($this->montoTotal + $this->descuentoAdicional, 'number(2)');
        $pdf->Cell($w[3], 4, $montoTotal, 'T', 0, 'R', false);
        $pdf->Ln();
//fin
// 2
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($w[0], 4, 'DESCUENTO Bs', '', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', '', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', '', 0, 'R', false);
        $montoTotal = SGridView::number($this->montoTotal, 'number(2)');
        $pdf->Cell($w[3], 4, SGridView::number($this->descuentoAdicional, 'number(2)'), '', 0, 'R', false);
        $pdf->Ln();
//fin
// 3
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($w[0], 4, 'TOTAL Bs', '', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', '', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', '', 0, 'R', false);
        $montoTotal = SGridView::number($this->montoTotal, 'number(2)');
        $pdf->Cell($w[3], 4, $montoTotal, '', 0, 'R', false);
        $pdf->Ln();
//fin
// 4
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($w[0], 4, 'MONTO GIFT CARD Bs', '', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', '', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', '', 0, 'R', false);
        $montoGiftCard = SGridView::number($this->montoGiftCard, 'number(2)');
        $pdf->Cell($w[3], 4, $montoGiftCard, '', 0, 'R', false);
        $pdf->Ln();
//fin
// 5
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($w[0], 4, 'MONTO A PAGAR Bs', '', 0, 'L', false);
        $pdf->Cell($w[1], 4, '', '', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', '', 0, 'R', false);
        $montoTotal = SGridView::number($this->montoTotal - $this->montoGiftCard, 'number(2)');
        $pdf->Cell($w[3], 4, $montoTotal, '', 0, 'R', false);
        $pdf->Ln();
//fin
// 6
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($w[0], 4, 'IMPORTE BASE CREDITO FISCAL Bs', '', 0, 'L', false);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($w[1], 4, '', '', 0, 'L', false);
        $pdf->Cell($w[2], 4, '', '', 0, 'R', false);
        $montoTotalSujetoIva = SGridView::number($this->montoTotalSujetoIva, 'number(2)');
        $pdf->Cell($w[3], 4, $montoTotalSujetoIva, '', 0, 'R', false);
        $pdf->Ln();
        $pdf->Ln();
//fin


        $pdf->SetFont('Arial', '', 7);
        $NumberToLetter = new NumberToLetter();
        $numeroLiteral = $NumberToLetter->to_word($montoTotal);
        $total = explode('.', $montoTotal);

        $numeroLiteral = strtoupper($numeroLiteral) . ' ' . (sizeof($total) == 2 ? $total[1] : '00') . '/100 BOLIVIANOS';
        $pdf->MultiCell(0, 4, 'Son: ' . $numeroLiteral, 0);
        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');
//        $pdf->Ln();
//
//        $pdf->MultiCell(0, 4, 'MONTO PAGADO: ' . SGridView::number($model->montoRecibido, 'number(2)'), 0);
//        $pdf->MultiCell(0, 4, 'MONTO CAMBIO: ' . SGridView::number($model->montoCambio, 'number(2)'), 0);

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);

//$pdf->MultiCell(0, 4, 'CODIGO CONTROL: ' . $this->codigoControl, 0);

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 7);

        $leyenda = 'ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY';
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, 'C');
        $pdf->Ln();
        $leyenda = $this->leyenda;
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, 'C');

        $pdf->Ln();
        $leyenda = '"Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea"';
        if ($this->codigoEmision == 2)
            $leyenda = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo";
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, 'C');

        $pdf->SetFont('Arial', '', 8);
//  $pdf->MultiCell(0, 4, 'FECHA LIMITE DE EMISION: ' . System::dateFormat($modelAutorizacion->fechaLimite, 'd/m/Y'), 0);
//RQ - INVOICE
        $WsdlSiat = new WsdlSiat('Factura');
        $codeText = $WsdlSiat->url() . '/consulta/QR?nit=' . $nitEmisor . "&cuf=$this->cuf&numero=$this->numeroFactura&t=1";
        $fileQR = 'tmp/qr' . $this->numeroFactura . '-' . rand(1, 10000000) . '.png';
        QRcode::png($codeText, $fileQR, 'L', 4, 2, true);
// $pdf->Image($fileQR,10,10,-150);
        $pdf->Cell(0, 30, $pdf->Image($fileQR, 25, $pdf->GetY(), -150), 0, 0, 'R', false);
// END RQ
        $pdf->Ln();

        if ($print == 1) {
            $file = "tmpInvoices";
            if (!file_exists($file)) {
                mkdir($file);
            }

            $file .= "/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier;

            if (!file_exists($file)) {
                mkdir($file);
            }


            $file .= "/tmp";

            if (!file_exists($file)) {
                mkdir($file);
            }

            $pdf->Output('F', $file . '/' . $this->cuf . '.pdf');

            file_put_contents($file . '/' . $this->cuf . '.xml', $this->archivo);
            $this->sendMail();
        } else {

            $model = $this->idsale0;
            if (false && $model->idorder == null) {
                $pdf->AddPage('P', array(79, 80));

                $products = new Productstock;
                $products = $products->getDocument($model->iddocument)->getData();

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(0, 4, 'PARA LLEVAR.  VENTA # ' . $model->number, 0, 'C');

                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 4, 'CLIENTE:', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(0, 4, $model->razonSocial, 0);

                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 4, 'FECHA  EMISION:', 0, 0, 'L');

                $fecha2 = explode(' ', $model->dateCreate);
                $fecha2 = $fecha2[1];
                $hora2 = substr($fecha2, 0, 2);
                $hora = substr($fecha2, 0, 5);
                $horario = 'PM';
                if ($hora2 * 1 >= 0 && $hora2 * 1 <= 11)
                    $horario = 'AM';

                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(0, 4, System::dateFormat($model->dateCreate, 'd/m/Y') . ' ' . $hora . ' ' . $horario, 0);

                $pdf->SetFont('Arial', '', 7);
                $w = array(10, 31, 11, 12);

                $pdf->Cell($w[0], 4, 'CANT.', 'B', 0, 'L', false);
                $pdf->Cell($w[1], 4, 'DETALLE', 'B', 0, 'L', false);
                $pdf->Cell($w[2], 4, 'P.U.', 'B', 0, 'R', false);
                $pdf->Cell($w[3], 4, 'TOTAL', 'B', 0, 'R', false);
                $pdf->Ln();

                $montoTotal = 0;
                foreach ($products as $productDetail) {
                    $subTotal = $productDetail->quantityoutput * $productDetail->price;
                    $pdf->Cell($w[0], 4, SGridView::number($productDetail->quantityoutput, 'number(2)'), 0, 0, 'R', false);
                    $pdf->Cell($w[1], 4, $productDetail->idproduct0->name, 0, 0, 'L', false);
                    $pdf->Cell($w[2], 4, SGridView::number($productDetail->price, 'number(2)'), 0, 0, 'R', false);
                    $pdf->Cell($w[3], 4, SGridView::number($subTotal, 'number(2)'), 0, 0, 'R', false);
                    $pdf->Ln();
                    $montoTotal += $subTotal;
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell($w[0], 4, 'TOTAL', 'T', 0, 'L', false);
                $pdf->Cell($w[1], 4, '', 'T', 0, 'L', false);
                $pdf->Cell($w[2], 4, '', 'T', 0, 'R', false);
                $montoTotal = SGridView::number($model->montoTotal, 'number(2)');
                $pdf->Cell($w[3], 4, $montoTotal, 'T', 0, 'R', false);
                $pdf->Ln();
                $pdf->SetFont('Arial', '', 7);
            }


            $pdf->Output('', 'factura' . $this->numeroFactura . '.pdf');
        }
    }

    public function printLetter_01_FACTURA_COMPRA_VENTA($print = 0) {





        $model = $this->idsale0;
        if ($print && ($this->idsale0->email == null || $this->idsale0->email == '')) {
            return;
        }
        $modelAutorizacion = SiatAutorizacion::model()->findByAttributes(array('cuf' => $this->cuf));
//


        $fileLogo = "enterprise/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier . '/logo.jpg';

        $products = new Productstock;
        $products = $products->getDocument($model->iddocument)->getData();

        $fullNameCompany = $model->idsystemPoint0->idsiatBranch0->idsiatSystem0->io->fullNameCompany . '';
        $nameBranch = $model->idsystemPoint0->idsiatBranch0->io->fullName;

        $razonSocialEmisor = $this->razonSocialEmisor;
        $direccion = $this->direccion;
        $codigoPuntoVenta = $this->codigoPuntoVenta;
//$direccion = 'Claudio Aliaga #1211';
        //$telefono = $this->telefono;
        $telefono = $model->idsystemPoint0->idsiatBranch0->io->numberPhone;
// $telefono = '71160952';
        $municipio = $this->municipio;
//$municipio = 'LA PAZ - BOLIVIA'; 
        $tipoFacturaDocumento = $this->tipoFacturaDocumento0->descripcion;
        $fechaEmision = $this->fechaEmision;
        $nombreRazonSocial = $this->nombreRazonSocial;

        $nitEmisor = $this->nitEmisor;

        $numeroDocumento = $this->numeroDocumento;
        if ($this->complemento != null) {
            $numeroDocumento .= '-' . $this->complemento;
        }

        $pdf = new FPDF('P', 'mm', array(280, 216));
//Establecemos los márgenes izquierda, arriba y derecha:
        $pdf->SetMargins(10, 15, 10);
        $pdf->AliasNbPages();

        $pdf->AddPage();

        if (strlen($razonSocialEmisor) > 20)
            $pdf->SetFont('Arial', 'B', 10);
        else
            $pdf->SetFont('Arial', 'B', 8);

        //  $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 4, '', 0, 'C', 'C');
        $razonSocialEmisor = iconv('utf-8', 'cp1252', $razonSocialEmisor);
        $pdf->Cell(80, 4, $razonSocialEmisor, 0, 'C', 'C');
        //$pdf->Cell(80, 4, 'ioSoftware', 0, 'C', 'C');
        $pdf->SetFont('Arial', 'B', 10);
        // $pdf->Cell(15, 4, '', 0, 'L');
        $pdf->Cell(31, 4, 'NIT :', 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 4, $nitEmisor, 0, 'L');

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 4, '', 0, 'C', 'C');
        $pdf->Cell(80, 4, $nameBranch, 0, 'C', 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 4, '', 0, 'C', 'C');
        $pdf->Cell(80, 4, 'Nro Punto de Venta ' . $codigoPuntoVenta, 0, 'C', 'C');

        $pdf->SetFont('Arial', 'B', 10);
        // $pdf->Cell(15, 4, '', 0, 'L');
        $pdf->Cell(31, 4, 'Nro. FACTURA :', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(80, 4, $this->numeroFactura, 0, 'L');

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(50, 4, '', 0, 'C', 'C');
        $direccion = $direccion;
        $direccion = iconv('utf-8', 'cp1252', $direccion);
        $pdf->Cell(80, 4, $direccion, 0, '', 'C');

        $pdf->Ln();

        $pdf->SetFont('Arial', '',);
        $pdf->Cell(50, 4, '', 0, 'C', 'C');
        $dato = '';
        if ($telefono != '0') {
            $dato = '  Tel:' . $telefono . ' - ';
        }
        $municipio = $dato . $municipio;
        $pdf->Cell(80, 4, $municipio, 0, 'C', 'C');
        $pdf->SetFont('Arial', 'B', 8);
        //$pdf->Cell(5, 4, '', 0, 'L');
        $pdf->Cell(31, 4, 'COD AUTORIZACION :', 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(40, 4, $this->cuf, 0, 'L');
        $pdf->Ln();

        if (file_exists($fileLogo)) {


            $pdf->Cell(10, 4, $pdf->Image($fileLogo, 8, 13), 10, 20, 'R', false);
        }
        // $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 15);

        $tipoFacturaDocumento = str_replace('FACTURA ', '', $tipoFacturaDocumento);
        $pdf->MultiCell(0, 4, 'FACTURA', 0, 'C');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->MultiCell(0, 4, '(' . $tipoFacturaDocumento . ')', 0, 'C', false);

        $fecha2 = explode(' ', $fechaEmision);
        $fecha2 = $fecha2[1];
        $hora2 = substr($fecha2, 0, 2);
        $hora = substr($fecha2, 0, 5);
        $horario = 'PM';
        if ($hora2 * 1 >= 0 && $hora2 * 1 <= 11)
            $horario = 'AM';

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 4, 'FECHA:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 4, System::dateFormat($fechaEmision, 'd/m/Y ') . $hora . ' ' . $horario, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 4, 'NIT/CI/CEX:', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(29, 4, $numeroDocumento, 0, 0, 'L');

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(40, 4, 'NOMBRE/RAZON SOCIAL:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $nombreRazonSocial = iconv('utf-8', 'cp1252', $nombreRazonSocial);
        $pdf->Cell(50, 4, $nombreRazonSocial, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 4, 'COD. CLIENTE:', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(29, 4, $this->codigoCliente, 0, 0, 'L');

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $pdf->SetFont('Arial', 'B', 8);
        $w = array(25, 20, 30, 60, 20, 20, 20);

        $pdf->setFillColor(218, 217, 217);

        $pdf->Cell($w[0], 4, 'CODIGO', 'LT', 0, 'C', true);
        $pdf->Cell($w[1], 4, 'CANTIDAD', 'LT', 0, 'C', true);
        $pdf->Cell($w[2], 4, 'UNIDAD DE', 'LT', 0, 'C', true);
        $pdf->Cell($w[3], 4, 'DESCRIPCION', 'LT', 0, 'C', true);
        $pdf->Cell($w[4], 4, 'PRECIO', 'LT', 0, 'C', true);
        $pdf->Cell($w[5], 4, 'DESCUENTO', 'LT', 0, 'C', true);
        $pdf->Cell($w[6], 4, 'SUBTOTAL', 'LTR', 0, 'C', true);
        $pdf->Ln();

        $pdf->Cell($w[0], 4, 'PRODUCTO/', 'L', 0, 'C', true);
        $pdf->Cell($w[1], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[2], 4, 'MEDIDA', 'L', 0, 'C', true);
        $pdf->Cell($w[3], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[4], 4, 'UNITARIO', 'L', 0, 'C', true);
        $pdf->Cell($w[5], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[6], 4, '', 'LR', 0, 'C', true);

        $pdf->Ln();
        $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();
        $pdf->Cell($w[0], 4, 'SERVICIO', 'L', 0, 'C', true);
        $pdf->Cell($w[1], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[2], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[3], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[4], 4, '', 'L', 0, 'C', true);
        $pdf->Cell($w[5], 4, '', 'L', 0, 'C', true);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell($w[6], 4, '', 'LR', 0, 'C', true);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        $montoTotal = 0;

        $xml = new SimpleXMLElement($this->archivo);

//  $pdf->set_text_color(0,0,0);



        foreach ($xml->detalle as $productDetail) {
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            $height = 4;
            $pdf->SetFont('Arial', '', 8);
            $descripcion = iconv('utf-8', 'cp1252', $productDetail->descripcion);
//
            $pdf->SetTextColor(255, 255, 255);
            $pdf->MultiCell($w[3], 4, $descripcion);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
//
            $height = ($y - $y0);

            $pdf->SetXY($x0, $y0);
            $descripcion = iconv('utf-8', 'cp1252', $productDetail->descripcion);

            $pdf->SetTextColor(0, 0, 0);
            $modelUnidadMedida = SiatUnidadMedida::model()->find('"codigoClasificador"=' . $productDetail->unidadMedida);

            $pdf->Cell($w[0], $height, $productDetail->codigoProducto, 'LT', 0, 'L');
            $pdf->Cell($w[1], $height, SGridView::number($productDetail->cantidad, 'number(2)', false, ''), 'LT', 0, 'R');

            $pdf->SetFont('Arial', '', 7);
            $text = iconv('utf-8', 'cp1252', $modelUnidadMedida->descripcion);
            ;
            $pdf->Cell($w[2], $height, $text, 'LT', 0, 'L');
            $pdf->SetFont('Arial', '', 8);

            ;
//$pdf->Cell($w[3], 4, $descripcion, 'LT', 0, 'L');
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            $pdf->MultiCell($w[3], 4, $descripcion, 'LT');
            $pdf->SetXY($x0 + $w[3], $y0);
//            $pdf->Cell($w[0], 4, $name, 0, 0, 'L', false);
//
//            $pdf->Ln();

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell($w[4], $height, SGridView::number($productDetail->precioUnitario, 'number(2)'), 'LT', 0, 'R');
            $pdf->Cell($w[5], $height, $productDetail->montoDescuento, 'LT', 0, 'R');
            $subTotal = $productDetail->cantidad * $productDetail->precioUnitario - $productDetail->montoDescuento;
            $pdf->Cell($w[6], $height, SGridView::number($subTotal, 'number(2)'), 'LTR', 0, 'R');
            $pdf->Ln();

            $montoTotal += $subTotal;
        }

// 1
        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(105, 4, '', 'T', 0, 'L', false);

        $pdf->Cell(70, 4, 'SUBTOTAL Bs', 'LTRB', 0, 'R', true);
        $montoTotal = SGridView::number($this->montoTotal + $this->descuentoAdicional, 'number(2)');
        $pdf->Cell(20, 4, $montoTotal, 'LTRB', 0, 'R', true);

        $pdf->Ln();
//fin
// 2
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(105, 4, '', '', 0, 'L', false);
        $pdf->Cell(70, 4, 'DESCUENTO Bs', 'LTRB', 0, 'R', true);
        $montoTotal = SGridView::number($this->montoTotal, 'number(2)');
        $pdf->Cell(20, 4, SGridView::number($this->descuentoAdicional, 'number(2)'), 'LTRB', 0, 'R', true);
        $pdf->Ln();
//fin
// 3
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(105, 4, '', '', 0, 'L', false);
        $pdf->Cell(70, 4, 'TOTAL Bs', 'LTRB', 0, 'R', true);
        $montoTotal = SGridView::number($this->montoTotal, 'number(2)');
        $pdf->Cell(20, 4, $montoTotal, 'LTRB', 0, 'R', true);
        $pdf->Ln();
//fin
// 4
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(105, 4, '', '', 0, 'L', false);
        $pdf->Cell(70, 4, 'MONTO GIFT CARD Bs', 'LTRB', 0, 'R', true);
        $montoGiftCard = SGridView::number($this->montoGiftCard, 'number(2)');
        $pdf->Cell(20, 4, $montoGiftCard, 'LTRB', 0, 'R', true);
        $pdf->Ln();
//fin
// 5
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(105, 4, '', '', 0, 'L', false);
        $pdf->Cell(70, 4, 'MONTO A PAGAR Bs', 'LTRB', 0, 'R', true);
        $montoTotal = SGridView::number($this->montoTotal - $this->montoGiftCard, 'number(2)');
        $pdf->Cell(20, 4, $montoTotal, 'LTRB', 0, 'R', true);
        $pdf->Ln();
//fin
// 6
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(105, 4, '', '', 0, 'L', false);
        $pdf->Cell(70, 4, 'IMPORTE BASE CREDITO FISCAL Bs', 'LTRB', 0, 'R', true);
        $montoTotalSujetoIva = SGridView::number($this->montoTotalSujetoIva, 'number(2)');
        $pdf->Cell(20, 4, $montoTotalSujetoIva, 'LTRB', 0, 'R', true);

        $pdf->Ln();
//fin
//        $pdf->SetFont('Arial', 'B', 10);
//        $pdf->Cell(105, 4, '', '', 0, 'L', false);
//        $pdf->Cell(70, 4, 'TOTAL USD', 'LTRB', 0, 'R', true);
//        $montoTotalSujetoIva = SGridView::number($this->montoTotalSujetoIva/6.96, 'number(2)');
//        $pdf->Cell(20, 4, $montoTotalSujetoIva, 'LTRB', 0, 'R', true);
//        
//        $pdf->Ln();
        //fin


        $pdf->SetFont('Arial', '', 10);
        $NumberToLetter = new NumberToLetter();
        $numeroLiteral = $NumberToLetter->to_word($montoTotal);
        $total = explode('.', $montoTotal);

        $numeroLiteral = strtoupper($numeroLiteral) . ' ' . (sizeof($total) == 2 ? $total[1] : '00') . '/100 BOLIVIANOS';
        $pdf->MultiCell(0, 4, 'Son: ' . $numeroLiteral, 0);
        /* BREAK LINE */$pdf->Cell(0, 1, '', 'B', 0, 'L');
//        $pdf->Ln();
//
//        $pdf->MultiCell(0, 4, 'MONTO PAGADO: ' . SGridView::number($model->montoRecibido, 'number(2)'), 0);
//        $pdf->MultiCell(0, 4, 'MONTO CAMBIO: ' . SGridView::number($model->montoCambio, 'number(2)'), 0);

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);

//$pdf->MultiCell(0, 4, 'CODIGO CONTROL: ' . $this->codigoControl, 0);

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(30, 4, '', 0, '');
        $leyenda = 'ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY';
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, '');
        $pdf->Ln();

        $pdf->Cell(30, 4, '', 0, '');
        $leyenda = $this->leyenda;
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, '');

        $pdf->Ln();

        $pdf->Cell(30, 4, '', 0, '');
        $leyenda = '"Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea"';
        if ($this->codigoEmision == 2)
            $leyenda = "Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo";
        $leyenda = iconv('utf-8', 'cp1252', $leyenda);
        $pdf->MultiCell(0, 4, $leyenda, 0, '');

        $pdf->SetFont('Arial', '', 8);
//  $pdf->MultiCell(0, 4, 'FECHA LIMITE DE EMISION: ' . System::dateFormat($modelAutorizacion->fechaLimite, 'd/m/Y'), 0);
//RQ - INVOICE
        $WsdlSiat = new WsdlSiat('Factura');
        $codeText = $WsdlSiat->url() . '/consulta/QR?nit=' . $nitEmisor . "&cuf=$this->cuf&numero=$this->numeroFactura&t=1";
        $fileQR = 'tmp/qr' . $this->numeroFactura . '-' . rand(1, 10000000) . '.png';
        QRcode::png($codeText, $fileQR, 'L', 4, 2, true);
// $pdf->Image($fileQR,10,10,-150);
        $pdf->Cell(0, 15, $pdf->Image($fileQR, 10, $pdf->GetY() - 20, 25), 0, 0, 'R', false);
// END RQ
//        $pdf->Ln();
//        $pdf->SetFont('Arial', 'B', 10);
//        $text = '"EL VALOR DE LA FACTURA DEBERÁ SER PAGADO EN DÓLARES O EN BOLIVIANOS AL TIPO DE CAMBIO DE VENTA AL MOMENTO DEL PAGO"';
//        $text = iconv('utf-8', 'cp1252', $text);
//        $pdf->MultiCell(195, 6, $text, 'LTRB', 'C');
//
//        $text = '"UNA VEZ ENTREGADO EL PRODUCTO CORRE A CUENTA Y RIESGO DEL CLIENTE"';
//        $text = iconv('utf-8', 'cp1252', $text);
//        $pdf->MultiCell(195, 6, $text, '', 'C');

        $pdf->Ln();

        if ($print == 1) {
            $file = "tmpInvoices";
            if (!file_exists($file)) {
                mkdir($file);
            }

            $file .= "/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier;

            if (!file_exists($file)) {
                mkdir($file);
            }


            $file .= "/tmp";

            if (!file_exists($file)) {
                mkdir($file);
            }

            $pdf->Output('F', $file . '/' . $this->cuf . '.pdf');

            file_put_contents($file . '/' . $this->cuf . '.xml', $this->archivo);
            $this->sendMail();
        } else {
//            $model = $this->idsale0;
//            if ($model->idorder == null) {
//                $pdf->AddPage('P', array(79, 80));
//
//                $products = new Productstock;
//                $products = $products->getDocument($model->iddocument)->getData();
//
//                $pdf->SetFont('Arial', 'B', 8);
//                $pdf->MultiCell(0, 4, 'PARA LLEVAR.  VENTA # ' . $model->number, 0, 'C');
//
//                $pdf->SetFont('Arial', 'B', 7);
//                $pdf->Cell(25, 4, 'CLIENTE:', 0, 0, 'L');
//                $pdf->SetFont('Arial', '', 9);
//                $pdf->MultiCell(0, 4, $model->nameClient, 0);
//
//                $pdf->SetFont('Arial', 'B', 7);
//                $pdf->Cell(25, 4, 'FECHA  EMISION:', 0, 0, 'L');
//
//                $fecha2 = explode(' ', $model->dateCreate);
//                $fecha2 = $fecha2[1];
//                $hora2 = substr($fecha2, 0, 2);
//                $hora = substr($fecha2, 0, 5);
//                $horario = 'PM';
//                if ($hora2 * 1 >= 0 && $hora2 * 1 <= 11)
//                    $horario = 'AM';
//
//                $pdf->SetFont('Arial', '', 9);
//                $pdf->MultiCell(0, 4, System::dateFormat($model->dateCreate, 'd/m/Y') . ' ' . $hora . ' ' . $horario, 0);
//
//                $pdf->SetFont('Arial', '', 7);
//                $w = array(10, 31, 11, 12);
//
//                $pdf->Cell($w[0], 4, 'CANT.', 'B', 0, 'L', false);
//                $pdf->Cell($w[1], 4, 'DETALLE', 'B', 0, 'L', false);
//                $pdf->Cell($w[2], 4, 'P.U.', 'B', 0, 'R', false);
//                $pdf->Cell($w[3], 4, 'TOTAL', 'B', 0, 'R', false);
//                $pdf->Ln();
//
//                $montoTotal = 0;
//                foreach ($products as $productDetail) {
//                    $subTotal = $productDetail->quantityoutput * $productDetail->price;
//                    $pdf->Cell($w[0], 4, SGridView::number($productDetail->quantityoutput, 'number(2)'), 0, 0, 'R', false);
//                    $pdf->Cell($w[1], 4, $productDetail->idproduct0->name, 0, 0, 'L', false);
//                    $pdf->Cell($w[2], 4, SGridView::number($productDetail->price, 'number(2)'), 0, 0, 'R', false);
//                    $pdf->Cell($w[3], 4, SGridView::number($subTotal, 'number(2)'), 0, 0, 'R', false);
//                    $pdf->Ln();
//                    $montoTotal += $subTotal;
//                }
//                $pdf->SetFont('Arial', 'B', 8);
//                $pdf->Cell($w[0], 4, 'TOTAL', 'T', 0, 'L', false);
//                $pdf->Cell($w[1], 4, '', 'T', 0, 'L', false);
//                $pdf->Cell($w[2], 4, '', 'T', 0, 'R', false);
//                $montoTotal = SGridView::number($model->montoTotal, 'number(2)');
//                $pdf->Cell($w[3], 4, $montoTotal, 'T', 0, 'R', false);
//                $pdf->Ln();
//                $pdf->SetFont('Arial', '', 7);
//            }
//

            $pdf->Output();
        }
    }
   
    public function sentToFile() {
        $file = "tmpInvoices";
        if (!file_exists($file)) {
            mkdir($file);
        }

        $file .= "/" . SystemPoint::getModelCurrent()->idsiatBranch0->idsiatSystem0->io->dbidentifier;

        if (!file_exists($file)) {
            mkdir($file);
        }

        $string = "id=[$this->id]" . chr(10);
        $string .= "dateCreate=[$this->dateCreate]" . chr(10);
        $string .= "recycleBin=[$this->recycleBin]" . chr(10);
        $string .= "iduser=[$this->iduser]" . chr(10);
        $string .= "codigoModalidad=[$this->codigoModalidad]" . chr(10);
        $string .= "idsale=[$this->idsale]" . chr(10);
        $string .= "idpurchase=[$this->idpurchase]" . chr(10);
        $string .= "cufd=[$this->cufd]" . chr(10);
        $string .= "codigoControl=[$this->codigoControl]" . chr(10);
        $string .= "cuis=[$this->cuis]" . chr(10);
        $string .= "numeroFactura=[$this->numeroFactura]" . chr(10);
        $string .= "codigoEmision=[$this->codigoEmision]" . chr(10);
        $string .= "id=[$this->cuf]" . chr(10);
        $string .= "codigoAmbiente=[$this->codigoAmbiente]" . chr(10);
        $string .= "codigoPuntoVenta=[$this->codigoPuntoVenta]" . chr(10);
        $string .= "codigoSistema=[$this->codigoSistema]" . chr(10);
        $string .= "codigoSistema=[$this->codigoSistema]" . chr(10);
        $string .= "codigoDocumentoSector=[$this->codigoDocumentoSector]" . chr(10);
        $string .= "tipoFacturaDocumento=[$this->tipoFacturaDocumento]" . chr(10);
        $string .= "archivo=[$this->archivo]" . chr(10);
        $string .= "fechaEnvio=[$this->fechaEnvio]" . chr(10);
        $string .= "hashArchivo=[***]" . chr(10);
        $string .= "codigoEstado=[$this->codigoEstado]" . chr(10);
        $string .= "codigoRecepcion=[$this->codigoRecepcion]" . chr(10);
        $string .= "transaccion=[$this->transaccion]" . chr(10);
        $string .= "codigoDescripcion=[$this->codigoDescripcion]" . chr(10);
        $string .= "codigosRespuestas=[$this->codigosRespuestas]" . chr(10);
        $string .= "nitEmisor=[$this->nitEmisor]" . chr(10);
        $string .= "razonSocialEmisor=[$this->razonSocialEmisor]" . chr(10);
        $string .= "municipio=[$this->municipio]" . chr(10);
        $string .= "telefono=[$this->telefono]" . chr(10);
        $string .= "direccion=[$this->direccion]" . chr(10);
        $string .= "fechaEmision=[$this->fechaEmision]" . chr(10);
        $string .= "nombreRazonSocial=[$this->nombreRazonSocial]" . chr(10);
        $string .= "codigoTipoDocumentoIdentidad=[$this->codigoTipoDocumentoIdentidad]" . chr(10);
        $string .= "numeroDocumento=[$this->numeroDocumento]" . chr(10);
        $string .= "complemento=[$this->complemento]" . chr(10);
        $string .= "codigoCliente=[$this->codigoCliente]" . chr(10);
        $string .= "codigoMetodoPago=[$this->codigoMetodoPago]" . chr(10);
        $string .= "numeroTarjeta=[$this->numeroTarjeta]" . chr(10);
        $string .= "montoTotal=[$this->montoTotal]" . chr(10);
        $string .= "montoTotalSujetoIva=[$this->montoTotalSujetoIva]" . chr(10);
        $string .= "montoGiftCard=[$this->montoGiftCard]" . chr(10);
        $string .= "descuentoAdicional=[$this->descuentoAdicional]" . chr(10);
        $string .= "codigoExcepcion=[$this->codigoExcepcion]" . chr(10);
        $string .= "cafc=[$this->cafc]" . chr(10);
        $string .= "codigoMoneda=[$this->codigoMoneda]" . chr(10);
        $string .= "tipoCambio=[$this->tipoCambio]" . chr(10);
        $string .= "montoTotalMoneda=[$this->montoTotalMoneda]" . chr(10);
        $string .= "leyenda=[$this->leyenda]" . chr(10);
        $string .= "usuario=[$this->usuario]" . chr(10);
        $string .= "fechaLimiteEmision=[$this->fechaLimiteEmision]" . chr(10);
        $string .= "cufdAnulacion=[$this->cufdAnulacion]" . chr(10);
        $string .= "responseAnulacion=[$this->responseAnulacion]" . chr(10);
        $string .= "codigoDescripcionAnulacion=[$this->codigoDescripcionAnulacion]" . chr(10);
        $string .= "codigoEstadoAnulacion=[$this->codigoEstadoAnulacion]" . chr(10);
        $string .= "transaccionAnulacion=[$this->transaccionAnulacion]" . chr(10);
        $string .= "idsiatContingencia=[$this->idcontingencia]" . chr(10);

        $string .= "______________________________________________________________________________________________________________________________" . chr(10);

        $file .= "/error.siat";

        $fp = fopen($file, 'a');
        fwrite($fp, $string);
        fwrite($fp, chr(10));

        fclose($fp);
    }

    public function getIdcontingencia0() {
        return $this->hasOne(Contingencia::class, ['id' => 'idcontingencia']);
    }


    public function getIdsale0() {
        return $this->hasOne(Sale::class, ['id' => 'idsale']);
    }
}
