<?php
namespace app\modules\apiv1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\modules\apiv1\models\Receipt;
use app\modules\apiv1\helpers\Pdf;
use app\models\IoSystemBranch;
use app\modules\service\helpers\DbConnection;

class PdfController extends Controller
{
    private $dbUser;
    private $dbPassword;
    private $dbHost;

    public function __construct($id, $module, $config = [])
    {
        $this->dbUser = Yii::$app->params['dbUser'];
        $this->dbPassword = Yii::$app->params['dbPassword'];
        $this->dbHost = Yii::$app->params['dbHost'];

        parent::__construct($id, $module, $config);
    }
    // generar recibo pdf
    public function actionReceipt($idbranch, $id)
    {
        $ioSystemBranch = IoSystemBranch::findOne(['id' => $idbranch]);
        if (!$ioSystemBranch) {
            return $this->asJson([
                'message' => "El documento que estás buscando no existe!",
                'statusCode' => 404
            ]);
        }

        // Conectar a la base de datos
        $db = DbConnection::getConnection(
            $ioSystemBranch->dbidentifier,
            $this->dbUser,
            $this->dbPassword,
            $this->dbHost
        );

        // Asignar la conexión personalizada a Yii
        Yii::$app->set('iooxsBranch', $db);

        $receipt = Receipt::findOne($id);

        if (!$receipt) {
            return $this->asJson([
                'message' => "Receipt with ID $id not found.",
                'statusCode' => 404
            ]);
        }

        $dbRoot = DbConnection::getConnection(
            $ioSystemBranch->cfgIoSystem->dbidentifier,
            $this->dbUser,
            $this->dbPassword,
            $this->dbHost
        );

        Yii::$app->set('iooxsRoot', $dbRoot);

        // Generar PDF
        $pdfContent = Pdf::generateReceiptPdf($receipt, $ioSystemBranch);

        $date = date('Y-m-d_H-i-s'); 
        $filename = "recibo_{$date}.pdf"; 
        // Configurar la respuesta
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/pdf');
        Yii::$app->response->headers->add('Content-Disposition', "inline; filename=\"$filename\"");
        Yii::$app->response->content = $pdfContent;
        
        return Yii::$app->response;
    }
}
