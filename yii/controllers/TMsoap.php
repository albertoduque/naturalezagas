<?php
/**
 * Created by PhpStorm.
 * User: Daniel Grijalba
 * Date: 24/04/18
 * Time: 09:30 AM
 */

  namespace app\controllers;
  
   use SoapClient;
use SOAPHeader;

class TMsoap{

    /**
     *
     * Usuario de conección al servicio soap
     *
     * @var string
     */

    protected $username = '';

    /**
     *
     * Password del usuario
     *
     * @var string
     */

    protected $password = '';
    
    protected $token= '';

    /**
     *
     * Endpoint para pruebas de facturacion
     *
     * @var string
     */

    protected $endpointFacturacionTest = '';

    /**
     *
     * Endpoint para crear clientes
     *
     * @var string
     */

    protected $endpointCrearCliente = '';

    /**
     *
     * Endpoint para obtener documentos
     *
     * @var string
     */

    protected $endpointObtenerDocumento = '';

    /**
     *
     * Endpoint creación de facturas.
     *
     * @var string
     */

    protected $endpointFacturacion = '';

    /**
     * Endpoint GTI
     *
     * @var string
     */
    protected $wsdlCargarFactura = '';

    /**
     *
     * Arreglo con opciones del servicio soap
     *
     * @var array
     */

    protected $options = array();

    /**
     * Endpoint de consulta de estado de documentos digiflow
     * @var string
     */
    protected $digiflowConsultaEstadoDocumento = '';

    public function __construct(){
      
        $this->username = '';
        $this->token = '';
        $this->password = '';
        $this->options = array(
            'token' => $this->token,
            'login' => $this->username,
            'password' => $this->password,
            'trace' => 1,
            'connection_timeout' => 80
        );
    }

    /**
     *
     * Utiliza el servicio de prueba de facturación y devuelve el resultado de la operación.
     *
     * @param $facturaData
     * @return mixed
     */

    public function sendFacturaTest(/*array*/ $facturaData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $client = $this->createClient($this->endpointFacturacionTest);

        $output = $client->putCustomerETDLoad($facturaData);
        $this->loggingData($facturaData);
        $this->logging($client);

        return $output;
    }

    /**
     * Consulta el estdo de un documento den digiflow (Peru - HyC)
     * @param $facturaData
     * @return mixed
     */
    public function checkDocumentStatus($facturaData){

        ini_set("soap.wsdl_cache_enabled", "0");

        $client = $this->createClient($this->digiflowConsultaEstadoDocumento);

        $output = $client->ConsultaEstado($facturaData);
        $this->loggingData($facturaData);
        $this->logging($client);

        return $output;

    }

    /**
     * Obtiene el pdf de un documento codificado en base64 (Digiflow Perú - HyC)
     * @param $facturaData
     * @return mixed
     */
    public function getDocumentPdf($facturaData){

        ini_set("soap.wsdl_cache_enabled", "0");

        $client = $this->createClient($this->digiflowObtenerDocumentoPdf);

        $output = $client->getDocumentoPDF($facturaData);
        $this->loggingData($facturaData);
        $this->logging($client);

        return $output;

    }

    /**
     *
     * Utiliza el servicio de facturación y devuelve el resultado de la operación.
     *
     * @param $facturaData
     * @return mixed
     */

    public function sendFactura(/*array*/ $facturaData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->loggingData($facturaData);

        try{
            $client = $this->createClient($this->endpointFacturacion);
            $output = $client->recepcionFact($facturaData);
            $this->logging($client);
            if($output == "\n"){
                return "PendingSync";
            }
            return $output;
        }catch (SoapFault $fault){
            error_log("SOAP Fault: (faultcode: {$fault->getCode()})");
            return "PendingSync";
        }catch (\Exception $e) {
            error_log($e->getMessage());
            return "PendingSync";
        }
    }

    /**
     * @param $facturaData
     */
    public function generateXml(/*array*/ $facturaData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->loggingData($facturaData);

        $client = $this->createClient($this->endpointFacturacion);
        $client->recepcionFact($facturaData);
        $request = $client->__getLastRequest();
        print_r($request);
    }

