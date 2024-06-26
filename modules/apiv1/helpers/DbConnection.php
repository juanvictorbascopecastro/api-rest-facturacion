<?php

namespace app\modules\apiv1\helpers;

use Yii;
use yii\db\Connection;

class DbConnection
{
    public static function getConnection($dbName, $username, $password)
    {
        $db = new Connection([
            'dsn' => 'pgsql:host=localhost;dbname=' . $dbName,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
        ]);
        
        $db->open();

        Yii::$app->set('customDb', $db); // Asigna la conexión personalizada a un componente de la aplicación
        return $db;
    }
}

