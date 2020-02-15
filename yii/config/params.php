<?php


$connection = new \yii\db\Connection([
'dsn' => 'mysql:host=localhost;dbname=dev002na_eventosdev',
    'username' => 'dev002naturgasco',
    'password' => 'my0Vf~tQ2G05',
    'charset' => 'utf8',
]);
$connection->open();
$options = $connection->createCommand( "SELECT value,nombre FROM parametros")->queryAll();
$connection->close();
$parameter = array();
foreach ($options as $option)
{
    if($option['nombre']=="EmailEstadisticas")
    {
        $parameter['EmailEstadisticas'] = $option['value'];
    }
    if($option['nombre']=="consecutivoFactura")
    {
        $parameter['consecutivoFactura'] = $option['value'];
    }
    if($option['nombre']=="consecutivoNC")
    {
        $parameter['consecutivoNC'] = $option['value'];
    }
    if($option['nombre']=="consecutivoND")
    {
        $parameter['consecutivoND'] = $option['value'];
    }
    if($option['nombre']=="consecutivoFacturaContingencia")
    {
        $parameter['consecutivoFacturaContingencia'] = $option['value'];
    }
}

return [
    'adminEmail' => 'desarrollo.tinger@gmail.com',
    'supportEmail' => 'desarrollo.tinger@gmail.com',
    'fileUploadUrl' => 'doc/',
    'fileUploadCert' => 'certificacion/',
    'EmailEstadisticas' => $parameter['EmailEstadisticas'] ? $parameter['EmailEstadisticas'] : 'desarrollo.tinger@gmail.com',
     'consecutivoFactura' => $parameter['consecutivoFactura'],
    'consecutivoFacturaContingencia' => $parameter['consecutivoFacturaContingencia'],
    'consecutivoNC' => $parameter['consecutivoNC'] ? $parameter['consecutivoNC'] : 1,
    'consecutivoND' => $parameter['consecutivoND'] ? $parameter['consecutivoND'] : 1,
    'sessionTimeoutSeconds'=>1800,
];