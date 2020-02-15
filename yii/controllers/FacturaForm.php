<?php

/**
 * Clientes Class
 */
class Clientes {
	public $resultados = array();
	public $evento;
	public $fecha_inicio;
	public $fecha_fin;
	public $empresa;
	public $persona;

	public function lista_clientes(){
		global $db, $prefix;

		$query = "SELECT id , id_evento , identificacion , nombre , fecha_in FROM {$prefix}_empresas AS t WHERE t.id_evento = {$this->evento}";

		$query2 = "SELECT id , id_evento , identificacion , nombre , apellido, fecha_in FROM {$prefix}_participantes AS t WHERE t.id_evento = {$this->evento} AND (id_empresa = 0 OR id_empresa IS NULL)";


		if($this->fecha_inicio != '' && $this->fecha_fin){
			$query .= " AND (fecha_in BETWEEN '{$this->fecha_inicio}' AND '{$this->fecha_fin}')";
			$query2 .= " AND (fecha_in BETWEEN '{$this->fecha_inicio}' AND '{$this->fecha_fin}')";
		}
		if($this->empresa != ''){
			$query .= " AND t.identificacion = '{$this->empresa}'";
			$query2 .= " AND id = -1";
		}elseif($this->persona != ''){
			$query2 .= " AND (nombre LIKE '%{$this->persona}%' OR apellido LIKE '%{$this->persona}%')";
			$query .= " AND id = -1";
		}
		
		$query.= " ORDER BY nombre ASC";
		$query2.= " ORDER BY nombre ASC";
		
		$smt = $db->prepare($query);
 		$smt->execute();
		$query = $smt->fetchAll();

		$smt = $db->prepare($query2);
 		$smt->execute();
		$query2 = $smt->fetchAll();

		//$query = $db->Execute($query)->GetArray();
		//$query2 = $db->Execute($query2)->GetArray();

	    foreach($query as $empresa){
	    	$empresa['tipo'] = 'Empresa';
	    	$this->resultados[] = $empresa;
	    }
	    foreach($query2 as $persona){
	    	$persona['tipo'] = 'Persona';
	    	$this->resultados[] = $persona;
	    }
	    
		return $this->resultados;
	}

}

/**
 * FacturaForm class.
 */
class FacturaForm
{
	private $errors = array();
	public $scenario;
	public $id;
	public $evento;
	public $empresaPersona;
	public $identificacion;
	public $fecha_pago;
	public $subtotal;
	public $iva;
	public $total;
	//--------------------
	public $dATOS;
	public $productos;
	public $descuentos;

	public function __construct($data=array(),$productos = false,$descuentos = false){
		if(!empty($data)){
			$this->id = $data['id'];
			$this->evento = $data['id_evento'];
			$this->empresaPersona = $data['empresaPersona'];
			$this->identificacion = $data['identificacion'];
			$this->fecha_pago = $data['fecha_pago'];
			$this->subtotal = $data['subtotal'];
			$this->iva = $data['iva'];
			$this->total = $data['total'];

			$this->getDatos();

			if($productos)
				$this->getProductos();
			if($descuentos)
				$this->getDescuentos();
		}
	}

	/**
	 * Metodo encargado de retornar la informacion de la empresa o persona
	 * @return array
	 */
	public function getDatos(){
		global $db, $prefix;

		if($this->empresaPersona == 'E')
			$query = "SELECT * FROM {$prefix}_empresas WHERE id_evento = {$this->evento} AND identificacion = {$this->identificacion} ";
		elseif($this->empresaPersona == 'P')
			$query = "SELECT * FROM {$prefix}_participantes WHERE id_evento = {$this->evento} AND identificacion = {$this->identificacion} ";

		$this->dATOS = $db->Execute($query)->GetRows();
		$this->dATOS = $this->dATOS[0];
		
		return $this->dATOS;
	}

	/**
	 * Metodo encargado de cargar el detalle de productos en una prefactura
	 * @return array productos de la prefactura
	 */
	public function getProductos(){
		global $db,$prefix;

		$query = "SELECT * FROM {$prefix}_detalle_prefactura WHERE id_prefactura = {$this->id}";

		$this->productos = $db->Execute($query)->GetArray();

		return $this->productos;
	}

