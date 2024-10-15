<?php

namespace app\modules\apiv1\helpers;

use Yii;
use yii\helpers\Html;
use Mpdf\Mpdf;

use app\modules\apiv1\helpers\DataCompany;
use app\modules\apiv1\models\MetodoPago;
use app\modules\apiv1\models\Customer;

class Pdf
{
    public static $MONEDA = 'Bs.';
    public static function generateReceiptPdf($receipt, $ioSystemBranch)
    {
        $companyData = [
            'fullNameCompany' => $ioSystemBranch['cfgIoSystem']['fullNameCompany'],
            'numberPhone' => $ioSystemBranch['numberPhone'],
            'address' => $ioSystemBranch['address']
        ];

        // Obtener datos del cliente
        $customer = Customer::findOne(['id' => $receipt->idcustomer]);
        $customerName = $customer ? $customer->razonSocial : 'Desconocido';
        $customerDocumentNumber = $customer ? $customer->numeroDocumento : 'Desconocido';

        $metodoPago = MetodoPago::findOne(['id' => $receipt->codigoMetodoPago]);
        $metodoPagoDescription = $metodoPago ? $metodoPago->descripcion : 'Desconocido';

        // Configuración de mPDF
        $mpdf = new Mpdf([
            'format' => [140, 216], // Media hoja: 140 mm x 216 mm
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => sys_get_temp_dir(), // Usa el directorio temporal del sistema
        ]);

        // Generar el contenido HTML del recibo
        $htmlContent = self::renderReceiptHtml($receipt, $companyData, $metodoPagoDescription, $customerName, $customerDocumentNumber);

        // Escribir el contenido HTML en el PDF
        $mpdf->WriteHTML($htmlContent);

        // Salida del PDF directamente al navegador sin usar archivos temporales
        return $mpdf->Output('recibo.pdf', \Mpdf\Output\Destination::INLINE);
    }

    private static function renderReceiptHtml($receipt, $companyData, $metodoPago, $customerName, $customerDocumentNumber)
    {
        // Extraer datos del recibo
        $dateCreate = Yii::$app->formatter->asDate($receipt->dateCreate, 'php:d/m/Y H:i:s');
        $number = Html::encode($receipt->number);
        $codigoMetodoPago = Html::encode($receipt->codigoMetodoPago);
        $montoTotal = number_format($receipt->montoTotal, 2, '.', ',');

        // Extraer datos de las ventas
        $sales = $receipt->saleReceipt;
        $salesHtml = '';
        foreach ($sales as $sale) {
            $saleDate = Yii::$app->formatter->asDate($sale->dateCreate, 'php:d/m/Y H:i:s');
            $saleAmount = number_format($sale->monto, 2, '.', ',');
            $saleNumber = Html::encode($sale->sale->number);

            $salesHtml .= "<tr>
                <td style='padding: 10px 10px;'>$saleDate</td>
                <td style='text-align: center;'>$saleAmount " . self::$MONEDA . "</td>
                <td style='text-align: right;'>$saleNumber</td>
            </tr>";
        }

        ob_start();
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recibo de Pago</title>
            <style>
                body {
                    font-family: 'Helvetica Neue', Helvetica, "Segoe UI", Arial, sans-serif;
                    font-size: 13px;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    padding: 0 15px;
                    margin: 0 auto;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 10px;
                    vertical-align: top;
                }
                th {
                    text-align: left;
                }
                .text-right {
                    text-align: right;
                }
                .font-weight-bold {
                    font-weight: bold;
                }
                .font-size-18 {
                    font-size: 18px;
                }
                .font-size-22 {
                    font-size: 22px;
                }
                .font-size-30 {
                    font-size: 30px;
                }
                .letter-spacing-1 {
                    letter-spacing: 1px;
                }
                .border-bottom {
                    border-bottom: 2px solid #666666;
                }
                .padding-bottom-10 {
                    padding-bottom: 10px;
                }
                .margin-top-40 {
                    margin-top: 40px;
                }
                .margin-top-50 {
                    margin-top: 50px;
                }
                .margin-top-60 {
                    margin-top: 60px;
                }
                .padding-top-20 {
                    padding-top: 20px;
                }
                .hr {
                    border-top: 1px solid #666666;
                }
                .no-border {
                    border: none;
                }
                .details-table th, .details-table td {
                    border: 1px solid #ddd;
                }
            </style>
        </head>
        <body>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <tr>
                    <td>N° <?= str_pad($receipt->number, 6, '0', STR_PAD_LEFT) ?></td>
                    <td class="text-right">Fecha: <?= $dateCreate ?></td>
                </tr>
            </table>
        </div>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <tr>
                    <td style="width: 50%;">
                        <img style="width: 100px; height: 100px;" src="https://res.cloudinary.com/dbfghtyws/image/upload/v1724329789/icono_app_pqgrot.png" alt="" />
                    </td>
                    <td style="width: 50%;" class="text-right padding-top-20">
                        <div class="font-size-18 font-weight-bold padding-bottom-6"><?= Html::encode($companyData['fullNameCompany']) ?></div>
                        <div class="padding-bottom-6">Dirección: <?= Html::encode($companyData['address']) ?></div>
                        <div>Teléfono: <?= Html::encode($companyData['numberPhone']) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <tr>
                    <td colspan="2" class="text-center font-size-22 font-weight-300 letter-spacing-1" style="text-align: center;">
                        RECIBO DE PAGO
                    </td>
                </tr>
            </table>
        </div>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <!-- <tr>
                    <td colspan="2" class="title-section font-size-16 letter-spacing-1 border-bottom padding-bottom-10">
                        DETALLES DE LA TRANSACCIÓN
                    </td>
                </tr> -->
                <tr>
                    <td style="width: 50%;">
                        <div class="letter-spacing-1 font-weight-300 padding-top-10" style="color: #999999">CLIENTE</div>
                        <div class="font-size-18"><?= Html::encode($customerName) ?></div>
                    </td>
                    <td style="width: 50%;" class="text-right">
                        <div class="letter-spacing-1 font-weight-300 padding-top-10" style="color: #999999">NÚMERO DE DOCUMENTO</div>
                        <div class="font-size-18"><?= Html::encode($customerDocumentNumber) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <tr>
                    <td colspan="2" class="title-section font-size-16 letter-spacing-1 border-bottom" style="margin-bottom: 5px">
                        REFERENTE A VENTAS
                    </td>
                </tr>
            </table>
            <table class="details-table" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th style="text-align: center;">Monto</th>
                        <th style="text-align: right;">Número de Venta</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $salesHtml ?>
                </tbody>
            </table>
        </div>

        <div class="container" style="margin-top: 20px">
            <table class="no-border">
                <tr>
                    <td style="width: 50%;">
                        <div class="font-size-18" style="color: #999999">MÉTODO DE PAGO</div>
                        <div class="font-size-22"><?= Html::encode($metodoPago) ?></div>
                    </td>
                    <td class="text-right">
                        <div class="font-size-18" style="color: #999999">MONTO TOTAL</div>
                        <div class="font-size-22"><?= Html::encode($montoTotal) ?> <?= self::$MONEDA?></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="container" style="margin-top: 25px">
            <table class="no-border">
                <tr>
                    <td style="text-align: center">
                        <div>...........................................</div>
                        <div>Entregue conforme</div>
                    </td>
                    <td style="text-align: center">
                        <div>...........................................</div>
                        <div>Recibi conforme</div>
                    </td>
                </tr>
            </table>
        </div>
        </body>
        </html>

        <?php
        return ob_get_clean();
    }

}
