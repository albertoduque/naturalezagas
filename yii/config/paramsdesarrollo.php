<?php


$connection = new \yii\db\Connection([
'dsn' => 'mysql:host=localhost;dbname=eventos_natur',
    'username' => 'root',
    'password' => '123',
    'charset' => 'utf8',
]);
$connection->open();
$options = $connection->createCommand( "SELECT value,nombre FROM parametros")->queryAll();
$connection->close();
var_dump($options);
$parameter = array();
foreach ($options as $option)
{
    if($option['nombre']=="EmailEstadisticas")
    {
        $parameter['EmailEstadisticas'] = $option['value'];
    }
    if($option['nombre']=="consecutivoActivo")
    {
        $parameter['consecutivoActivo'] = $option['value'];
    }
}

return [
    'adminEmail' => 'desarrollo.tinger@gmail.com',
    'supportEmail' => 'desarrollo.tinger@gmail.com',
    'fileUploadUrl' => 'doc/',
    'fileUploadCert' => 'certificacion/',
    'EmailEstadisticas' => $parameter['EmailEstadisticas'] ? $parameter['EmailEstadisticas'] : 'desarrollo.tinger@gmail.com',
  //  'consecutivoActivo' => $parameter['consecutivoActivo'] ? $parameter['consecutivoActivo'] : 1,
    //'sessionTimeoutSeconds'=>300,
];