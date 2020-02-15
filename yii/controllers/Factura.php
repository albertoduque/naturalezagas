<?php

/**
 * Clase encargada de gestionar los recibos de caja
 */
class ReciboCaja{

	public $id;
	public $id_factura;
	public $numero;
	public $fecha;
	public $fecha_pago;
	public $valor_descuento;
	public $valor_retencion;
	public $valor_pagado;
        public $valor_subtotal;
        public $valor_iva;
	public $tipo_pago;
	//-----------------------
	public $fACTURA;
	public $resultados = array();

	public function __construct($data){
		$this->id = $data['id'];
		$this->id_factura = $data['id_factura'];
		$this->numero = $data['numero'];
		$this->fecha = $data['fecha'];
		$this->fecha_pago = $data['fecha_pago'];
		$this->valor_descuento = $data['valor_descuento'];
		$this->valor_retencion = $data['valor_retencion'];
		$this->valor_pagado = $data['valor_pagado'];
                $this->valor_subtotal = $data['valor_subtotal'];
                $this->valor_iva = $data['valor_iva'];
		$this->tipo_pago = $data['tipo_pago'];	

		$this->getFactura();	
	}

	/**
	 * Metodo encargado de retornar los datos de la factura
	 * @return Factura Objeto con datos de factura
	 */
	public function getFactura(){
		global $db, $prefix;

		$query = "SELECT * FROM {$prefix}_facturas WHERE id = {$this->id_factura}";

		$smt = $db->prepare($query); 
 		$smt->execute();
		$tmp= $smt->fetchAll();

		$this->fACTURA = new Factura($tmp[0]);

		return $this->fACTURA;
	}

	/**
	 * Metodo encargado de mostra los recibos de caja de una factura
	 * @return array Resultados
	 */
	public function lista_recibos(){
		global $db, $prefix;

		$query = "SELECT * FROM {$prefix}_recibos WHERE id_factura = {$this->id_factura}";
$smt = $db->prepare($query); 
 		$smt->execute();
		$tmp = $smt->fetchAll();

		foreach($tmp as $recibo){
			$recibo['valor_descuento'] = $this->format($recibo['valor_descuento']);
			$recibo['valor_retencion'] = $this->format($recibo['valor_retencion']);
			$recibo['valor_pagado'] = $this->format($recibo['valor_pagado']);
			$this->resultados[] = new ReciboCaja($recibo);
		}

		return $this->resultados;
	}
        
      

	/**
	 * Metodo encargado de guardar un recibo de caja
	 */
	public function save(){
		global $db,$prefix;

		//$this->valor_descuento = Factura::unformat($this->valor_descuento);
                $this->valor_descuento = Factura::unformat($this->valor_descuento);
		$this->valor_retencion = Factura::unformat($this->valor_retencion);
		$this->valor_pagado = Factura::unformat($this->valor_pagado);
                $this->valor_subtotal = Factura::unformat($this->valor_subtotal);
                $this->valor_iva = Factura::unformat($this->valor_iva);

		$query = "INSERT INTO {$prefix}_recibos VALUES(NULL, {$this->id_factura}, {$this->numero}, '{$this->fecha}','{$this->fecha_pago}', {$this->valor_descuento}, {$this->valor_retencion}, {$this->valor_pagado}, '{$this->tipo_pago}', '{$this->valor_subtotal}', '{$this->valor_iva}')";
                
		$db->Execute($query);
               
		$query = "UPDATE {$prefix}_facturas SET estado = '{$this->tipo_pago}' WHERE id = {$this->id_factura}";

		$db->Execute($query);

		if($this->tipo_pago == 'T'){
			$query = "UPDATE {$prefix}_participantes SET estado_pago = 'T' WHERE pago = {$this->id_factura}";

			$db->Execute($query);
		}elseif($this->tipo_pago == 'P'){
			$query = "UPDATE {$prefix}_participantes SET estado_pago = 'R' WHERE pago = {$this->id_factura}";

			$db->Execute($query);
		}
		$this->getFactura();
	}

