<?php

namespace app\modules\apiv1\helpers;

use Yii;
use yii\db\Connection;
use yii\db\Exception;

class DbConnection
{
    public static function getConnection($dbName, $username, $password, $dbHost)
    {
        try {
            $db = new Connection([
                'dsn' => 'pgsql:host='. $dbHost .';dbname=' . $dbName,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8',
            ]);
            
            $db->open();
    
            Yii::$app->set('customDb', $db); // Asigna la conexión personalizada a un componente de la aplicación
            return $db;

        } catch (Exception $e) {
            Yii::error('Error al conectar a la base de datos: ' . $e->getMessage());
            throw new \yii\web\ServerErrorHttpException('Error al conectar a la base de datos');
        }
    }
}
