<?php

 namespace app\controllers;
use DOMDocument;
use Datetime;
use Yii;
class FacturaWsdl
{

    public function __construct()
    {

    }

    
    public function createFacturas()
    {
        header('Content-Type: text/xml');
        $xmlDoc = new DOMDocument('1.0');
        
        $raiz = $xmlDoc->appendChild($xmlDoc->createElement('root'));
        $root = $raiz->appendChild($xmlDoc->createElement('FacturaElectronicaXML'));
        $encabezadoTag = $root->appendChild($xmlDoc->createElement('eBfelEncabezadofactura'));
        
        $r = $encabezadoTag->appendChild($xmlDoc->createElement('token', "e2556e2f2dc65b60653fab4fc380996647363a01"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement('tipodocumento', "1"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("consecutivo", 980000001));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("Fechafacturacion", "2018-10-18 10:10:10"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("moneda", "COP"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("totalimportebruto",  number_format(1000000.00,2,'.','')));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("totalbaseimponible", number_format(190000.00,2,'.','')));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("Totalfactura", number_format(1900000.00,2,'.','')));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("Tiponota", "2"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("Tipopersona", "1"));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("Tipoidentificacion", 13));
        $r = $encabezadoTag->appendChild($xmlDoc->createElement("numeroidentificacion", "1033118093"));
        $encabezadoTag->appendChild($xmlDoc->createElement("Aplicafel", "Si"));
        $encabezadoTag->appendChild($xmlDoc->createElement("pais", "CO"));
        $encabezadoTag->appendChild($xmlDoc->createElement("Ciudad", "Bogot�� D.C"));
        $encabezadoTag->appendChild($xmlDoc->createElement("Departamento", "Bogot�� D.C"));
        $encabezadoTag->appendChild($xmlDoc->createElement("direccion", "Bogot�� D.C"));
        $encabezadoTag->appendChild($xmlDoc->createElement("telefono", "105563322"));
        $encabezadoTag->appendChild($xmlDoc->createElement("Tipocompra", 1));
        
        $detalleFact = $encabezadoTag->appendChild($xmlDoc->createElement("listaDetalle"));
        $detalleFact->appendChild($xmlDoc->createElement("codigoproducto", "RES01"));
        $detalleFact->appendChild($xmlDoc->createElement("descripcion", "nutella"));
        $detalleFact->appendChild($xmlDoc->createElement("cantidad", number_format(19,0,'.','')));
        $detalleFact->appendChild($xmlDoc->createElement("valorunitario", number_format(18000,0,'.','')));
        $detalleFact->appendChild($xmlDoc->createElement("preciosinimpuestos", number_format(1700,0,'.','')));
        $detalleFact->appendChild($xmlDoc->createElement("preciototal", number_format(20000,0,'.','')));
        
        $impuestosFact = $encabezadoTag->appendChild($xmlDoc->createElement("listaImpuestos"));
        $impuestosFact->appendChild($xmlDoc->createElement("codigoImpuestoRetencion", "01"));
        $impuestosFact->appendChild($xmlDoc->createElement("valorImpuestoRetencion", number_format(20000,0,'.','')));
        $impuestosFact->appendChild($xmlDoc->createElement("baseimponible", number_format(20000,0,'.','')));
        
       echo $xmlDoc->saveXML() . "\n";die;
        
        return (String)  $xmlDoc->saveXML();

    }
    
    public function createFactura2($token)
    {
        //var_dump(date(DATE_ATOM, strtotime('2010-12-30 23:21:46')));die;
        $variableFactura = array(
  //"arg0"=> array(
  "felCabezaDocumento"=> array(
    "accionAcuse"=> "0",
    "aceptacion"=> "0",
    "activo"=> "0",
    "acuse"=> "0",
    "acuseVista"=> false,
    "aplicafel"=> "Si",
    "bariolocalidad"=> "Santa Helena de Baviera",
    "ciudad"=> "Bogotá D.C",
    "clasificacionCliente"=> "",
    "codigoPlantillaPdf"=> "",
    "codigovendedor"=> "",
    "comentarioAcuse"=> "",
    "condicionPagoReferencia"=> "",
    "consecutivo"=> 980000056,
    "consecutivofacturamodificada"=> "",
    "cufe"=> "",
    "cufefacturamodificada"=> "",
    "departamento"=> "Bogotá D.C",
    "descripcion"=> "",
    "descripestadodianconsolidados"=> "",
    "descuento"=> "",
    "despachadoACiudad"=> "",
    "despachadoADireccion"=> "",
    "despachadoANombre"=> "",
    "despachadoAObservacion"=> "",
    "despachadoAObservacionLugar"=> "",
    "despachadoATelefono"=> "",
    "despachadoAZonaDespacho"=> "",
    "direccion"=> "Calle 100 #95-90 Edif Zimma 307",
    "documentoEmitidoEn"=> "",
    "email"=> "duque.alberto@gmail.com",
    "enviadaAAdquirientes"=> "",
    "envioPorEmailPlataforma"=> "",
    "estado"=> "",
    "estadoactual"=> "",
    "estadodian"=> "",
    "estadodianconsolidados"=> "",
    "fechaAlta"=> "",
    "fechaBaja"=> "",
    "fechaFirma"=> "",
    "fechaMod"=> "",
    "fechaemisionordencompra"=> "",
    "fechafacturacion"=> "".date(DATE_ATOM, strtotime('2010-12-30 23:21:46')),
    "fechafacturacionFinal"=> "".date(DATE_ATOM, strtotime('+30 day' ,strtotime($params['fechafacturacion']))),
    "fechafacturacionInicial"=> "".date(DATE_ATOM, strtotime('2010-12-30 23:21:46')),
    "fechafacturamodificada"=> "",
    "fecharespuesta"=> "",
    "fechavencimiento"=> "",
    "filterTotal"=> "",
    "horaEntregaProgramada"=> "",
    "id"=> "",
    "idErp"=> "",
    "idFkEmpresa"=> "",
    "idLote"=> "",
    "idUsuarioAlta"=> "",
    "idUsuarioBaja"=> "",
    "idUsuarioMod"=> "",
    "identificacionProveedor"=> "",
    "idprefijo"=> "",
    "idpunrang"=> "",
    "impuestoTotalTemp"=> "",
    "incoterm"=> "",
    "listaDetalle"=> array(
      "activo"=> "",
      "cantdisponotas"=> "",
      "cantidad"=> "",
      "codigoproducto"=> "",
      "descripcion"=> "Descripciones de la Factura",
      "descuento"=> "",
      "estado"=> "",
      "fechaAlta"=> "",
      "fechaBaja"=> "",
      "fechaMod"=> "",
      "gramaje"=> "",
      "id"=> "",
      "idFkEmpresa"=> "",
      "idUsuarioAlta"=> "",
      "idUsuarioBaja"=> "",
      "idUsuarioMod"=> "",
      "idfactura"=> "",
      "listaImpuestos"=> array(
        "activo"=> "",
        "baseimponible"=> "",
        "codigoImpuestoRetencion"=> "01",
        "codigoproducto"=> "",
        "estado"=> "",
        "fechaAlta"=> "",
        "fechaBaja"=> "",
        "fechaMod"=> "",
        "id"=> "",
        "idFkEmpresa"=> "",
        "idUsuarioAlta"=> "",
        "idUsuarioBaja"=> "",
        "idUsuarioMod"=> "",
        "idfactura"=> "",
        "porcentaje"=> "",
        "valorImpuestoRetencion"=> ""
      ),
      "notaDescuento"=> "",
      "porcentajedescuento"=> "",
      "posicion"=> "",
      "preciosinimpuestos"=> "",
      "preciototal"=> "",
      "referencia"=> "",
      "seriales"=> "",
      "tamanio"=> "",
      "unidadmedida"=> "",
      "valorimpuestos"=> "",
      "valorunitario"=> ""
    ),
    "listaImpuestos"=> array(
      "activo"=> "",
      "baseimponible"=> "",
      "codigoImpuestoRetencion"=> "01",
      "codigoproducto"=> "",
      "estado"=> "",
      "fechaAlta"=> "",
      "fechaBaja"=> "",
      "fechaMod"=> "",
      "id"=> "",
      "idFkEmpresa"=> "",
      "idUsuarioAlta"=> "",
      "idUsuarioBaja"=> "",
      "idUsuarioMod"=> "",
      "idfactura"=> "",
      "porcentaje"=> "",
      "valorImpuestoRetencion"=> ""
    ),
    "lpref"=> "",
    "mediopago"=> "",
    "moneda"=> "COP",
    "nitProveedorTecnologico"=> "",
    "nombreProveedor"=> "",
    "nombrecompleto"=> "Jesus",
    "nombrevendedor"=> "",
    "numeroFacturaAnterior"=> "",
    "numeroaceptacioninterno"=> "",
    "numeroidentificacion"=> "1033118093",
    "ordencompra"=> "",
    "ordencompravendedor"=> "",
    "pais"=> "CO",
    "passwordPt"=> "",
    "periododepagoa"=> "",
    "porcentajeDescuento"=> "",
    "port"=> "",
    "prefijo"=> "PRUE",
    "prefijofacturamodificada"=> "",
    "primerapellido"=> "",
    "primernombre"=> "Alberto Jesus",
    "procesadoPrintNet"=> "",
    "proceso"=> "",
    "razonsocial"=> "",
    "rechazo"=> "",
    "regimen"=> "",
    "rutaCarga"=> "",
    "rutaDescarga"=> "",
    "segundoapellido"=> "Duque",
    "segundonombre"=> "Jesus",
    "server"=> "",
    "telefono"=> "",
    "tipo"=> "",
    "tipocompra"=> "2",
    "tipodocumento"=> "1",
    "tipoidentificacion"=> "13",
    "tiponota"=> "",
    "tipopersona"=> "2",
    "tokenempresa"=> $token,
    "totalbaseimponible"=> "",
    "totalfactura"=> "",
    "totalimportebruto"=> "",
    "trm"=> "",
    "usernamePt"=> "",
    "usuarioQueTomaElPedido"=> "",
    "valNotaDesTotal"=> "",
    "validacionDian"=> "",
    "zona"=> ""
  )
);

        return $variableFactura;
        
    }