	public function update(){
		global $db, $prefix;
                
                $this->valor_descuento = Factura::unformat($this->valor_descuento);
		$this->valor_retencion = Factura::unformat($this->valor_retencion);
		$this->valor_pagado = Factura::unformat($this->valor_pagado);
                $this->valor_subtotal = Factura::unformat($this->valor_subtotal);
                $this->valor_iva = Factura::unformat($this->valor_iva);
                
		$query = "UPDATE {$prefix}_recibos SET numero = {$this->numero},fecha = '{$this->fecha}', fecha_pago = '$this->fecha_pago', valor_descuento = {$this->valor_descuento},valor_retencion = {$this->valor_retencion},valor_pagado = {$this->valor_pagado},tipo_pago = '{$this->tipo_pago}',valor_subtotal = '{$this->valor_subtotal}',valor_iva = '{$this->valor_iva}' WHERE id = {$this->id}";
		//echo $query;exit();
		$db->Execute($query);

		$query = "UPDATE {$prefix}_facturas SET estado = '{$this->tipo_pago}' WHERE id = {$this->id_factura}";

		$db->Execute($query);

		if($this->tipo_pago == 'T'){
			$query = "UPDATE {$prefix}_participantes SET estado_pago = 'T' WHERE pago = {$this->id_factura}";

			$db->Execute($query);
		}elseif($this->tipo_pago == 'P'){
			$query = "UPDATE {$prefix}_participantes SET estado_pago = 'R' WHERE pago = {$this->id_factura}";

			$db->Execute($query);
		}
		$this->getFactura();
	}

	/**
	 * Metodo encargado de eliminar un recibo
	 */
	public function delete(){
		global $db, $prefix;

		$db->Execute("DELETE FROM {$prefix}_recibos WHERE id = {$this->id}");

		$query = "SELECT COUNT(*) AS numero_recibos FROM {$prefix}_recibos WHERE id_factura = {$this->id_factura}";

		$numero_recibos = $db->Execute($query)->GetRows();

		if($numero_recibos[0]['numero_recibos'] == 0){

			$query = "UPDATE {$prefix}_facturas SET estado = 'F' WHERE id = {$this->id_factura}";

			$db->Execute($query);

		}elseif($this->tipo_pago == 'T' || $this->tipo_pago == 'P'){
			$query = "UPDATE {$prefix}_facturas SET estado = 'P' WHERE id = {$this->id_factura}";

			$db->Execute($query);

			$query = "UPDATE {$prefix}_participantes SET estado_pago = 'R' WHERE pago = {$this->id_factura}";

			$db->Execute($query);
		}

		$this->getFactura();
	}

	/**
     * Metodo para retornar los tipos de pago
     * @param string $index Indice para retornar su valor default NULL
     * @return array tipos distponibles
     */
    public function getTipoPago($index = NULL){
        $res = array(
            'P'=>'Pago Parcial',
            'T'=>'Pago Total',
         );
        return $index == NULL ? $res : $res[$index];
    }

    /**
	 * Este metodo sera llamado para formatear un numero
	 * @param string $valor
	 * @return string Texto formateado con (.) y (,), Ejemplo 1.000,00 
	 */
	public static function format($valor){

	    return number_format($valor, 0, ',', '.');
	}

}

/**
 * Clase encargada de gestionar las facturas
 */
class Factura {
	
	public $id;
	public $numero;
	public $evento;
	public $identificacion_empresa;
	public $identificacion_persona;
	public $contacto;
	public $fecha;
	public $subtotal;
	public $iva;
	public $total;
	public $estado;
	public $observaciones;
        public $moneda;
	//--------------------
	public $dATOS;
	public $cONTACTO;
	public $productos;
	public $descuentos;
	public $recibos;
	public $inscritos = array();
	//--------------------
	public $total_retencion = 0;
	public $total_anticipo = 0;
	public $total_descuento = 0;
	//--------------------
	public $fecha_inicio;
	public $fecha_fin;
	public $empresa;
	public $persona;

