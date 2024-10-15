<?php
use yii\web\Request;
$request = new Request();

$dbHost = 'localhost';
$dbPassword = '12345678';
$dbUser = 'postgrest';

$Authorization = $request->getHeaders()->get('Authorization');
$keyJWT = "IO-SOFTWARE-LLAVE-SECRETA";

$dbAccess='0x_iooxs_access';
$dbIo = '0x_iooxs_io';
$DBiooxsRoot = null;
$DBiooxsBranch = null;
// echo '..' . $Authorization;
if(!empty($Authorization)) {
    
    $token = str_replace('Bearer ', '', $Authorization);

    function base64UrlDecode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    function decodeJWT($jwt, $secretKey) {
        // Dividir el JWT en sus partes
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
    
        // Decodificar el header y el payload
        $header = json_decode(base64UrlDecode($headerEncoded), true);
        $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    
        // Crear la firma nuevamente
        $signature = base64UrlDecode($signatureEncoded);
        $validSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secretKey, true);
    
        // Verificar la firma
        if (hash_equals($validSignature, $signature)) {
            return $payload;
        } else {
            throw new Exception('Firma JWT no válida');
        }
    }
    // Tu clave secreta (llave)
    //$clave_secreta = "IO-SOFTWARE-KEY-UNIQUE-JWT-IDENTIFIER";
    
    try{
        // Decodificar el JWT
        $datos_decodificados = decodeJWT($token, $keyJWT);
        $iduser = $datos_decodificados['uid'];
    } catch (Exception $e) {
        // Manejo de errores
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    
    }
    //echo $iduser;
    $conn = pg_connect("host=$dbHost port=5432 dbname=$dbAccess user=$dbUser password=$dbPassword");
    
    $sql = 'select system.dbidentifier as "BDiooxsRoot", branch.dbidentifier as "DBiooxsBranch"
          from cfg."ioSystemBranchUser" iouser inner join  cfg."ioSystemBranch" branch on iouser."idioSystemBranch"=branch.id
               inner join  cfg."ioSystem" system on branch."idioSystem"=system.id
          where "iduserActive" ='.$iduser;
    
    $result = pg_query($conn, $sql);
  
    while ($row = pg_fetch_assoc($result)) {
        $DBiooxsRoot = $row['BDiooxsRoot'];
        $DBiooxsBranch = $row['DBiooxsBranch'];
        break;
    }
}
//echo $DBiooxsRoot.','.$DBiooxsBranch;

$connections = [
    'iooxs_access' => [
        'class' => 'yii\db\Connection',
        'dsn' => "pgsql:host=$dbHost;dbname=" . $dbAccess,
        'username' => $dbUser,
        'password' => $dbPassword,
        'charset' => 'utf8',
    ],
    'iooxs_io' => [
        'class' => 'yii\db\Connection',
        'dsn' => "pgsql:host=$dbHost;dbname=". $dbIo,
        'username' => $dbUser,
        'password' => $dbPassword,
        'charset' => 'utf8',
    ],
];

if ($DBiooxsRoot !== null) {
    $connections['iooxsRoot'] = [
        'class' => 'yii\db\Connection',
        'dsn' => "pgsql:host=$dbHost;dbname=" . $DBiooxsRoot,
        'username' => $dbUser,
        'password' => $dbPassword,
        'charset' => 'utf8',
    ];
} else {
    $connections['iooxsRoot'] = null;
}

if ($DBiooxsBranch !== null) {
    $connections['iooxsBranch'] = [
        'class' => 'yii\db\Connection',
        'dsn' => "pgsql:host=$dbHost;dbname=" . $DBiooxsBranch,
        'username' => $dbUser,
        'password' => $dbPassword,
        'charset' => 'utf8',
    ];
} else {
    $connections['iooxsBranch'] = null;
}

return $connections;