    /**
     * tipoCompra es 1 credito
     * tipodocumento es 1 Factura
     * @param $token
     * @param $params
     * @return array
     */
    public function createFactura($token,$params)
    {
        date_default_timezone_set('America/Bogota');
        //var_dump(date(DATE_ATOM, strtotime('2010-12-30 23:21:46')));die;
		$variableFactura = array(
			//"arg0"=> array(
			"felCabezaDocumento"=> array(
				//Cabeza documento electrónico
				//"idEmpresa"=>"348",
				//"usuario"=>"EmpAsociNaturgas",
				//"contrasenia"=>"PwAs0c1ac10natuG4s",
				"idEmpresa"=>"385",
				"usuario"=>"UAsociacionGasNatural",
				"contrasenia"=>"Pw4s0c14c10nG4sN4tur4l",
				"token"=>$token,
				"version"=> $params['version'],
				"tipodocumento"=> $params['tipodocumento'],
				"prefijo"=> $params['prefijo'],
				"consecutivo"=> $params['consecutivo'],
				"fechafacturacion"=> "".date(DATE_ATOM, strtotime($params['fechafacturacion'])),
				"codigoPlantillaPdf"=> 1,
				//"idErp"=> "",
				"cantidadLineas"=> $params["cantidadLineas"],
				//"incoterm"=> "", 
				"tiponota"=> $params["tipoNota"], //Campo no se encuentra contratado
				"aplicafel"=> "Si",
				//"centroCostos"=> "",
				//"descripcionCentroCostos"=> "",
				"codigovendedor"=> "",
				"nombrevendedor"=> "",
				//"sucursal"=> "",
				//"pago"=> "",
				"listaDetalle"=> $params['listaDetalle'],
				"listaImpuestos"=> $params['listaImpuestos'],
				"listaFacturasModificadas"=> $params["listaFacturasModificadas"],
				//"listaDocumentosAdjuntos"=> $params["listaDocumentosAdjuntos"],
				"listaAdquirentes"=> $params["listaAdquirentes"],
				"listaCamposAdicionales"=> $params["listaCamposAdicionales"],
				//"listaDescuentos"=> $params["listaDescuentos"],
				//"listaCargos"=> $params["listaCargos"],
				//"listaCodigoBarras"=> $params["listaCodigoBarras"],
				//"listaDatosEntrega"=> $params["listaDatosEntrega"],
				"listaMediosPagos"=> $params["listaMediosPagos"],
				//"listaAnticipos"=> $params["listaAnticipos"],
				"tipoOperacion"=> $params["tipoOperacion"],
				//Fin cabeza documento electrónico
				
				//Pago
				"pago"=>$params["listaPago"],
				/*
				"pago"=>array(
					"moneda"=> $params['moneda'],
					"totalimportebruto"=> $params['totalimportebruto'],
					"totalbaseimponible"=> $params['totalBaseImponible'],
					"totalbaseconimpuestos"=> $params['totalbaseconimpuestos'],
					"totalfactura"=> $params['totalfactura'],
					//"pagoanticipado"=> "",
					"tipocompra"=> $params['tipo_compra'],
					"periododepagoa"=> $params['periodo_pago'],
					"fechavencimiento"=> "".date(DATE_ATOM, strtotime($params['fechafacturacion'])),
					"trm"=> ""
					//"trm_alterna"=> "",
					//"fechaTasaCambio"=> "",
					//"codigoMonedaCambio"=> "",
					//"totalDescuento"=> "",
					//"totalCargos"=> "",
				)
				¨/
				//Fin Pago
				
				
				/*
				"campoAdicional1"=> $params['ciudadEmpresa'],
				"campoAdicional2"=> $params['direccionEmpresa'],
				"campoAdicional3"=> $params['telefonoEmpresa'],
				"campoAdicional4"=> $params['ciudadEmpresa'],
				"campoAdicional5"=> $params['numero_autorizacion_factura'],
				"campoAdicional6"=> $params['fecha_factura'],
				"campoAdicional7"=> $params['desde_factura'],
				"campoAdicional8"=> $params['hasta_factura'],
				"campoAdicional10"=> $params['identificacionFormat'],
				"primernombre"=> $params["nombre"],
				"segundonombre"=> "",
				"primerapellido"=> $params["apellido"],
				"segundoapellido"=> "",
				"bariolocalidad"=> "",
				"clasificacionCliente"=> "",//"comentarioAcuse"=> "",
				"condicionPagoReferencia"=> "",
				"consecutivofacturamodificada"=> "",
				"cufe"=> "",
				"descripcion"=> "",
				"descuento"=> "0",
				"despachadoACiudad"=> "",
				"despachadoADireccion"=> "",
				"despachadoANombre"=> "",
				"despachadoAObservacion"=> "",
				"despachadoAObservacionLugar"=> "",
				"despachadoATelefono"=> "",
				"despachadoAZonaDespacho"=> "",
				"documentoEmitidoEn"=> "",
				"envioPorEmailPlataforma"=> "",
				"fechaemisionordencompra"=> "",
				"fecharespuesta"=> "",
				"filterTotal"=> "",
				"horaEntregaProgramada"=> "",
				"idLote"=> "",
				"nitProveedorTecnologico"=> "",
				"numeroaceptacioninterno"=> "",
				"ordencompra"=> "",
				"porcentajeDescuento"=> "",
				"prefijofacturamodificada"=> "",
				"regimen"=> "",
				//"direccion"=> $params['direccion'],
				//"email"=> $params['email'],
				//"nombrecompleto"=> $params['nombrecompleto'],
				//"tipoidentificacion"=> $params['tipoidentificacioncodigo'],
				//"tipoidentificacion"=> $params['tipoidentificacion'],
				//"numeroidentificacion"=> $params['numeroidentificacion'],
				//"pais"=> $params['pais'],
				//"telefono"=> $params['telefono'],
				//"tipopersona"=> $params['tipopersona'],
				//"razonsocial"=> $params["razonsocial"],
				
				//"accionAcuse"=> "0",
				//"aceptacion"=> "0",
				//"activo"=> "0",
				//"acuse"=> "0",
				//"acuseVista"=> false,
				//"ciudad"=> $params['ciudad'],
				//"cufefacturamodificada"=> "",
				//"descripestadodianconsolidados"=> "",
				//"enviadaAAdquirientes"=> "",
				//"estado"=> "",
				//"estadoactual"=> "",
				//"estadodian"=> "",
				//"estadodianconsolidados"=> "",
				//"fechaAlta"=> "",
				//"fechaBaja"=> "",
				//"fechaFirma"=> "",
				//"fechaMod"=> "",
				//"fechafacturacionFinal"=> "".date(DATE_ATOM, strtotime($params['fechafacturacion'])),
				//"fechafacturacionInicial"=> "".date(DATE_ATOM, strtotime($params['fechafacturacion'])),
				//"fechafacturamodificada"=> "",
				//"id"=> "",
				//"idFkEmpresa"=> "",
				//"idUsuarioAlta"=> "",
				//"idUsuarioBaja"=> "",
				//"idUsuarioMod"=> "",
				//"identificacionProveedor"=> "",
				//"idprefijo"=> "",
				//"idpunrang"=> "",
				//"impuestoTotalTemp"=> "",
				//"lpref"=> "",
				//"nombreProveedor"=> "",
				//"numeroFacturaAnterior"=> "",
				//"ordencompravendedor"=> "",
				//"passwordPt"=> "",
				//"port"=> "",
				//"procesadoPrintNet"=> "",
				//"proceso"=> "",
				//"rechazo"=> "",
				//"rutaCarga"=> "",
				//"rutaDescarga"=> "",
				//"server"=> "",
				//"tipo"=> "",
				//"tokenempresa"=> $token,
				//"usernamePt"=> "",
				//"usuarioQueTomaElPedido"=> "",
				//"valNotaDesTotal"=> "",
				//"validacionDian"=> "",
				//"zona"=> ""
				*/
			)
		);

        return $variableFactura;
        
    }