    /**
     *
     * Devuelve un error al momento de intentar sincronizar una factura.
     *
     * @param $facturaData
     * @return string
     */
    public function sendError(/*array*/ $facturaData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->loggingData($facturaData);

        return "This is an error";
    }

    /**
     *
     * Devuelve el documento dando la información del mismo.
     *
     * @param $documentData
     * @return mixed
     */

    public function getDocument(/*array*/ $documentData){
        ini_set("soap.wsdl_cache_enabled", "0");

        try{
            $client = $this->createClient($this->endpointObtenerDocumento);

            $output = $client->ObtenerPDF($documentData);

            $this->loggingData($documentData);
            $this->logging($client);

            return $output;
        }catch (SoapFault $fault){
            error_log("SOAP Fault: (faultcode: {$fault->getCode()})");
            return "errorDownload";
        }catch (\Exception $e) {
            error_log($e->getMessage());
            return "errorDownload";
        }
    }

    /**
     *
     * Utiliza el servicio de creación de clientes y devuelve el resultado de la operación.
     *
     * @param $clienteData
     * @return mixed
     */

    public function sendCliente(/*array*/ $clienteData){
        ini_set("soap.wsdl_cache_enabled", "0");

        $client = $this->createClient($this->endpointCrearCliente);

        $output = $client->Crear($clienteData);

        $this->loggingData($clienteData);
        $this->logging($client);

        return $output;
    }

    /**
     *
     * Crea un nuevo cliente de soap.
     *
     * @param $wsdlName
     * @return SoapClient
     */

    protected function createClient($wsdlName){
        $client = new SoapClient($wsdlName, $this->options);
        return $client;
    }

    /**
     *
     * Registra los request al servicio de facturación
     *
     * @param SoapClient $client
     */

    protected function logging(SoapClient $client){
        $request = $client->__getLastRequest();
        $response = $client->__getLastResponse();
        error_log(print_r($request,true));
        error_log(print_r($response, true));
    }

    /**
     *
     * Guarda la información enviada al servicio en json y la guarda en el log.
     *
     * @param $clienteData
     */

    protected function loggingData(/*array*/ $clienteData){
        error_log(json_encode($clienteData));
    }

    /**
     *
     * Devuelve el valor dado en letras.
     *
     * @param $amount
     * @param $currency
     * @param bool|false $fem
     * @param bool|true $dec
     * @return string
     */

