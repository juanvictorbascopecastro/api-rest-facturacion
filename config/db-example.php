<?php

$headers = getallheaders();
$keyJWT='CLAVE-SECRETA';
$Authorization=$headers['Authorization'];
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
    $iduser=$datos_decodificados['uid'];
} catch (Exception $e) {
    // Manejo de errores
    echo 'Excepción capturada: ',  $e->getMessage(), "\n";

}
//echo $iduser;

$dbAccess='00_iooxs_access';

$conn = pg_connect("host=127.0.0.1 port=5432 dbname=$dbAccess user=postgres password=12345678");

$sql='select system.dbidentifier as "BDiooxsRoot", branch.dbidentifier as "DBiooxsBranch"
      from cfg."ioSystemBranchUser" iouser inner join  cfg."ioSystemBranch" branch on iouser."idioSystemBranch"=branch.id
           inner join  cfg."ioSystem" system on branch."idioSystem"=system.id
      where "iduserActive"='.$iduser;

$result = pg_query($conn, $sql);
$DBiooxsRoot='';
$DBiooxsBranch='';

while ($row = pg_fetch_assoc($result)) {
        $DBiooxsRoot = $row['BDiooxsRoot'];

        $DBiooxsBranch = $row['DBiooxsBranch'];
        break;
    }
//echo $DBiooxsRoot.','.$DBiooxsBranch;



return [
    'iooxs_access' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname='.$dbAccess,
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),
    'iooxs_io' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname=00_iooxs_io',
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),
   'iooxsRoot' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname='.$DBiooxsRoot,
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),

   'iooxsBranch' => array(
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=localhost;dbname='.$DBiooxsBranch,
        'username' => 'postgres',
        'password' => '12345678',
        'charset' => 'utf8',
    ),
];