    public function createND($token,$params)
    {
        date_default_timezone_set('America/Bogota');
        //var_dump(date(DATE_ATOM, strtotime('2010-12-30 23:21:46')));die;
        $variableFactura = array(
            //"arg0" => array(
            "felCabezaDocumento" => array(
                "accionAcuse" => "0",
                "aceptacion" => "0",
                "activo" => "0",
                "acuse" => "0",
                "acuseVista" => false,
                "aplicafel" => "Si",
                "bariolocalidad" => "Santa Helena de Baviera",
                "campoAdicional1" => "Direccion Naturgas",
                "campoAdicional2" => $params['direccionEmpresa'],
                "campoAdicional3" => $params['telefonoEmpresa'],
                "campoAdicional4"=> $params['ciudadEmpresa'],
                "campoAdicional5"=> $params['numero_autorizacion_factura'],
                "campoAdicional6"=> $params['fecha_factura'],
                "campoAdicional7"=> $params['desde_factura'],
                "campoAdicional8"=> $params['hasta_factura'],
                "ciudad" => "Bogotá D.C",
                "clasificacionCliente" => "",
                "codigoPlantillaPdf" => "",
                "codigovendedor" => "",
                "comentarioAcuse" => "",
                "condicionPagoReferencia" => "",
                "consecutivo" => $params['consecutivo'],
                "consecutivofacturamodificada" => $params['consecutivofacturamodificada'],
                "cufe" => "",
                "cufefacturamodificada" => $params['cufe'],
                "departamento" => "Bogotá D.C",
                "descripcion" => "",
                "descripestadodianconsolidados" => "",
                "descuento" => "",
                "despachadoACiudad" => "",
                "despachadoADireccion" => "",
                "despachadoANombre" => "",
                "despachadoAObservacion" => "",
                "despachadoAObservacionLugar" => "",
                "despachadoATelefono" => "",
                "despachadoAZonaDespacho" => "",
                "direccion" => $params['direccion'],
                "documentoEmitidoEn" => "",
                "email" => "duque.alberto@gmail.com",
                "enviadaAAdquirientes" => "",
                "envioPorEmailPlataforma" => "",
                "estado" => "",
                "estadoactual" => "",
                "estadodian" => "",
                "estadodianconsolidados" => "",
                "fechaAlta" => "",
                "fechaBaja" => "",
                "fechaFirma" => "",
                "fechaMod" => "",
                "fechaemisionordencompra" => "",
                "fechafacturacion" => "" . date(DATE_ATOM, strtotime($params['fechafacturacion'])),
                "fechafacturacionFinal" => "",
                "fechafacturacionInicial" => "",
                "fechafacturamodificada" => "" . date(DATE_ATOM, strtotime('2018-11-05 23:21:46')),
                "fecharespuesta" => "",
                "fechavencimiento" => "".date(DATE_ATOM, strtotime('+30 day' ,strtotime($params['fechafacturacion']))),
                "filterTotal" => "",
                "horaEntregaProgramada" => "",
                "id" => "",
                "idErp" => "",
                "idFkEmpresa" => "",
                "idLote" => "",
                "idUsuarioAlta" => "",
                "idUsuarioBaja" => "",
                "idUsuarioMod" => "",
                "identificacionProveedor" => "",
                "idprefijo" => "",
                "idpunrang" => "",
                "impuestoTotalTemp" => "",
                "incoterm" => "",
                "listaDetalle" => $params['listaDetalle'],
                "listaImpuestos" => $params['listaImpuestos'],
                "lpref" => "",
                "mediopago" => "",
                "moneda" => "COP",
                "nitProveedorTecnologico" => "",
                "nombreProveedor" => "",
                "nombrecompleto" => $params['nombrecompleto'],
                "nombrevendedor" => "",
                "numeroFacturaAnterior" => "",
                "numeroaceptacioninterno" => "",
                "numeroidentificacion" => $params['numeroidentificacion'],
                "ordencompra" => "",
                "ordencompravendedor" => "",
                "pais" => "CO",
                "passwordPt" => "",
                "periododepagoa" => "",
                "porcentajeDescuento" => "",
                "port" => "",
                "prefijo" => $params['prefijo'],
                "prefijofacturamodificada" => "",
                "primerapellido" => "",
                "primernombre" => "Alberto Jesus",
                "procesadoPrintNet" => "",
                "proceso" => "",
                "razonsocial" => "",
                "rechazo" => "",
                "regimen" => "",
                "rutaCarga" => "",
                "rutaDescarga" => "",
                "segundoapellido" => "Duque",
                "segundonombre" => "Jesus",
                "server" => "",
                "telefono" => $params['telefono'],
                "tipo" => "",
                "tipocompra" => "2",
                "tipodocumento" => "3",
                "tipoidentificacion" => "13",
                "tiponota" => "8",
                "tipopersona" => "2",
                "tokenempresa" => $token,
                "totalbaseimponible" => "200",
                "totalfactura" => $params['totalfactura'],
                "totalimportebruto" => $params['totalimportebruto'],
                "trm" => "",
                "usernamePt" => "",
                "usuarioQueTomaElPedido" => "",
                "valNotaDesTotal" => "",
                "validacionDian" => "",
                "zona" => ""
            )
        );
    }