	/**
	 * Metodo encargado de cargar el detalle de productos en una prefactura
	 * @return array productos de la prefactura
	 */
	public function getDescuentos(){
		global $db,$prefix;

		$query = "SELECT * FROM {$prefix}_descuentos_prefactura WHERE id_prefactura = {$this->id}";

		$this->descuentos = $db->Execute($query)->GetArray();

		return $this->descuentos;
	}

	/**
	 * Inicializa el modelo con un atributo
	 */
	public function init(){
		if($this->scenario == 'principal')
			$this->empresaPersona = 'E';
	}

	/**
	 * Metodo encargado de guardar la prefactura en la base de datos
	 */
	public function save(){
		global $db, $prefix;

		$query = "INSERT INTO {$prefix}_prefactura VALUES(NULL,{$this->evento},'{$this->empresaPersona}','{$this->identificacion}','{$this->fecha_pago}',{$this->subtotal},{$this->iva}, {$this->total})";
		$db->Execute($query);

		$id = $db->Execute("SELECT MAX(id) AS id FROM {$prefix}_prefactura")->GetRows();
		$this->id = $id[0]['id'];

		foreach($this->productos as $producto)
			$db->Execute("INSERT INTO {$prefix}_detalle_prefactura VALUES(NULL,{$this->id},{$producto['producto']},{$producto['cantidad']},{$producto['valor']},{$producto['valorTotal']})");

		foreach($this->descuentos as $descuento)
			$db->Execute("INSERT INTO {$prefix}_descuentos_prefactura VALUES(NULL,{$this->id},{$descuento['id']},{$descuento['valor']})");
		
	}

	/**
	 * Metodo encargado de acumular errores de validacion
	 * @param string $attribute atributo del error
	 * @param string $message   Mensaje de error
	 */
	private function addError($attribute,$message){
		$this->errors[get_class($this).'_'.$attribute] = $message;
	}
	/**
	 * Metodo encargado de retornar los errores
	 * @return array Con errores de validacion
	 */
	public function getErrors(){

		return $this->errors;
	}
	/**
	 * Valida que la identificacion este en empresa o en participantes
	 */
	public function validarEmpresa()
	{
		global $db, $prefix; 
		if($this->scenario == 'principal' && $this->identificacion != '')
		{
			
			if($this->empresaPersona == 'E'){
				$query = "SELECT identificacion FROM {$prefix}_empresas WHERE identificacion = {$this->identificacion} AND id_evento = {$this->evento}";
				$empresa = $db->Execute($query); 
				if(!$empresa->GetRows()){
					$this->addError('identificacion','El Número de Indntificación no existe, debe registrarse para generar la factura proforma');
				}

			}elseif($this->empresaPersona == 'P'){
				$query = "SELECT identificacion FROM {$prefix}_participantes WHERE (identificacion = {$this->identificacion} AND id_evento = {$this->evento}) AND (id_empresa IS NULL OR id_empresa = 0)";
				$participante = $db->Execute($query);
				if(!$participante->GetRows()){
					$this->addError('identificacion','El Número de Indentificación no existe, debe registrarse para generar la factura proforma');
				}
			}
				
		}
	}

	/**
	 * Valida que la fecha de pago este entre las fechas del evento
	 */
	public function validarFechaEvento()
	{
		global $db, $prefix;
		if($this->scenario == 'principal' && $this->fecha_pago != '')
		{
			$query = "SELECT fecha_hora_inicio, fecha_hora_fin FROM {$prefix}_eventos WHERE id = {$this->evento}";
			$evento = $db->Execute($query)->GetRows();
			if(!($this->fecha_pago >= $evento[0]['fecha_hora_inicio'] && $this->fecha_pago <= $evento[0]['fecha_hora_fin']))
				$this->addError('fecha_pago','La fecha esta fuera del Rango del Evento');
		}
	}

}