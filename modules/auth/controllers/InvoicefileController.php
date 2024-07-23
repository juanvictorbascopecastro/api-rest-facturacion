<?php

namespace app\modules\auth\controllers;

use Yii;
use app\modules\auth\controllers\BaseController;
use app\modules\apiv1\models\Sale;
/**
 * Login controller for the `auth` module
 */
class InvoicefileController extends BaseController
{  
    public $enableCsrfValidation = false;

    public function actionIndex($id)
    {
        $sale = Sale::find()->where(['id' => $id])->with('productStocks')->one();

        if (!$sale) {
            return parent::sendResponse([
                'statusCode' => 404,
                'message' => "Sale with ID $id not found.",
            ]);
        }
        
        $filePath = Yii::getAlias('@webroot/files/invoice.pdf');
        return Yii::$app->response->sendFile($filePath, 'invoice.pdf', [
            'mimeType' => 'application/pdf',
            'inline' => true // Cambia a false para forzar la descarga en lugar de mostrar en el navegador
        ]);
    }
}