    public function createNC($token,$params)
    {
        date_default_timezone_set('America/Bogota');
        //var_dump(date(DATE_ATOM, strtotime('2010-12-30 23:21:46')));die;
        $variableFactura = array(
  //"arg0"=> array(
  "felCabezaDocumento"=> array(
    "accionAcuse"=> "0",
    "aceptacion"=> "0",
    "activo"=> "0",
    "acuse"=> "0",
    "acuseVista"=> false,
    "aplicafel"=> "Si",
    "bariolocalidad"=> "Santa Helena de Baviera",
      "campoAdicional1"=> $params['ciudadEmpresa'],
      "campoAdicional2"=> $params['direccionEmpresa'],
      "campoAdicional3"=> $params['telefonoEmpresa'],
      "campoAdicional4"=> $params['ciudadEmpresa'],
      "campoAdicional5"=> $params['numero_autorizacion_factura'],
      "campoAdicional6"=> $params['fecha_factura'],
      "campoAdicional7"=> $params['desde_factura'],
      "campoAdicional8"=> $params['hasta_factura'],
      "campoAdicional10"=> $params['identificacionFormat'],
    "ciudad"=> $params['ciudad'],
    "clasificacionCliente"=> "",
    "codigoPlantillaPdf"=> "",
    "codigovendedor"=> "",
    "comentarioAcuse"=> "",
    "condicionPagoReferencia"=> "",
    "consecutivo"=> $params['consecutivo'],
    //"consecutivofacturamodificada"=> $params['consecutivofacturamodificada'],
    "cufe"=> "",
    //"cufefacturamodificada"=> $params['cufe'],
    "departamento"=> $params['ciudad'],
    "descripcion"=> "",
    "descripestadodianconsolidados"=> "",
    "descuento"=> "",
    "despachadoACiudad"=> "",
    "despachadoADireccion"=> "",
    "despachadoANombre"=> "",
    "despachadoAObservacion"=> "",
    "despachadoAObservacionLugar"=> "",
    "despachadoATelefono"=> "",
    "despachadoAZonaDespacho"=> "",
    "direccion"=> $params['direccion'],
    "documentoEmitidoEn"=> "",
    "email"=> $params['email'],
    "enviadaAAdquirientes"=> "",
    "envioPorEmailPlataforma"=> "",
    "estado"=> "",
    "estadoactual"=> "",
    "estadodian"=> "",
    "estadodianconsolidados"=> "",
    "fechaAlta"=> "",
    "fechaBaja"=> "",
    "fechaFirma"=> "",
    "fechaMod"=> "",
    "fechaemisionordencompra"=> "",
    "fechafacturacion"=> "".date(DATE_ATOM, strtotime($params['fechafacturacion'])),
    "fechafacturacionFinal"=> "",
    "fechafacturacionInicial"=> "",
    "fechafacturamodificada"=> "".date(DATE_ATOM, strtotime('2018-11-05 23:21:46')),
    "fecharespuesta"=> "",
    "fechavencimiento"=> "".date(DATE_ATOM, strtotime('+30 day' ,strtotime($params['fechafacturacion']))),
    "filterTotal"=> "",
    "horaEntregaProgramada"=> "",
    "id"=> "",
    "idErp"=> "",
    "idFkEmpresa"=> "",
    "idLote"=> "",
    "idUsuarioAlta"=> "",
    "idUsuarioBaja"=> "",
    "idUsuarioMod"=> "",
    "identificacionProveedor"=> "",
    "idprefijo"=> "",
    "idpunrang"=> "",
    "impuestoTotalTemp"=> "",
    "incoterm"=> "",
    "listaDetalle"=> $params['listaDetalle'],
    "listaFacturasmodificadas"=> $params['listaFacturasModificadas'],
    "listaImpuestos"=> $params['listaImpuestos'],
    "lpref"=> "",
    "mediopago"=> "",
    "moneda"=> $params['moneda'],
    "nitProveedorTecnologico"=> "",
    "nombreProveedor"=> "",
    "nombrecompleto"=> $params['nombrecompleto'],
    "nombrevendedor"=> "",
    "numeroFacturaAnterior"=> "",
    "numeroaceptacioninterno"=> "",
    "numeroidentificacion"=> $params['numeroidentificacion'],
    "ordencompra"=> "",
    "ordencompravendedor"=> "",
    "pais"=> $params['pais'],
    "passwordPt"=> "",
    "periododepagoa"=> "",
    "porcentajeDescuento"=> "",
    "port"=> "",
      "prefijo"=> $params['prefijo'],
      "prefijofacturamodificada"=> "",
      "primerapellido"=> $params["apellido"],
      "primernombre"=> $params["nombre"],
      "procesadoPrintNet"=> "",
      "proceso"=> "",
      "razonsocial"=> $params["razonsocial"],
      "rechazo"=> "",
      "regimen"=> "",
      "rutaCarga"=> "",
      "rutaDescarga"=> "",
      "segundoapellido"=> "",
      "segundonombre"=> "",
      "server"=> "",
      "telefono"=> $params['telefono'],
    "tipo"=> "",
      "tipocompra"=> "1",
      "tipodocumento"=> $params['tipodocumento'],
      "tipoidentificacion"=> $params['tipoidentificacion'],
      "tiponota"=> "1",
      "tipopersona"=> $params['tipopersona'],
      "tokenempresa"=> $token,
      "totalbaseimponible"=> $params['totalBaseImponible'],
      "totalfactura"=> $params['totalfactura'],
      "totalimportebruto"=> $params['totalimportebruto'],
    "trm"=> "",
    "usernamePt"=> "",
    "usuarioQueTomaElPedido"=> "",
    "valNotaDesTotal"=> "",
    "validacionDian"=> "",
    "zona"=> ""
  )
);

        return $variableFactura;
        
    }
    
    
    public function loadNC($model,$detalle,$informacionEmpresa,$infoFactura=null,$infoFacturas=null){
        date_default_timezone_set('America/Bogota');
        $NCXml =array();
		
		$descripcion = $model->tipo_factura == 'FA' || $model->tipo_factura == 'ND' ? 'descripcion' : 'observacion';
		
		//Cabeza documento electrónico
        $NCXml['version'] = $informacionEmpresa ? $informacionEmpresa['version_manual'] : '';
		$NCXml['tipodocumento'] =  $model->tipoDocumento ? $model->tipoDocumento: '';
		//$NCXml['prefijo'] = $model->tipo_factura == 'FA' ? 'FENT' : $model->tipo_factura;
		$NCXml['prefijo'] = $model->tipo_factura == 'FA' ? 'FENT' : $model->tipo_factura;
		if($model->tipoDocumento == 5)
            $NCXml['prefijo'] = 'CONT'; //Factura de Contingencia, se tenia CONT se cambia FC
        if($model->tipo_factura=='NC')
            $NCXml['prefijo'] = "NCNT"; //NOta Crédito, se tiene NCNT se cambia por NC
        if($model->tipo_factura=='ND')
            $NCXml['prefijo'] = "NDNT"; //Nota Débito, se tiene NDNT se cambia por ND
		
		$NCXml['consecutivo'] = "".$model->numero;
		$NCXml['fechafacturacion'] = $model->fecha;
		$NCXml['cantidadLineas'] = $model->cantidadLineas;
		$NCXml['tipoNota'] = $model->tipoNota;
        $totalBaseImponible = 0;
        $totalbaseconimpuestos = 0;
		
		
        foreach ($detalle as $df) {
            $baseImponible = floatval(str_replace(",","",$model->tipo_factura == 'FA' || $model->tipo_factura == 'ND' ? $df['subtotal'] : $df['valor']));
            $totalBaseImponible += $baseImponible;
            $totalbaseconimpuestos += $df['valor'] + (($df['iva'] * $baseImponible)/100);
            $detalle_factura[] = array(
									"codigoproducto"=> $df['id_producto'],
									"tipocodigoproducto"=> $df['tipo_codigo_producto'],
									"nombreProducto"=> $df['producto'],
									//"grupo"=> "",
									//"familia"=> "",
									//"marca"=> "",
									//"modelo"=> "",
									"descripcion"=> $df[$descripcion] ? $df['producto']."\n".$df[$descripcion] : $df['producto'],
									"referencia"=> "",
									"cantidad"=> $df['cantidad'],
									"unidadmedida"=> "94",
									"valorunitario"=> $baseImponible,
									"preciosinimpuestos"=>  $baseImponible,
									//"preciototal"=> floatval(str_replace(",","",$df['valorTotal'])),
									"preciototal"=> $baseImponible,
									"posicion"=>"",
									"seriales"=> "",
									"gramaje"=> "",
									"tamanio"=> "",
									"muestracomercial"=> "",
									"muestracomercialcodigo"=> "",
									"tipoImpuesto"=> $df['tipo_impuesto'],
									"listaImpuestos"=> array(
										"codigoImpuestoRetencion"=> "01", //$model->idImpuesto->identificador,
										"porcentaje"=> $df['iva'],
										"valorImpuestoRetencion"=> ($df['iva'] * $baseImponible)/100,
										"baseimponible"=> $baseImponible,
										"isAutoRetenido"=> false,
										//"activo"=> "",
										//"codigoproducto"=> $df['id_producto'],
										//"estado"=> "",
										//"fechaAlta"=> "",
										//"fechaBaja"=> "",
										//"fechaMod"=> "",
										//"id"=> "",
										//"idFkEmpresa"=> "",
										//"idUsuarioAlta"=> "",
										//"idUsuarioBaja"=> "",
										//"idUsuarioMod"=> "",
										//"idfactura"=> "",
										//"consecutivo"=> $NCXml['consecutivo']
									),
									//"listadedescuentos"=>"",
									//"listaCargos"=>"",
									//"listaCodigoBarras"=>"",
									//"listaCamposAdicionales"=>"",
									//"aplicaMandato"=>"",
									/*"listaMandantes"=>array(
										"identificacionMandante"=>""
										,"digitoVerificacionMandante"=>""
										,"tipoIdentificacionMandante"=>""
										,"tipoPersona"=>""
										,"tipoObligacion"=>""
										,"tipoRepresentacion"=>""
										,"tipoEstablecimiento"=>""
										,"País"=>""
										,"Departamento"=>""
										,"Ciudad"=>""
										,"Dirección"=>""
										,"nombreMandante"=>""
										,"zonaPostal"=>""
										,"numeroContrato"=>""
										,"descripcionContrato"=>""
										,"tipoContrato"=>""
									),*/
									//"descuento"=> "",
									//"porcentajedescuento"=> "",
									//"activo"=> "",
									//"consecutivo"=> $NCXml['consecutivo'],
									//"cantdisponotas"=> "",
									//"estado"=> "",
									//"fechaAlta"=> "",
									//"fechaBaja"=> "",
									//"fechaMod"=> "",
									//"id"=> "",
									//"idFkEmpresa"=> "",
									//"idUsuarioAlta"=> "",
									//"idUsuarioBaja"=> "",
									//"idUsuarioMod"=> "",
									//"idfactura"=> "",
									//"notaDescuento"=> "",
									//"valorimpuestos"=> "",
								);
        }
		$NCXml['listaDetalle'] = $detalle_factura;
		//Fin detalle documento electrónico
		
		$NCXml['listaImpuestos'] = array(
									"codigoImpuestoRetencion"=> "01", //$model->idImpuesto->identificador,
									"porcentaje"=> $detalle[0]["iva"],
									"valorImpuestoRetencion"=> $model->iva,
									"baseimponible"=> $model->subtotal,
									"isAutoRetenido"=> false,
									//"activo"=> "",
									//"codigoproducto"=> "",
									//"estado"=> "",
									//"fechaAlta"=> "",
									//"fechaBaja"=> "",
									//"fechaMod"=> "",
									//"id"=> "",
									//"idFkEmpresa"=> "",
									//"idUsuarioAlta"=> "",
									//"idUsuarioBaja"=> "",
									//"idUsuarioMod"=> "",
									//"idfactura"=> "",
									//"consecutivo"=> $NCXml['consecutivo']
								);
		//Fin impuesto documento electrónico
		
		/*$listaDocumentosAdjuntos = array(
			"nombreConExtension"=>""
			,"contenidoDelDoc"=>""
		);
		$NCXml['listaDocumentosAdjuntos'] = $listaDocumentosAdjuntos;*/
		/*if($model->tipo_factura != 'FA') {
            if($infoFacturas) {
                foreach ($infoFacturas as $factura) {
                    $facturasModificadas[] = array(
                        "tipoDocumentoFacturaModificada" => "",
                        "prefijoFacturaModificada" => "",
                        "consecutivofacturamodificada" => $factura->numero,
						
                        //"consecutivo" => $model->numero,
                        //"cufefacturamodificada" => $factura->cufe,
                        //"fechafacturamodificada" => "" . date(DATE_ATOM, strtotime($factura->fecha)),//Fecha temporal se debe colocar una nueva fecha que es donde se guarda el cufe
                        //"idfactura" => "",
                        //"idfkempresa" => "",
                        //"observacion" => ""
                    );
                }
            }
            else{
                $facturasModificadas[] = array(
					"tipoDocumentoFacturaModificada" => "",
                    "prefijoFacturaModificada" => "",
                    "consecutivofacturamodificada" => $NCXml['consecutivofacturamodificada'],
					
                    //"consecutivo" => $model->numero,
                    //"cufefacturamodificada" => $NCXml['cufe'],
                    //"fechafacturamodificada" => "" . date(DATE_ATOM, strtotime($infoFactura->fecha)),//Fecha temporal se debe colocar una nueva fecha que es donde se guarda el cufe
                    //"idfactura" => "",
                    //"idfkempresa" => "",
                    //"observacion" => ""
                );
            }
        }*/
		
        //if($model->tipo_factura == 'FA') {
			switch($model->tipoDocumento){
				case 1://Factura
					$facturasModificadas[] = array(
						"tipoDocumentoFacturaModificada" => "",
						"prefijoFacturaModificada" => "",
						"consecutivofacturamodificada" => '',
						
						//"consecutivo" => '',
						//"cufefacturamodificada" => "",
						//"fechafacturamodificada" => "",
						//"idfactura" => "",
						//"idfkempresa" => "",
						//"observacion" => ""
					);
					break;
				case 2: //Nota Crédito
					switch($model->tipoDocumentoFacturaModificada){
						case 1:
							$td = 1; //Factura
							break;
						case 2:
							$td = 5; //Contingencia
							break;
						case 3:
							break;
					}
					/*
					WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
					*/
					
					if($infoFacturas) {
						foreach ($infoFacturas as $factura) {
							$facturasModificadas[] = array(
								"tipoDocumentoFacturaModificada" => 1,
								"prefijoFacturaModificada" => "SETT",
								"consecutivoFacturaModificada" => $factura->numero,
								
								//"consecutivo" => $model->numero,
								//"cufefacturamodificada" => $factura->cufe,
								//"fechafacturamodificada" => "" . date(DATE_ATOM, strtotime($factura->fecha)),//Fecha temporal se debe colocar una nueva fecha que es donde se guarda el cufe
								//"idfactura" => "",
								//"idfkempresa" => "",
								//"observacion" => ""
							);
						}
					}
					else{
						$facturasModificadas[] = array(
							"tipoDocumentoFacturaModificada" => $td,
							//"prefijoFacturaModificada" => "SETT", //SETT = Pruebas
							"prefijoFacturaModificada" => $td == 5 ? "FC" :  "SETT", //SETT = Pruebas
							"consecutivoFacturaModificada" => $model->facturaNumero,
							
							//"consecutivo" => '',
							//"cufefacturamodificada" => "",
							//"fechafacturamodificada" => "",
							//"idfactura" => "",
							//"idfkempresa" => "",
							//"observacion" => ""
						);
					}
					break;
				case 3: //Nota Debito
					switch($model->tipoDocumentoFacturaModificada){
						case 1:
							$td = 1;
							break;
						case 2:
							$td = 5;
							break;
						case 3:
							break;
					}
					$facturasModificadas[] = array(
						"tipoDocumentoFacturaModificada" => $td,
						"prefijoFacturaModificada" => $td == 5 ? "FC" :  "SETT", //SETT = Pruebas
						"consecutivoFacturaModificada" => $model->facturaNumero,
						
						//"consecutivo" => '',
						//"cufefacturamodificada" => "",
						//"fechafacturamodificada" => "",
						//"idfactura" => "",
						//"idfkempresa" => "",
						//"observacion" => ""
					);
					break;
				case 5: //Contingencia
					$facturasModificadas[] = array( 
						"tipoDocumentoFacturaModificada" => $model->tipoDocumento,
						"prefijoFacturaModificada" => "FC",
						"consecutivoFacturaModificada" => $model->numero,
						
						//"consecutivo" => '',
						//"cufefacturamodificada" => "",
						//"fechafacturamodificada" => "",
						//"idfactura" => "",
						//"idfkempresa" => "",
						//"observacion" => ""
					);
					break;
			}
        //}
		
        $NCXml['listaFacturasModificadas'] = $facturasModificadas;

		/*
		if($model->tipo_factura == 'FA') {
            if(!$model->id_empresa)
            {
                if($model->idPersona->tipo_documento==1)
                    $tipoIdentificacion = 13;
                if($model->idPersona->tipo_documento==2)
                    $tipoIdentificacion = 22;
                if($model->idPersona->tipo_documento==3)
                    $tipoIdentificacion = 41;
            }
            $NCXml['tipopersona'] =$model->id_empresa ? 1 : 2;
            $NCXml['razonsocial'] = $model->id_empresa ?  $model->idEmpresa->nombre : "";
            $NCXml['nombre'] = !$model->id_empresa ?  $model->idPersona->nombre : "";
            $NCXml['apellido'] = !$model->id_empresa ?  $model->idPersona->apellido : "";
            $NCXml['pais'] = $model->id_empresa ? $model->idEmpresa->ciudad->idPais->alias : $model->idPersona->ciudad->idPais->alias;
			
            //$NCXml['ciudad'] = $model->id_empresa ? $model->idEmpresa->ciudad->nombre : $model->idPersona->ciudad->nombre;
            //$NCXml['tipoidentificacion'] =$model->id_empresa ? 31 : $tipoIdentificacion;
        }
        if($model->tipo_factura != 'FA') {
            if(!$infoFactura->id_empresa)
            {
                if($infoFactura->idPersona->tipo_documento==1)
                    $tipoIdentificacion = 13;
                if($infoFactura->idPersona->tipo_documento==2)
                    $tipoIdentificacion = 22;
                if($infoFactura->idPersona->tipo_documento==3)
                    $tipoIdentificacion = 41;
            }
            $NCXml['tipopersona'] =$infoFactura->id_empresa ? 1 : 2;
            $NCXml['razonsocial'] = $infoFactura->id_empresa ?  $infoFactura->idEmpresa->nombre : "";
            $NCXml['nombre'] = !$infoFactura->id_empresa ?  $infoFactura->idPersona->nombre : "";
            $NCXml['apellido'] = !$infoFactura->id_empresa ?  $infoFactura->idPersona->apellido : "";
            $NCXml['pais'] = $infoFactura->id_empresa ? $infoFactura->idEmpresa->ciudad->idPais->alias : $infoFactura->idPersona->ciudad->idPais->alias;
			
            //$NCXml['tipoidentificacion'] =$infoFactura->id_empresa ? 31 : $tipoIdentificacion;
            //$NCXml['ciudad'] = $infoFactura->id_empresa ? $infoFactura->idEmpresa->ciudad->nombre : $infoFactura->idPersona->ciudad->nombre;
        }
		$NCXml['nombrecompleto'] =  $model->id_empresa ? '': $model->clientes;
		$NCXml['tipoIdentificacion'] =  $model->tipoidentificacion;
		$NCXml['numeroidentificacion'] =$model->identificacion;
		$NCXml['digitoVerificacion'] =$model->identificacionFormat;
		$NCXml['email'] =isset($model->idEmpresa->correo_facturacion_electronica) && $model->idEmpresa->correo_facturacion_electronica ? trim($model->idEmpresa->correo_facturacion_electronica) : 'factura.electronica@naturgas.com.co';//"duque.alberto@gmail.com";
        if($NCXml['email']=="")
            $NCXml['email'] = 'factura.electronica@naturgas.com.co';
		$NCXml['departamento'] =  $model->departamento;
        $NCXml['ciudad'] =  $model->ciudad;
		$NCXml['direccion'] =$model->direccion;
		$NCXml['telefono'] =$model->telefonoContacto;
		*/
		$listaAdquirentes = array(
			"tipoPersona"=> $model->id_empresa ? 1 : 2
			//, "nombrecompleto"=> $model->id_empresa ? $infoFactura->idPersona->nombre.' '.$infoFactura->idPersona->apellido : $model->clientes
			, "nombreCompleto"=> $model->id_empresa ? $model->clientes : ""
			, "tipoIdentificacion"=> $model->tipoidentificacion
			, "numeroIdentificacion"=> $model->identificacion
			, "digitoverificacion"=> $model->tipoidentificacion==31 ?  $model->verificacion : ""
			, "regimen"=> "05"
			, "email"=> isset($model->idEmpresa->correo_facturacion_electronica) && $model->idEmpresa->correo_facturacion_electronica ? 
				trim($model->idEmpresa->correo_facturacion_electronica) : 
				//'factura.electronica@naturgas.com.co'
				//'daniel.morales@bss.morpss.com;comercial@tingersuministros.com' quemado en el codigo
				'comercial@tingersuministros.com'
			, "pais"=> $model->id_empresa ? $model->idEmpresa->ciudad->idPais->alias : $model->idPersona->ciudad->idPais->alias
			, "departamento"=>$model->departamento
			, "nombreDepartamento"=>ucwords(strtolower($model->departamentoNombre))
			, "codigoCiudad"=>$model->ciudad
			, "descripcionCiudad"=> strtoupper($model->id_empresa ? $model->idEmpresa->ciudad->nombre : $model->idPersona->ciudad->nombre)
			//, "barioLocalidad"=> ""
			, "direccion"=> $model->direccion
			, "telefono"=> $model->telefonoContacto
			//, "codigoPostal"=>""
			, "envioPorEmailPlataforma"=>"email"
			//, "nitProveedorTecnologico"=>""
			, "tipoObligacion"=>"O-14"
			, "paisNombre"=>ucfirst(strtolower($model->id_empresa ? $model->idEmpresa->ciudad->idPais->nombre : $model->idPersona->ciudad->idPais->nombre))
			//, "codigoCIUU"=>""
			//, "matriculaMercantil"=>""
		);
        $NCXml['listaAdquirentes'] = $listaAdquirentes;
		
		//$NCXml['direccionEmpresa'] = $informacionEmpresa ? $informacionEmpresa['direccion'] : '';
        //$NCXml['telefonoEmpresa'] = $informacionEmpresa ? $informacionEmpresa['telefono'] : '';
        //$NCXml['numero_autorizacion_factura'] = $informacionEmpresa ? $informacionEmpresa['numero_autorizacion_factura'] : '';
        //$NCXml['fecha_factura'] = $informacionEmpresa ? $informacionEmpresa['fecha_factura'] : '';
        //$NCXml['desde_factura'] = $informacionEmpresa ? $informacionEmpresa['desde_factura'] : '';
        //$NCXml['hasta_factura'] = $informacionEmpresa ? $informacionEmpresa['hasta_factura'] : '';
        //$NCXml['ciudadEmpresa'] = $informacionEmpresa ? $informacionEmpresa['ciudad'] : '';
        //$NCXml['nombreEmpresa'] = $informacionEmpresa ? $informacionEmpresa['nombre'] : '';
        //$NCXml['identificacionFormat'] =$model->identificacionFormat;
		$listaCamposAdicionales = array(
			array(
				"nombreCampo" => "ciudadEmp"
				,"valorCampo" => $informacionEmpresa['ciudad']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"direccionEmp"
				,"valorCampo"=>$informacionEmpresa['direccion']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"telefonoEmp"
				,"valorCampo"=>$informacionEmpresa['telefono']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"regimenEmp"
				,"valorCampo"=>"Responsable de IVA"
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"autfactelec"
				,"valorCampo"=>$informacionEmpresa['numero_autorizacion_factura']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"fechaautfact"
				,"valorCampo"=>$informacionEmpresa['fecha_factura']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"RangoInicial"
				,"valorCampo"=>$informacionEmpresa['desde_factura']
				,"orden"=>0
				,"seccion"=>0
			),
			array(
				"nombreCampo"=>"RangoFinal"
				,"valorCampo"=>$informacionEmpresa['hasta_factura']
				,"orden"=>0
				,"seccion"=>0
			),
		);
		$NCXml['listaCamposAdicionales'] = $listaCamposAdicionales;
		$listaOrdenesCompra = array(
			"ordencompra" => $model->orden_compra
			,"fechaemisionordencompra" => $model->fechaemisionordencompra
			,"numeroaceptacioninterno" => $model->numeroaceptacioninterno
		);
		$NCXml['listaOrdenesCompra'] = $listaOrdenesCompra;
		/*$listaDescuentos = array(
			"codigoDescuento"=>""
			,"descuento"=>""
			,"porcentajeDescuento"=>""
			,"descripcion"=>""
		);
		$NCXml['listaDescuentos'] = $listaDescuentos;*/
		/*$listaCargos = array(
			"codigoCargo"=>""
			,"valorCargo"=>""
			,"porcentajeCargo"=>""
			,"descripcion"=>""
		);
		$NCXml['listaCargos'] = $listaCargos;*/
		/*$listaCodigoBarras = array(){
			"cadenaACodificar"=>""
			,"tipoModelo"=>""
			,"orden"=>""
			,"tipoCodificacion"=>""
			,"descripcion"=>""
			,"fecha"=>""
		}
		$NCXml['listaCodigoBarras'] = $listaCodigoBarras;*/
		/*$listaDatosEntrega = array(
			"cantidad"=>""
			,"cantidadMaxima"=>""
			,"cantidadMínima"=>""
			,"identificadorTransporte"=>""
			,"paisEntrega"=>""
			,"ciudadEntrega"=>""
			,"direccionEntrega"=>""
			,"lugarEntrega"=>""
			,"telefonoEntrega"=>""
			,"periodoEntregaEstimado"=>""
			,"periodoEntregaPrometido"=>""
			,"periodoEntregaSolicitado"=>""
			,"tiempoRealEntrega"=>""
			,"ultimaFechaEntrega"=>""
			,"nombretransportista"=>""
			,"tipoidentificacionTransportista"=>""
			,"identificacionTransportista"=>""
			,"dVIdentificaciontransportista"=>""
			,"empresaTransportista"=>""
			,"tipoIdentificacionEmpresaTransportista"=>""
			,"nitEmpresaTransportista"=>""
			,"descripcion"=>""
		);
		$NCXml['listaDatosEntrega'] = $listaDatosEntrega;*/
		$listaMediosPagos = array(
			"medioPago" =>$model->idMedioPago->codigo
		);
		$NCXml['listaMediosPagos'] = $listaMediosPagos;
		/*$listaAnticipos = array(
			"anticipo"=>""
			,"fechaAnticipo"=>""
			,"descripcion"=>""
		);
		$NCXml['listaAnticipos'] = $listaAnticipos;*/
		//$NCXml['tipoOperacion'] = "05";
		$NCXml['tipoOperacion'] = "10";
		//Fin cabeza documento electrónico
		//Pago
		if($model->idMoneda->codigo == 'COP')
		{
    		$listaPago = array(
    			"moneda"=> $model->idMoneda->codigo,
    			"totalimportebruto"=> $model->subtotal,
    			"totalbaseimponible"=> $totalBaseImponible,
    			"totalbaseconimpuestos"=> $model->total,
    			"totalfactura"=> $model->total,
    			"tipocompra"=> $model->tipo_compra, //1 Contado, 2 Crédito
    			"periododepagoa"=> $model->periodo_pago, //Días
    			"fechavencimiento"=> $model->fecha_vencimiento
    		);
		}
		if($model->idMoneda->codigo !== 'COP')
		{
    		$listaPago = array(
    			"moneda"=> $model->idMoneda->codigo,
    			"totalimportebruto"=> $model->subtotal,
    			"totalbaseimponible"=> $totalBaseImponible,
    			"totalbaseconimpuestos"=> $model->total,
    			"totalfactura"=> $model->total,
    			"tipocompra"=> $model->tipo_compra, //1 Contado, 2 Crédito
    			"periododepagoa"=> $model->periodo_pago, //Días
    			"fechavencimiento"=> $model->fecha_vencimiento,
    			"trm"=> $model->idMoneda->codigo == 'COP' ? '' : $model->trm ,
    			"codigoMonedaCambio"=> $model->idMoneda->codigo == 'COP' ? '' : "COP",
    			"fechaTasaCambio" => $model->idMoneda->codigo == 'COP' ? '' : date(DATE_ATOM, strtotime($params['fechafacturacion']))
    		);
		}
		$NCXml['listaPago'] = $listaPago;
		//Fin Pago
		
        //$NCXml['direccionEmpresa'] = $informacionEmpresa ? $informacionEmpresa['direccion'] : '';
        //$NCXml['telefonoEmpresa'] = $informacionEmpresa ? $informacionEmpresa['telefono'] : '';
        //$NCXml['numero_autorizacion_factura'] = $informacionEmpresa ? $informacionEmpresa['numero_autorizacion_factura'] : '';
        //$NCXml['fecha_factura'] = $informacionEmpresa ? $informacionEmpresa['fecha_factura'] : '';
        //$NCXml['desde_factura'] = $informacionEmpresa ? $informacionEmpresa['desde_factura'] : '';
        //$NCXml['hasta_factura'] = $informacionEmpresa ? $informacionEmpresa['hasta_factura'] : '';
        //$NCXml['ciudadEmpresa'] = $informacionEmpresa ? $informacionEmpresa['ciudad'] : '';
        //$NCXml['nombreEmpresa'] = $informacionEmpresa ? $informacionEmpresa['nombre'] : '';
        //$NCXml['identificacionFormat'] =$model->identificacionFormat;
		
        
        if($model->tipo_factura != 'FA')
        {
            $NCXml['consecutivofacturamodificada'] =  "FENT".$infoFactura->numero;
            $NCXml['cufe'] = $infoFactura->cufe;
        }
        
        
        
        
        
        
        return $NCXml;
    }