    public function transformNumberToLetter($amount, $currency, $fem = false, $dec = true){
        $num = (int) $amount;

        $currency_long = '';

        switch($currency){
            case '1':
                $currency_long = strtoupper(' Y nn/100 DOLARES AMERICANOS');
                break;
            case '2':
                $currency_long = strtoupper(' Y nn/100 SOLES');
                break;
            case '3':
                $currency_long = strtoupper(' Y nn/100 EUROS');
                break;
            case '4':
                $currency_long = strtoupper(' Y nn/100 LIBRAS');
                break;
        }

        $matuni[2]  = "dos";
        $matuni[3]  = "tres";
        $matuni[4]  = "cuatro";
        $matuni[5]  = "cinco";
        $matuni[6]  = "seis";
        $matuni[7]  = "siete";
        $matuni[8]  = "ocho";
        $matuni[9]  = "nueve";
        $matuni[10] = "diez";
        $matuni[11] = "once";
        $matuni[12] = "doce";
        $matuni[13] = "trece";
        $matuni[14] = "catorce";
        $matuni[15] = "quince";
        $matuni[16] = "dieciseis";
        $matuni[17] = "diecisiete";
        $matuni[18] = "dieciocho";
        $matuni[19] = "diecinueve";
        $matuni[20] = "veinte";
        $matunisub[2] = "dos";
        $matunisub[3] = "tres";
        $matunisub[4] = "cuatro";
        $matunisub[5] = "quin";
        $matunisub[6] = "seis";
        $matunisub[7] = "sete";
        $matunisub[8] = "ocho";
        $matunisub[9] = "nove";

        $matdec[2] = "veint";
        $matdec[3] = "treinta";
        $matdec[4] = "cuarenta";
        $matdec[5] = "cincuenta";
        $matdec[6] = "sesenta";
        $matdec[7] = "setenta";
        $matdec[8] = "ochenta";
        $matdec[9] = "noventa";
        $matsub[3]  = 'mill';
        $matsub[5]  = 'bill';
        $matsub[7]  = 'mill';
        $matsub[9]  = 'trill';
        $matsub[11] = 'mill';
        $matsub[13] = 'bill';
        $matsub[15] = 'mill';
        $matmil[4]  = 'millones';
        $matmil[6]  = 'billones';
        $matmil[7]  = 'de billones';
        $matmil[8]  = 'millones de billones';
        $matmil[10] = 'trillones';
        $matmil[11] = 'de trillones';
        $matmil[12] = 'millones de trillones';
        $matmil[13] = 'de trillones';
        $matmil[14] = 'billones de trillones';
        $matmil[15] = 'de billones de trillones';
        $matmil[16] = 'millones de billones de trillones';

        $num = trim((string)@$num);
        if ($num[0] == '-') {
            $neg = 'menos ';
            $num = substr($num, 1);
        }else
            $neg = '';
        while ($num[0] == '0') $num = substr($num, 1);
        if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
        $zeros = true;
        $punt = false;
        $ent = '';
        $fra = '';
        for ($c = 0; $c < strlen($num); $c++) {
            $n = $num[$c];
            if (! (strpos(".,'''", $n) === false)) {
                if ($punt) break;
                else{
                    $punt = true;
                    continue;
                }

            }elseif (! (strpos('0123456789', $n) === false)) {
                if ($punt) {
                    if ($n != '0') $zeros = false;
                    $fra .= $n;
                }else

                    $ent .= $n;
            }else

                break;

        }
        $ent = '     ' . $ent;
        if ($dec and $fra and ! $zeros) {
            $fin = ' coma';
            for ($n = 0; $n < strlen($fra); $n++) {
                if (($s = $fra[$n]) == '0')
                    $fin .= ' cero';
                elseif ($s == '1')
                    $fin .= $fem ? ' una' : ' un';
                else
                    $fin .= ' ' . $matuni[$s];
            }
        }else
            $fin = '';
        if ((int)$ent === 0) return 'Cero ' . $fin;
        $tex = '';
        $sub = 0;
        $mils = 0;
        $neutro = false;
        while ( ($num = substr($ent, -3)) != '   ') {
            $ent = substr($ent, 0, -3);
            if (++$sub < 3 and $fem) {
                $matuni[1] = 'una';
                $subcent = 'os';
            }else{
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
            }
            $t = '';
            $n2 = substr($num, 1);
            if ($n2 == '00') {
            }elseif ($n2 < 21)
                $t = ' ' . $matuni[(int)$n2];
            elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0) $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }else{
                $n3 = $num[2];
                if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }
            $n = $num[0];
            if ($n == 1) {
                $t = ' ciento' . $t;
            }elseif ($n == 5){
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
            }elseif ($n != 0){
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
            }
            if ($sub == 1) {
            }elseif (! isset($matsub[$sub])) {
                if ($num == 1) {
                    $t = ' mil';
                }elseif ($num > 1){
                    $t .= ' mil';
                }
            }elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . 'ón';
            }elseif ($num > 1){
                $t .= ' ' . $matsub[$sub] . 'ones';
            }
            if ($num == '000') $mils ++;
            elseif ($mils != 0) {
                if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                $mils = 0;
            }
            $neutro = true;
            $tex = $t . $tex;
        }
        $tex = $neg . substr($tex, 1) . $fin;

        $dec = explode(".", round($amount,2) );

        if(!isset($dec[1])){
            $dec[1] = "00";
        }else{
            $decimalValue = $dec[1]*1;
            if(strlen($dec[1]) == 1 && $decimalValue < 10){
                $dec[1] = $dec[1].'0';
            }
        }

        $currency_long = str_replace("NN", $dec[1], $currency_long);

        return utf8_decode(strtoupper(ucfirst($tex))).$currency_long;
    }

    /**
     *
     * Devuelve la diferencia en días entre las dos fechas dadas.
     *
     * @param $startDate
     * @param $endDate
     * @return mixed
     */

    public function dateDifference($startDate, $endDate){
        $start_datetime = new DateTime($startDate);
        $end_datetime = new DateTime($endDate);

        $total_diff =  $end_datetime->diff($start_datetime);
        return $total_diff->days;
    }
}