	public function __construct($data,$productos = false,$descuentos = false){
		if(!empty($data)){
			$this->id = $data['id'];
			$this->numero = ''.$data['numero'];
			$this->evento = $data['id_evento'];
			$this->identificacion_empresa = $data['identificacion_empresa'];
			$this->identificacion_persona = $data['identificacion_persona'];
			$this->contacto = $data['id_contacto'];
			$this->fecha = $data['fecha'];
			$this->subtotal = $data['subtotal'];
			$this->iva = $data['iva'];
			$this->total = $data['total'];
			$this->estado = $data['estado'];
                        $this->moneda = $data['eventos_monedas_idMoneda'];
			$this->observaciones = $data['observaciones'];

			$this->getDatos();
			$this->getDatosRecibos();

			if($productos){
				$this->getProductos();
				$this->getInscritos();
			}

			if($descuentos)
				$this->getDescuentos();
		}
	}
	/**
	 * Metodo encargado de listar y filtrar las facturas segun criterios
	 * @return array Resultados de la busqueda
	 */
	public function lista_facturas(){
		global $db,$prefix;
		$lista_facturas = array();

		$query = "SELECT * FROM {$prefix}_facturas  WHERE id_evento = {$this->evento} AND identificacion_empresa <> 0";
		//$query2 = "SELECT t.id as id, t.id_evento as id_evento, t.numero as numero, t.identificacion_persona as identificacion_persona, t.fecha as fecha, t.estado as estado FROM {$prefix}_facturas	AS t  JOIN {$prefix}_participantes  AS p ON t.identificacion_persona = p.identificacion WHERE t.id_evento = {$this->evento}";
		$query2 = "SELECT t.* FROM {$prefix}_facturas	AS t  JOIN {$prefix}_participantes  AS p ON t.identificacion_persona = p.identificacion WHERE t.id_evento = {$this->evento}";

		if($this->fecha_inicio != '' && $this->fecha_fin){
			$query .= " AND (fecha BETWEEN '{$this->fecha_inicio}' AND '{$this->fecha_fin}')";
			$query2 .= " AND (t.fecha  BETWEEN '{$this->fecha_inicio}' AND '{$this->fecha_fin}')";
		}
		if($this->empresa != ''){
			$query .= " AND identificacion_empresa = '{$this->empresa}'";
			$query2 .= " AND t.id = -1";
		}elseif($this->persona != ''){
			$query2 .= " AND (p.nombre LIKE '%{$this->persona}%' OR p.apellido LIKE '%{$this->persona}%')";
			$query .= " AND id = -1";
		}
		if($this->numero != ''){
			$query .= " AND numero LIKE '%{$this->numero}%'";
			$query2 .= " AND t.numero LIKE '%{$this->numero}%'";
		}
		$query .= ' ORDER BY numero';
		$query2 .= ' ORDER BY t.numero';

		
		//echo "<h1>query = ".$query."</h1>";
		//echo "<h1>query2 = ".$query2."</h1>";
		
		$smt = $db->prepare($query);
    		$smt->execute();
    		$query  = $smt->fetchAll();
		$smt = $db->prepare($query2);
    		$smt->execute();
    		$query2  = $smt->fetchAll();

		foreach ($query as $key => $value)
    		$lista_facturas[$value['numero']] = new Factura($value,true);

		foreach ($query2 as $key => $value)
    		$lista_facturas[$value['numero']] = new Factura($value,true);
	
    	ksort($lista_facturas);

    	return $lista_facturas;
	}
	/**
	 * Metodo encargado de retornar la informacion de la empresa o persona
	 * @return array
	 */
	public function getDatos(){
		global $db, $prefix;

		if(($this->identificacion_empresa != '' || $this->identificacion_empresa != 0) && ($this->identificacion_persona == '' ||  $this->identificacion_persona == 0)){
			$query = "SELECT * FROM {$prefix}_empresas WHERE id_evento = {$this->evento} AND identificacion ='{$this->identificacion_empresa}'";
                         //echo $query;
			$smt = $db->prepare($query);
			$smt->execute();
			$this->dATOS = $smt->fetchAll();
			//$this->dATOS = $db->query($query);
			$this->dATOS = $this->dATOS[0];
				
		}elseif(($this->identificacion_persona != '' || $this->identificacion_persona != 0) && ($this->identificacion_empresa == '' ||  $this->identificacion_empresa == 0)){
			$query = "SELECT * FROM {$prefix}_participantes WHERE id_evento = {$this->evento} AND identificacion = {$this->identificacion_persona} ";
			$smt = $db->prepare($query);
    			$smt->execute();
    			$this->dATOS = $smt->fetchAll();
			$this->dATOS = $this->dATOS[0];
				
			
		}

		if($this->contacto != '' || $this->contacto != 0){
			$query = "SELECT * FROM {$prefix}_empresas_facturacion WHERE id = {$this->contacto} ";
			$smt = $db->prepare($query);
			$smt->execute();
			$this->cONTACTO = $smt->fetchAll();
			$this->cONTACTO = $this->cONTACTO[0];
		}

		return $this->dATOS;
	}

        
          /**
	 * Metodo encargado de mostra los recibos de caja de una factura con su numero
	 * @return array Resultados
	 */
	public function lista_recibos_factura(){
		global $db, $prefix;

		$query = "SELECT * FROM {$prefix}_recibos WHERE id_factura = (select id from eventos_facturas where numero ={$this->fACTURA}) ";
		$smt = $db->prepare($query); 
 		$smt->execute();
		$tmp = $smt->fetchAll();
		foreach($tmp  as $recibo){
			$recibo['valor_descuento'] = $this->format($recibo['valor_descuento']);
			$recibo['valor_retencion'] = $this->format($recibo['valor_retencion']);
			$recibo['valor_pagado'] = $this->format($recibo['valor_pagado']);
			$this->resultados[] = new ReciboCaja($recibo);
		}

		return $this->resultados;
	}
        
