<?php

namespace app\modules\apiv1\helpers;

use Yii;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use yii\web\UploadedFile;
use Cloudinary\Cloudinary;

class UploadFile {
    private static $folderProductImage = 'productos';

    public static function validateFile($uploadedFile) {
        $maxFileSize = ini_get('upload_max_filesize');
        $maxPostSize = ini_get('post_max_size');
        
        if ($uploadedFile->error != UPLOAD_ERR_OK) {
            $errorMessage = 'Error en la subida del archivo';
            switch ($uploadedFile->error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage = "El archivo subido excede el tamaño máximo permitido de {$maxFileSize}B (upload_max_filesize) o {$maxPostSize}B (post_max_size).";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = 'El archivo subido fue sólo parcialmente subido.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = 'No se subió ningún archivo.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMessage = 'Falta una carpeta temporal.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage = 'No se pudo escribir el archivo en el disco.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMessage = 'La subida del archivo fue detenida por una extensión.';
                    break;
            }
            return ['error' => true, 'message' => $errorMessage, 'code' => $uploadedFile->error];
        }

        return ['error' => false];
    }

    public static function uploadToCloudinary($uploadedFile) {
        try {
            $cloudinaryConfig = Yii::$app->params['cloudinary'];
            Configuration::instance($cloudinaryConfig);
            $uploadApi = new UploadApi();
            $response = $uploadApi->upload($uploadedFile->tempName, [
                'folder' => self::$folderProductImage, // Accede a la propiedad estática correctamente
                'use_filename' => true,
                'unique_filename' => false,
                'overwrite' => true
            ]);
    
            return ['url' => $response['secure_url'], 'public_id' => $response['public_id'], 'error' => false];
        } catch (Exception $e) {
            return ['error' => true, 'message' => 'No se pudo subir la imagen a Cloudinary', 'details' => $e->getMessage()];
        }
    }
    

    public static function deleteFromCloudinary($imageUrl) {
        try {
            $cloudinaryConfig = Yii::$app->params['cloudinary'];
            Configuration::instance($cloudinaryConfig);
            $uploadApi = new UploadApi();

            $publicId = pathinfo($imageUrl, PATHINFO_FILENAME);
            $result = $uploadApi->destroy(self::$folderProductImage . '/' . $publicId, $options = []);
            if ($result['result'] === 'ok') {
                return ['error' => false, 'message' => 'Imagen eliminada correctamente de Cloudinary'];
            } else {
                return ['error' => true, 'message' => 'No se pudo eliminar la imagen de Cloudinary'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'message' => 'Error al intentar eliminar la imagen de Cloudinary', 'details' => $e->getMessage()];
        }
    }

    // public static function deleteFromCloudinaryArray($arrayPath) {
    //     try {
    //         $cloudinaryConfig = Yii::$app->params['cloudinary'];
    //         Configuration::instance($cloudinaryConfig);
    //         $uploadApi = new UploadApi();
    
    //         $publicIds = array_map(function($imageUrl) {
    //             $publicId = pathinfo($imageUrl, PATHINFO_FILENAME);
    //             return self::$folderProductImage . '/' . $publicId;
    //         }, $arrayPath);
    //         $result = $uploadApi->deleteAssets($publicIds, $options = []);
    //         return $result;
    
    //         $results = [];
    //         foreach ($result as $publicId => $deleteResult) {
    //             if ($deleteResult['result'] === 'ok') {
    //                 $results[] = ['error' => false, 'message' => "Imagen con public_id {$publicId} eliminada correctamente de Cloudinary"];
    //             } else {
    //                 $results[] = ['error' => true, 'message' => "No se pudo eliminar la imagen con public_id {$publicId} de Cloudinary"];
    //             }
    //         }
    
    //         return $results;
    //     } catch (Exception $e) {
    //         return ['error' => true, 'message' => 'Error al intentar eliminar la imagen de Cloudinary', 'details' => $e->getMessage()];
    //     }
    // }
}
