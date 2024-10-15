<?php

namespace app\modules\apiv1\controllers;

use Yii;
use yii\web\Controller; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\modules\apiv1\models\Email;

class SendmailController extends Controller
{
    public $enableCsrfValidation = false;
    private $emailAddress;
    private $emailKey;
    private $emailHost;

    public function __construct($id, $module, $config = [])
    {
        $mailParams = Yii::$app->params['mail'];
        $this->emailAddress = $mailParams['emailAddress'];
        $this->emailKey = $mailParams['emailKey'];
        $this->emailHost = $mailParams['emailHost'];

        parent::__construct($id, $module, $config);
    }
    /**
     * Enviar correo electrónico.
     * 
     * @return array
     */
    public function actionIndex()
    {
        // Crear una instancia del modelo de correo
        $emailModel = new Email();

        // Cargar los datos del modelo desde la solicitud POST
        if ($emailModel->load(Yii::$app->request->post(), '') && $emailModel->validate()) {
            // Crear una instancia de PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host       = $this->emailHost; // Servidor SMTP configurado
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->emailAddress; // Tu dirección de correo
                $mail->Password   = $this->emailKey; // Tu contraseña del correo
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usa STARTTLS
                $mail->Port       = 587; // Puerto para STARTTLS
                $mail->SMTPDebug  = 0;

                // Configurar destinatarios y contenido
                $mail->setFrom($this->emailAddress);
                $mail->addAddress($emailModel->to);
                $mail->Subject = $emailModel->subject;
                $mail->isHTML(true);
                $mail->Body    = $emailModel->body;
                $mail->AltBody = $emailModel->altBody;

                // Rutas de los archivos a adjuntar
                $filePaths = [
                    Yii::getAlias('@webroot') . '/files/invoice.pdf',
                    // Yii::getAlias('@webroot') . '/files/invoice2.pdf',
                ];

                // Adjuntar los archivos
                foreach ($filePaths as $filePath) {
                    if (file_exists($filePath)) {
                        $mail->addAttachment($filePath);
                    } else {
                        return $this->sendResponse([
                            'statusCode' => 404,
                            'message' => "El archivo no se encontró: {$filePath}"
                        ]);
                    }
                }

                // Enviar el correo
                $mail->send();
                return $this->sendResponse([
                    'statusCode' => 200,
                    'message' => 'El mensaje ha sido enviado'
                ]);
            } catch (Exception $e) {
                return $this->sendResponse([
                    'statusCode' => 500,
                    'message' => "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}"
                ]);
            }
        } else {
            return $this->sendResponse([
                'statusCode' => 422,
                'message' => 'Datos de correo inválidos',
                'errors' => $emailModel->errors
            ]);
        }
    }

    /**
     * Envía una respuesta JSON.
     * 
     * @param array $response
     * @return array
     */
    protected function sendResponse($response)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $response['statusCode'];
        
        $responseData = ['message' => $response['message']];
        
        if (isset($response['name'])) {
            $responseData['name'] = $response['name'];
        }
        
        if (isset($response['errors'])) {
            $responseData['errors'] = $response['errors'];
        }
        
        if (isset($response['data'])) {
            $responseData['data'] = $response['data'];
        }
        
        return $responseData;
    }
}