	/**
	 * Metodo encargado de retornar las sumatorias de los recibos de una factura
	 */
	public function getDatosRecibos(){
		global $db,$prefix;

		if($this->id != ''){
			$query = "SELECT SUM(valor_retencion) AS retencion, SUM(valor_pagado) AS anticipo, SUM(valor_descuento) AS descuento FROM {$prefix}_recibos WHERE id_factura = {$this->id}";
			$smt = $db->prepare($query);
			$smt->execute();
			$tmp = $smt->fetchAll();
			if($tmp){
				$this->total_retencion = $tmp[0]['retencion'] != '' ? $tmp[0]['retencion'] : 0;
				$this->total_anticipo = $tmp[0]['anticipo'] != '' ? $tmp[0]['anticipo'] : 0;
				$this->total_descuento = $tmp[0]['descuento'] != '' ? $tmp[0]['descuento'] : 0;
			}

			$query = "SELECT * FROM {$prefix}_recibos WHERE id_factura = {$this->id}";
			$smt = $db->prepare($query);
			$smt->execute();
			$this->recibos= $smt->fetchAll();
			$total = $this->total;
			foreach ($this->recibos as $key=>$recibo){
				$total -= $recibo['valor_pagado'];
				$total -= $recibo['valor_descuento'];
				$total -= $recibo['valor_retencion'];
				
				$this->recibos[$key]['saldo'] = $total;
			}
				

			return $this->recibos;
			
		}
		
	}

	/**
	 * Metodo encargado de cargar el detalle de productos en una prefactura
	 * @return array productos de la factura
	 */
	public function getProductos(){
		global $db,$prefix;

		if($this->id != ''){
			$query = "SELECT p.nombre as nombre, p.inscripciones as inscripciones, t.cantidad as cantidad, t.valor as valor, t.descuento as descuento, t.valorTotal as valorTotal FROM {$prefix}_detalle_factura as t JOIN {$prefix}_productos as p ON t.id_producto = p.id  WHERE id_factura = {$this->id}";
			$smt = $db->prepare($query);
			$smt->execute();
			$this->productos=$smt->fetchAll();
		}
		
		return $this->productos;
	}

	/**
	 * Metodo encargado de buscar los inscritos q tenga esta factura
	 * @return array inscritos de factura
	 */
	public function getInscritos(){
		global $db,$prefix;

		if($this->id != ''){
			$query = "SELECT * FROM {$prefix}_participantes WHERE pago = {$this->id}";
			$smt = $db->prepare($query);
			$smt->execute();
			$tmp=$smt->fetchAll();
			foreach($tmp as $key=>$inscrito){
				$this->inscritos[$key] = $inscrito['nombre'].' '.$inscrito['apellido'];

				if($inscrito['id_tipo_asistente'] == 26)
					$this->inscritos[$key] .= ' <i>(Inscrito x Descuento)</i>';
			}

			$this->inscritos = implode(' - ', $this->inscritos);
		}
		

		return $this->inscritos;
	}

