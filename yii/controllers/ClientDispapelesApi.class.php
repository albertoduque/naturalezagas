<?php
namespace app\controllers;
use SoapClient;
class ClientDispapelesApi{
    private $url;
    private $token;
    private $usuario;
    private $password;
    private $header;
    private $opciones;
    private $ctx;
    private $client;

    public function __construct(){
       //Producción
	   /*
       // $this->url = "https://dpfactura.com.co/wsFact_e/InterSoap?wsdl";
        $this->url = "https://colaboracionelectronica.com/wsFact_e/InterSoap?wsdl";
        $this->token = "5ce21110c1e2d29f448f9ee0a10f26fed0238d25";
        $this->usuario = "UsNaturgas";
        $this->password = "PwNaturg4s";
        $this->header = "Token: {$this->token}\r Username: {$this->usuario}\r Password: {$this->password}\r ";
        $this->opciones = array('http' => array( 'header' => $this->header));
        $this->ctx = stream_context_create($this->opciones);
        $this->client = new SoapClient($this->url, array('trace'=>1, 'uri'=>'http://soap.wsdispapeles.dispapeles.com.co/','exceptions' => true,'stream_context' => $this->ctx));
		//Fin Producción
		*/
		
		//Desarrollo
		//$this->url = "https://enviardocumentos.dispafel.com/DFFacturaElectronicaEnviarDocumentos/enviarDocumento?wsdl";
		$this->url = "https://wsenviardocumentos.dispafel.com/DFFacturaElectronicaEnviarDocumentos/enviarDocumento?wsdl";
        $this->client = new SoapClient($this->url);
		
		//Fin Desarrollo
        
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarEstadoFactura
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarEstadoFactura($variables){
      $response = $this->client->__soapCall("consultarEstadoFactura", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarEstadosFactura
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarEstadosFactura($variables){
      $response = $this->client->__soapCall("consultarEstadosFactura", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarEstadosFacturaByErp
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarEstadosFacturaByErp($variables){
      $response = $this->client->__soapCall("consultarEstadosFacturaByErp", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarEstadosFacturaByLote
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarEstadosFacturaByLote($variables){
      $response = $this->client->__soapCall("consultarEstadosFacturaByLote", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarPdfFactura
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarPdfFactura($variables){
      $response = $this->client->__soapCall("consultarPdfFactura", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo consultarXmlFactura
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function consultarXmlFactura($variables){
      $response = $this->client->__soapCall("consultarXmlFactura", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo enviarFactura
     * @param Array arg0
     * @return  response
     * **/
    
    function enviarFactura($variables){
		//Producción
		//$response = $this->client->__soapCall("enviarFactura", array($variables));
		//Fin Producción
		
		//Desarrollo
		$response = $this->client->__soapCall("enviarDocumento", array($variables));
		//print "<pre>";
		/*var_dump($variables);
		//print "</pre>";
		print "<hr><pre>";
		var_dump($response);
		print "</pre>";
		var_dump($response->return->consecutivo);print "<br>";
		var_dump($response->return->descripcionProceso);print "<br>";
		var_dump($response->return->estadoProceso);print "<br>";
		var_dump($response->return->idErp);print "<br>";
		var_dump($response->return->prefijo);print "<br>";
		var_dump($response->return->tipoDocumento);print "<br>";*/
		//"SETT"
		//Fin Desarrollo
		
		return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo enviarFacturaIndependiente
     * @param Array arg0
     * @return  response
     * **/
    
    function enviarFacturaIndependiente($variables){
        
      $client = new SoapClient($this->url, array('trace'=>1, 'stream_context' => $this->ctx));
      $response = $client->__soapCall("enviarFacturaIndependiente", array($variables));
      
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo enviarFacturaLote
     * @param Array arg0
     * @return  response
     * **/
    
    function enviarFacturaLote($variables){
      $response = $client->__soapCall("enviarFacturaLote", array($variables));
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo enviarFacturaPorLoteDto
     * @param Array arg0
     * @return  response
     * **/
    
    function enviarFacturaPorLoteDto($variables){
      $response = $this->client->__soapCall("enviarFacturaPorLoteDto", array($variables));
      return $response;
    }
    
    /**
     * Created By: Pablo Lievano
     * Fecha: 2018-09-23
     * Contenido: Consume el metodo findRegistroDocumentosEmpresa
     * @param Array objConsultaFactura
     * @param   String consecutivo
     * @param   Date fechafacturacion
     * @param   String fechafacturacionString
     * @param   String idErp
     * @param   String idFkEmpresa default ''
     * @param   String idLote default ''
     * @param   String prefijo
     * @param   String tipodocumento
     * @param   String tokenempresa
     * @return  response
     * **/
    
    function findRegistroDocumentosEmpresa($variables){
      $response = $this->client->__soapCall("findRegistroDocumentosEmpresa", array($variables));
      return $response;
    }

}
?>