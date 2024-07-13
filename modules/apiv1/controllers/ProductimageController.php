<?php
namespace app\modules\apiv1\controllers;

use Yii;
use app\modules\apiv1\controllers\BaseController; 

use app\modules\apiv1\models\ProductImage;
use app\modules\apiv1\models\Product;

use yii\web\UploadedFile;
use app\modules\apiv1\helpers\UploadFile;

class ProductimageController extends BaseController
{
    public $modelClass = 'app\modules\apiv1\models\ProductImage';

    public function actions()
    {
        $actions = parent::actions();
        unset(
            $actions['index'],
            $actions['view'],
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['options']
        );
        
        return $actions;
    }

    public function actionInsert($idproduct)
    {
        // Obtener el producto asociado
        $product = Product::findOne($idproduct);
        if (!$product) {
            return $this->sendResponse(['statusCode' => 404, 'message' => 'Product not found']);
        }

        // Obtener el archivo subido (asumiendo que el campo en el formulario se llama 'image')
        $uploadedFile = UploadedFile::getInstanceByName('image');
        if (!$uploadedFile) {
            return $this->sendResponse(['statusCode' => 400, 'message' => 'No file uploaded']);
        }

        // Validar el archivo subido
        $validationResult = UploadFile::validateFile($uploadedFile);
        if ($validationResult['error']) {
            return $this->sendResponse(['statusCode' => 400, 'message' => $validationResult['message']]);
        }

        // Subir la imagen a Cloudinary
        $uploadResult = UploadFile::uploadToCloudinary($uploadedFile);
        if ($uploadResult['error']) {
            return $this->sendResponse(['statusCode' => 500, 'message' => $uploadResult['message']]);
        }

        // Crear un nuevo registro de ProductImage
        $productImage = new Productimage();
        $productImage->idproduct = $product->id;
        $productImage->imagepath = $uploadResult['url']; // URL de la imagen en Cloudinary
        $productImage->recyclebin = false; // Ajusta según tus requerimientos

        // Guardar el registro de ProductImage en la base de datos
        if (!$productImage->save()) {
            $deleteResult = UploadFile::deleteFromCloudinary($uploadResult['url']);
            return $this->sendResponse(['statusCode' => 500, 'message' => 'Failed to save product image', 'errors' => $productImage->errors]);
        }

        // Si todo ha ido bien, enviar respuesta de éxito
        return $this->sendResponse([
            'statusCode' => 201,
            'message' => 'Product image created successfully',
            'data' => Productimage::findOne($productImage->id)
        ]);
    }

    public function actionRemove($id)
    {
        // Obtener el registro de ProductImage
        $productImage = Productimage::findOne($id);
        if (!$productImage) {
            return $this->sendResponse(['statusCode' => 404, 'message' => 'Product image not found']);
        }
        // Eliminar la imagen de Cloudinary
        $deleteResult = UploadFile::deleteFromCloudinary($productImage->imagepath);
        return $deleteResult;
        if ($deleteResult['error']) {
            return $this->sendResponse(['statusCode' => 500, 'message' => 'Failed to delete product image from Cloudinary']);
        }

        // Eliminar el registro de ProductImage de la base de datos
        if (!$productImage->delete()) {
            return $this->sendResponse(['statusCode' => 500, 'message' => 'Failed to delete product image record']);
        }

        // Si todo ha ido bien, enviar respuesta de éxito
        return $this->sendResponse([
            'statusCode' => 200,
            'message' => 'Product image deleted successfully'
        ]);
    }
}