	/**
	 * Metodo encargado de cargar el detalle de productos en una prefactura
	 * @return array productos de la prefactura
	 */
	public function getDescuentos(){
		global $db,$prefix;

		$query = "SELECT * FROM {$prefix}_descuentos_factura WHERE id_factura = {$this->id}";
		$smt = $db->prepare($query);
		$smt->execute();
		$this->descuentos = $smt->fetchAll();

		return $this->descuentos;
	}
	/**
	 * Metodo encargado de guardar la prefactura en la base de datos
	 */
	public function save(){
		global $db, $prefix;

		$this->iva = $this->unformat($this->iva);
		$query = "INSERT INTO {$prefix}_facturas VALUES(NULL,'{$this->numero}',{$this->evento},'{$this->identificacion_empresa}','{$this->identificacion_persona}','{$this->contacto}','{$this->fecha}',{$this->subtotal},{$this->iva}, {$this->total},'{$this->estado}','{$this->observaciones}','{$this->moneda}')";

                $db->Execute($query);
		
		$id = $db->Execute("SELECT id FROM {$prefix}_facturas WHERE id_evento = {$this->evento} AND numero = '{$this->numero}'")->GetRows();
		$this->id = $id[0]['id'];

		foreach($this->productos as $producto){
			$producto['cantidad'] = $this->unformat($producto['cantidad']);
			$producto['valor'] = $this->unformat($producto['valor']);

			$db->Execute("INSERT INTO {$prefix}_detalle_factura VALUES(NULL,{$this->id},{$producto['producto']},{$producto['cantidad']},{$producto['valor']},{$producto['descuento']},{$producto['valorTotal']})");

			if(isset($producto['participantes'])){
				foreach($producto['participantes'] as $participante)
				$db->Execute("UPDATE {$prefix}_participantes SET pago = {$this->id}, fecha_pago = '{$this->fecha}', factura = '{$this->numero}', fecha_factura = '{$this->fecha}', estado_pago = 'A' WHERE id = {$participante}");
			}

			if(isset($producto['participantes_descuento'])){
				foreach($producto['participantes_descuento'] as $participante)
				$db->Execute("UPDATE {$prefix}_participantes SET pago = {$this->id}, fecha_pago = '{$this->fecha}', factura = '{$this->numero}', fecha_factura = '{$this->fecha}', estado_pago = 'T', id_tipo_asistente = 26 WHERE id = {$participante}");
			}
		}

		/*foreach($this->descuentos as $descuento)
			$db->Execute("INSERT INTO {$prefix}_descuentos_factura VALUES(NULL,{$this->id},{$descuento['id']},{$descuento['valor']})");
		*/
	}

	/**
	 * Metodo encargado de anular las Facturas
	 * @return boolean Indica si se anulo o no
	 */
	public function anular(){
		global $db, $prefix;
		$db->Execute("UPDATE {$prefix}_participantes SET pago = '', fecha_pago = '', factura = '', fecha_factura = '', estado_pago = 'P' WHERE pago = {$this->id}");

		$db->Execute("UPDATE {$prefix}_facturas SET estado = 'A' WHERE id = {$this->id}");
		return true;
	}

	/**
	 * Metodo encargado de eliminar las facturas
	 * @return boolean Indica si se elimino o no
	 */
	public function eliminar(){
		global $db, $prefix;
		$db->Execute("UPDATE {$prefix}_participantes SET pago = '', fecha_pago = '', factura = '', fecha_factura = '', estado_pago = 'P', id_tipo_asistente = 19 WHERE pago = {$this->id}");

		$db->Execute("DELETE FROM {$prefix}_detalle_factura WHERE id_factura = {$this->id}");
		$db->Execute("DELETE FROM {$prefix}_descuentos_factura WHERE id_factura = {$this->id}");
		$db->Execute("DELETE FROM {$prefix}_facturas WHERE id = {$this->id}");
		
		return true;
	}

	/**
     * Metodo para retornar los Estados de la factura
     * @param string $index Indice para retornar su valor default NULL
     * @return array Estados distponibles
     */
    public static function getEstado($index = NULL){
        $res = array(
            'F'=>'Por Cancelar',
            'A'=>'Anulada',
            'P'=>'Pago Parcial',
            'T'=>'Pago Total',
         );
        return $index == NULL ? $res : $res[$index];
    }

    /**
	 * Este metodo sera llamado para desformatear un numero que venga con comas(,) y puntos (.)
	 * @param string $valor
	 * @return string Texto sin comas ni puntos 
	 */
	public static function unformat($valor){
	    $trans = array('.' => '');
	    $trans2 = array(',' => '.');
	    $valorunformat=strtr(strtr($valor, $trans), $trans2);
	    return $valorunformat;
	}
}


class Monedas {
	public $id;
	public $nombre;
	public $simbolo;
  
	public function lista_monedas(){
		global $db, $prefix;
		$query = "SELECT * FROM {$prefix}_monedas";
		$smt = $db->prepare($query);
		$smt->execute();
		$this->resultados= $smt->fetchAll(); 
		return $this->resultados;
	}


}
?>