    public function loadSF($model,$detalle,$informacionEmpresa,$infoFactura=null){
         date_default_timezone_set('America/Bogota');
        $NCXml =array();
        $NCXml['direccionEmpresa'] = $informacionEmpresa ? $informacionEmpresa['direccion'] : '';
        $NCXml['telefonoEmpresa'] = $informacionEmpresa ? $informacionEmpresa['telefono'] : '';
        $NCXml['numero_autorizacion_factura'] = $informacionEmpresa ? $informacionEmpresa['numero_autorizacion_factura'] : '';
        $NCXml['fecha_factura'] = $informacionEmpresa ? $informacionEmpresa['fecha_factura'] : '';
        $NCXml['desde_factura'] = $informacionEmpresa ? $informacionEmpresa['desde_factura'] : '';
        $NCXml['hasta_factura'] = $informacionEmpresa ? $informacionEmpresa['hasta_factura'] : '';
        $NCXml['ciudadEmpresa'] = $informacionEmpresa ? $informacionEmpresa['ciudad'] : '';
        $NCXml['nombreEmpresa'] = $informacionEmpresa ? $informacionEmpresa['nombre'] : '';
        $NCXml['fechafacturacion'] = $model->fecha;
        $NCXml['totalimportebruto'] = $model->subtotal;
        $NCXml['totalfactura'] = $model->total;
        $NCXml['prefijo'] = $model->tipo_factura == 'FA' ? 'FENT' : $model->tipo_factura;
        $NCXml['consecutivo'] = "".$model->numero;
        $NCXml['direccion'] =$model->direccion;
        $NCXml['telefono'] =$model->telefonoContacto;
        $NCXml['numeroidentificacion'] =$model->identificacion;
        $NCXml['nombrecompleto'] =$model->clientes;
        $NCXml['tipodocumento'] =  $model->tipoDocumento ? $model->tipoDocumento: '';
        if($model->tipo_factura == 'FA') {
            if(!$model->id_empresa)
            {
                if($model->idPersona->tipo_documento==1)
                    $tipoIdentificacion = 13;
                if($model->idPersona->tipo_documento==2)
                    $tipoIdentificacion = 22;
                if($model->idPersona->tipo_documento==3)
                    $tipoIdentificacion = 41;
            }
            $NCXml['pais'] = $model->id_empresa ? $model->idEmpresa->ciudad->idPais->alias : $model->idPersona->ciudad->idPais->alias;
            $NCXml['ciudad'] = $model->id_empresa ? $model->idEmpresa->ciudad->nombre : $model->idPersona->ciudad->nombre;
            $NCXml['tipoidentificacion'] =$model->id_empresa ? 31 : $tipoIdentificacion;
            $NCXml['tipopersona'] =$model->id_empresa ? 1 : 2;
            $NCXml['razonsocial'] = $model->id_empresa ?  $model->idEmpresa->nombre : "";
            $NCXml['nombre'] = !$model->id_empresa ?  $model->idPersona->nombre : "";
            $NCXml['apellido'] = !$model->id_empresa ?  $model->idPersona->apellido : "";
        }
        if($model->tipo_factura != 'FA') {
            if(!$infoFactura->id_empresa)
            {
                if($infoFactura->idPersona->tipo_documento==1)
                    $tipoIdentificacion = 13;
                if($infoFactura->idPersona->tipo_documento==2)
                    $tipoIdentificacion = 22;
                if($infoFactura->idPersona->tipo_documento==3)
                    $tipoIdentificacion = 41;
            }
            $NCXml['tipoidentificacion'] =$infoFactura->id_empresa ? 31 : $tipoIdentificacion;
            $NCXml['tipopersona'] =$infoFactura->id_empresa ? 1 : 2;
            $NCXml['razonsocial'] = $infoFactura->id_empresa ?  $infoFactura->idEmpresa->nombre : "";
            $NCXml['nombre'] = !$infoFactura->id_empresa ?  $infoFactura->idPersona->nombre : "";
            $NCXml['apellido'] = !$infoFactura->id_empresa ?  $infoFactura->idPersona->apellido : "";
            $NCXml['pais'] = $infoFactura->id_empresa ? $infoFactura->idEmpresa->ciudad->idPais->alias : $infoFactura->idPersona->ciudad->idPais->alias;
            $NCXml['ciudad'] = $infoFactura->id_empresa ? $infoFactura->idEmpresa->ciudad->nombre : $infoFactura->idPersona->ciudad->nombre;
        }

        $NCXml['moneda'] =$model->idMoneda->simbolo;
        $NCXml['email'] ="factura.electronica@naturgas.com.co";
        $descripcion = $model->tipo_factura == 'FA' || $model->tipo_factura == 'ND' ? 'observacion' : 'observacion';
        if($model->tipo_factura != 'FA')
        {
            $NCXml['consecutivofacturamodificada'] =  "FENT".$model->numero;
            $NCXml['cufe'] = $model->cufe;
        }
        //for($i=0;$i<8;$i++){
        foreach ($detalle as $df) {
            $descripciones = $df['id_inscripcion'] ? $this->getInscripciones($df['id_inscripcion']) : $df['producto']."\n".$df[$descripcion] ;
            $detalle_factura[] = array(
                "activo"=> "",
                "consecutivo"=> $NCXml['consecutivo'],
                "cantdisponotas"=> "",
                "cantidad"=> $df['cantidad'],
                "codigoproducto"=> "",
                "descripcion"=> $descripciones,
                "descuento"=> "",
                "estado"=> "",
                "fechaAlta"=> "",
                "fechaBaja"=> "",
                "fechaMod"=> "",
                "gramaje"=> "",
                "id"=> "",
                "idFkEmpresa"=> "",
                "idUsuarioAlta"=> "",
                "idUsuarioBaja"=> "",
                "idUsuarioMod"=> "",
                "idfactura"=> "",
                "listaImpuestos"=> array(
                    "activo"=> "",
                    "baseimponible"=> "",
                    "codigoImpuestoRetencion"=> "01",
                    "codigoproducto"=> "",
                    "estado"=> "",
                    "fechaAlta"=> "",
                    "fechaBaja"=> "",
                    "fechaMod"=> "",
                    "id"=> "",
                    "idFkEmpresa"=> "",
                    "idUsuarioAlta"=> "",
                    "idUsuarioBaja"=> "",
                    "idUsuarioMod"=> "",
                    "idfactura"=> "",
                    "porcentaje"=> $df['iva'],
                    "valorImpuestoRetencion"=> "",
                    "consecutivo"=> $NCXml['consecutivo']
                ),
                "notaDescuento"=> "",
                "porcentajedescuento"=> "",
                "posicion"=> "",
                "preciosinimpuestos"=> "",
                "preciototal"=> floatval(str_replace(",","",$df['valorTotal'])),
                "referencia"=> "",
                "seriales"=> "",
                "tamanio"=> "",
                "unidadmedida"=> "",
                "valorimpuestos"=> "",
                "valorunitario"=> floatval(str_replace(",","",$model->tipo_factura == 'FA' || $model->tipo_factura == 'ND' ? $df['subtotal'] : $df['valor']))
            );

        }
        //}
        $NCXml['listaDetalle'] = $detalle_factura;
        if($model->tipo_factura != 'FA') {
            $facturasModificadas[]= array(
                "consecutivo"=> $model->numero,
                "consecutivofacturamodificada"=> $NCXml['consecutivofacturamodificada'],
                "cufefacturamodificada"=> $NCXml['cufe'],
                "fechafacturamodificada"=> "",
                "idfactura"=> "",
                "idfkempresa"=> "",
                "observacion"=> ""
            );
        }
        if($model->tipo_factura == 'FA') {
            $facturasModificadas[] = array(
                "consecutivo" => '',
                "consecutivofacturamodificada" => '',
                "cufefacturamodificada" => "",
                "fechafacturamodificada" => "",
                "idfactura" => "",
                "idfkempresa" => "",
                "observacion" => ""
            );
        }
        $NCXml['listaFacturasModificadas'] = $facturasModificadas;
        $NCXml['listaImpuestos'] = array(
            "activo"=> "",
            "baseimponible"=> "",
            "codigoImpuestoRetencion"=> "01",
            "codigoproducto"=> "",
            "estado"=> "",
            "fechaAlta"=> "",
            "fechaBaja"=> "",
            "fechaMod"=> "",
            "id"=> "",
            "idFkEmpresa"=> "",
            "idUsuarioAlta"=> "",
            "idUsuarioBaja"=> "",
            "idUsuarioMod"=> "",
            "idfactura"=> "",
            "porcentaje"=> "",
            "valorImpuestoRetencion"=> $model->iva,
            "consecutivo"=> $NCXml['consecutivo']
        );
        return $NCXml;
    }
    
    public function sendFile($tokenempresa,$params){
        $variables = array(
            "objConsultaFactura"=> array(
                    'consecutivo' => $params['consecutivo'],
                    'fechafacturacion' => ''.$params['fechafacturacion'],
                    'prefijo' => $params['prefijo'],
                    'idFkEmpresa' => '',
                    'idLote' => '',
                    'tipodocumento' => $params['tipodocumento'],
                    'tokenempresa' => $tokenempresa
                ));
        return $variables;
    }
    
    public function getInscripciones($idInscripcion){
        $sql = "SELECT CONCAT(pro.nombre,'-',p.nombre, ' ',p.apellido) as nombre "
            . "FROM inscripciones i "
            . "LEFT JOIN personas p ON (i.id_persona=p.id) "
            . "LEFT JOIN productos pro ON(i.id_producto=pro.id) "
            . "WHERE i.id=".$idInscripcion;
        $lista=Yii::$app->db->createCommand($sql)->queryOne();

        return $lista['nombre'];
    }
        
    
}