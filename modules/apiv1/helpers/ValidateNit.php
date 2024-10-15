<?php 
namespace app\modules\apiv1\helpers;

use app\modules\apiv1\models\SiatTipoDocumentoIdentidad;
use app\modules\ioLib\helpers\WsdlSiat; 
use app\models\Siat;
use app\models\UserSystemPoint;

class ValidateNit {

    public static function isValid($nit)
    {
        $codigoExcepcion = 0;
        $msg = '';
        $connection = 0;

        try {
            $wsdlSiat = new wsdlSiat('ServicioFacturacionCompraVenta');
            if ($wsdlSiat->success()) {
                $resp = $wsdlSiat->run('verificarComunicacion');

                $siat = Siat::findOne(1);
                if ($resp->return->transaccion == 1) {
                    $siat->conn(1);
                    $connection = 1;
                } else {
                    $siat->conn(0);
                    $connection = 0;
                }

                // Verifica si la respuesta es válida y contiene datos iterables
                if (is_array($resp->return) || is_object($resp->return)) {
                    // Procesa los datos aquí
                    // foreach($resp->return as $item) {
                    //     // Código para procesar cada item
                    // }
                } else {
                    throw new \Exception('Respuesta no válida del servicio.');
                }
            }
        } catch (\Exception $e) {
            // Maneja excepciones y errores
            $msg = $e->getMessage();
            $codigoExcepcion = -1; // Código para indicar error
            $codigoExcepcion = -1; 
        }

        if ($connection == 1) {
            $codigoExcepcion = self::verifiedNit($nit, $msg);
        }

        return ['codigoExcepcion' => $codigoExcepcion, 'msg' => $msg, 'connection' => $connection];
    }


    public static function verifiedNit($nit, &$msg) {
        
        $nit = trim($nit);
        $wsdlSiat = new wsdlSiat('FacturacionCodigos');

        $modelUser = UserSystemPoint::getModel();
        $modelSystemPoint = $modelUser->idsystemPoint0;

        $params = array(
            'SolicitudVerificarNit' => array(
                'codigoAmbiente' => $modelSystemPoint->idsiatBranch0->codigoAmbiente,
                'codigoModalidad' => $modelSystemPoint->idsiatBranch0->codigoModalidad,
                'codigoSistema' => $modelSystemPoint->idsiatBranch0->codigoSistema,
                'codigoSucursal' => $modelSystemPoint->idsiatBranch0->codigoSucursal,
                'cuis' => $modelSystemPoint->SiatCuisActive()->cuis,
                'nit' => $wsdlSiat::$nit,
                'nitParaVerificacion' => $nit
            )
        );
        
        if ($wsdlSiat::success()) {
            $respons = $wsdlSiat::run('verificarNit', $params);
            if ($respons->RespuestaVerificarNit != null) {
                $codigo = $respons->RespuestaVerificarNit->mensajesList->codigo;
                $msg = $descripcion = $respons->RespuestaVerificarNit->mensajesList->descripcion;
                $transaccion = $respons->RespuestaVerificarNit->transaccion;
                return $descripcion == 'NIT ACTIVO';
            }
        }

        return false;
    }
}