<?php
namespace app\controllers;


class SoapDp extends TMsoap
{
    public function __construct(){
        parent::__construct();

        $this->wsdlCargarFactura = 'https://dpfactura.com.co/wsFact_e/InterSoap?wsdl';
        $this->username = 'EmpAsociNaturgas';
        $this->password = 'PwAs0c1ac10natuG4s';
        $this->token = 'e2556e2f2dc65b60653fab4fc380996647363a01';
        $this->options = array(
            'login' => $this->username,
            'password' => $this->password,
            'token' => $this->token,
            'trace' => 1,
        );
    }

    /**
     *
     *  y devuelve el resultado de la operación.
     *
     * @param $facturaData
     * @return mixed
     */

    public function cargarFactura(/*array*/ $facturaData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->loggingData($facturaData);

        $invoice = [
            "pvcDocumentosXML" => $facturaData,
            "pvcCorreoUsuario" => $this->options['login'],
            "pvcClaveUsuario" => $this->options['password']
        ];


        try{
            $client = $this->createClient($this->wsdlCargarFactura);
            $output = $client->enviarFactura($invoice);
            $this->logging($client);
            var_dump($client);
            if($output->InsertarDocumentosResult == "EL XML de entrada contiene errores que no hacen posible interpretarlo"){
                return false;
            }
            return true;
        }catch (SoapFault $fault){
            error_log("SOAP Fault: (faultcode: {$fault->getCode()})");
            var_dump($fault->getMessage());
            return false;
        }catch (\Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }
}