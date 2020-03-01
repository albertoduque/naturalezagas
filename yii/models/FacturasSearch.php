<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Facturas;
use yii\data\ArrayDataProvider;
use  yii\web\Session;
use yii\db\Expression;
/**
 * FacturasSearch represents the model behind the search form about `app\models\Facturas`.
 */

class FacturasSearch extends Facturas
{
    /**
     * @inheritdoc
     */
    public $empresa_nombre,$persona_nombre,$estado_factura,$estado_pago,$producto,$diasTrans,$fecha,$fechaPago;
    public function rules()
    {
        return [
            [['tipo_factura'], 'string'],
            [['id', 'subtotal', 'iva', 'total', 'id_estado_factura', 'id_moneda', 'descuento', 'deleted','trm'], 'integer'],
            [['numero', 'observaciones', 'created_at', 'modified_at','empresa_nombre','persona_nombre','estado_factura','estado_pago','producto','diasTrans','fecha','fechaPago'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    public function getPagos($idFactura=0,$idInscripcion=0){
        $idFactura = $idFactura === NULL ? 0 : $idFactura;
        $where = "dr.id_factura=".$idFactura." AND dr.id_inscripcion=".$idInscripcion;
        $query = (new \yii\db\Query())
                ->select("SUM(dr.valor) as pagos")
                ->from('detalle_recibos dr')
                ->where($where)->one();
        return count($query) > 0 ? $query['pagos'] : 0;
    }
    
    public function getTotal($idFactura=0,$idInscripcion=0){
         $where = "df.id_factura=".$idFactura." AND df.id_inscripcion=".$idInscripcion;
        $query = (new \yii\db\Query())
                ->select("valor,valorTotal,iva")
                ->from('detalle_factura df')
                ->where($where)->one();
        return count($query) > 0 ? $query : 0;
    }
    
     /**
     * @param int $idFactura
     * @param int $idInscripcion
     * @return array|bool|int
     */
    public function getRelacionFactura($idFactura=0){
        $where = "nc.nc_id=".$idFactura;
        $query = (new \yii\db\Query())
            ->select("f.numero,f.id_serie AS serie,f.id as idFactura")
            ->from('relacion_nc_factura nc')
            ->leftJoin("facturas f","nc.factura_id=f.id")
            ->where($where)->one();
		//var_dump(count($query));
        return $query ? $query : 0;
        //return count($query) > 0 ? $query : 0;
    }
	
	 public function getRelacionNC($idFactura=0){
        $where = "nc.factura_id=".$idFactura;
        $query = (new \yii\db\Query())
            ->select("f.numero,f.id_serie AS serie,f.id as idFactura")
            ->from('relacion_nc_factura nc')
            ->leftJoin("facturas f","nc.nc_id=f.id")
            ->where($where)->one();
        return $query || 0;
    }
    
    /**
     * Obtener el valor de productos no facturados 
     * @param type $idFactura
     * @param type $idInscripcion
     * @return type
     */
     public function getTotalInscripciones($idInscripcion=0){
         $where = "i.id=".$idInscripcion;
        $query = (new \yii\db\Query())
                ->select("p.valor,p.valor,p.valor")
                ->from('inscripciones i')
                ->leftJoin("productos p","p.id=i.id_producto")
                ->where($where)->one();
        return count($query) > 0 ? $query : 0;
    }
    
    public function getFechaPago($idFactura=0,$idInscripcion=0){
        $idFactura = $idFactura === NULL ? 0 : $idFactura;
          $where = "dr.id_factura=".$idFactura." && dr.id_inscripcion=".$idInscripcion;
          $query = (new \yii\db\Query())
                ->select("MAX(dr.fecha_pago) as fecha_pago")
                ->from('detalle_recibos dr')
                ->where($where)->one();
        return count($query) > 0 || $query['fecha_pago']!==NULL ? $query['fecha_pago'] : 0;
    }
    
    public function FormatoFechas($fecha){
        $dia = substr($fecha, 0, 2);
        $mes  = substr($fecha, 3, 2);
        $ano = substr($fecha, -4);
        // fechal final realizada el cambio de formato a las fechas europeas5
        $fecha = $ano.'-'.$mes.'-'.$dia;
   
    return $fecha;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $evento = \app\models\Eventos::findOne($event_id);
        $year = explode("-",$evento['fecha_hora_inicio']) ;
        //var_dump($params['FacturasSearch']['empresa_nombre']);die;
        $where1 = "i.deleted=0 ";
        $where2 = "df.id_inscripcion is NULL";
        if(isset($params['FacturasSearch']['empresa_nombre']) && $params['FacturasSearch']['empresa_nombre'])
        {
            $where1 .= " AND e.nombre like '%".$params['FacturasSearch']['empresa_nombre']."%'";
            $where2 .= " AND e.nombre like '%".$params['FacturasSearch']['empresa_nombre']."%'";
        }
        if(isset($params['FacturasSearch']['persona_nombre']) && $params['FacturasSearch']['persona_nombre'])
        {
            $where1 .= " AND p.nombre like '%".$params['FacturasSearch']['persona_nombre']."%'";
            $where2 .= " AND p.nombre like '%".$params['FacturasSearch']['persona_nombre']."%'";
        }
        if(isset($params['FacturasSearch']['producto']) && $params['FacturasSearch']['producto'])
        {
            $where1 .= " AND pr.nombre like '%".$params['FacturasSearch']['producto']."%'";
            $where2 .= " AND pr.nombre like '%".$params['FacturasSearch']['producto']."%'";
        }
        if(isset($params['FacturasSearch']['estado_pago']))
        {
            if($params['FacturasSearch']['estado_pago']==='0')
            {
               $where2 .= " AND f.id is NULL";
                // para mostrar con filtro de estado de pago con nc o nd
                if($params['FacturasSearch']['estado_factura'] !=='4')
                    $where1 .= " AND f.id is NULL";
               //$where1 .=  " AND (i.is_facturado =".$params['FacturasSearch']['estado_pago']." && i.estado=1)" ;
            }
        }
        if(isset($params['FacturasSearch']['estado_factura']))
        {
            if($params['FacturasSearch']['estado_factura']!=='')
            {
                $where2 .= " AND f.id is NULL";
                $where1 .=  " AND (f.id_estado_factura =".$params['FacturasSearch']['estado_factura']." OR df.id_estado_factura=".$params['FacturasSearch']['estado_factura'].")" ;
            }
        }
        if(isset($params['FacturasSearch']['numero']) && $params['FacturasSearch']['numero'])
        {
            $where1 .= " AND f.numero like '%".$params['FacturasSearch']['numero']."%'";
            $where2 .= " AND f.numero like '%".$params['FacturasSearch']['numero']."%'";
        }
        if(isset($params['FacturasSearch']['tipo_factura']) && $params['FacturasSearch']['tipo_factura'])
        {
            $where1 .= " AND f.tipo_factura like '%".$params['FacturasSearch']['tipo_factura']."%'";
            $where2 .= " AND f.tipo_factura like '%".$params['FacturasSearch']['tipo_factura']."%'";
        }
        if(isset($params['FacturasSearch']['diasTrans']) && $params['FacturasSearch']['diasTrans'])
        {
            $where1 .= " AND DATEDIFF(CURDATE(),f.fecha) = ".$params['FacturasSearch']['diasTrans'];
            $where2 .= " AND DATEDIFF(CURDATE(),f.fecha) = ".$params['FacturasSearch']['diasTrans'];
        }
        if(isset($params['FacturasSearch']['fecha']) && $params['FacturasSearch']['fecha'])
        {
            $fecha = $this->FormatoFechas($params['FacturasSearch']['fecha']);
            $where1 .= " AND f.fecha = '".$fecha."'";
            $where2 .= " AND f.fecha = '".$fecha."'";
        }
        if($event_id)
        {
            $where2 .= " AND pr.id_evento=".$event_id;
        }
        
        $where1 .= " AND ta.facturable='SI' ";
        $where1 .= " AND (i.is_presence = 1 OR (i.is_presence = 0 AND f.id > 0)) ";
        //Obtiene todas las facturas de los produtos no los inscritos
        $sql = "SELECT f.id as idFactura,DATEDIFF(CURDATE(),f.fecha) as diasTrans,f.numero,f.fecha as fecha,p.nombre as persona,p.apellido,e.nombre as empresa,f.id_persona,f.id_empresa,f.subtotal,f.iva,f.total,ef.nombre as estado,pr.nombre as producto,df.id_estado_factura,(SELECT SUM(dr.valor) FROM detalle_recibos dr WHERE dr.id_factura=idFactura)  as pagos,MAX(dr.fecha_pago) as fecha_pago,df.id_inscripcion,ef.deleted as estadoInscripcion,
                ef.deleted as is_facturado,ef.deleted as id_estado_factura_inscrito,ef.deleted as is_presence,f.tipo_factura,case WHEN f.id_serie = 1 THEN 'FENT' WHEN f.id_serie = 2 THEN 'CONT' WHEN f.id_serie = 3 THEN 'NCNT' ELSE 'FENT' END AS serie
                FROM
                  facturas f
                LEFT JOIN
                  detalle_factura df ON(df.id_factura= f.id)
                LEFT JOIN
                  personas p ON(f.id_persona=p.id)
                LEFT JOIN
                    tipo_asistentes ta ON(ta.id=p.id_tipo_asistente)
                LEFT JOIN
                  empresas e ON(f.id_empresa=e.id)
                LEFT JOIN
                  estados_factura ef ON(df.id_estado_factura=ef.id)
                LEFT JOIN
                  productos pr ON(df.id_producto=pr.id)
                LEFT JOIN
                  detalle_recibos dr ON(f.id=dr.id_factura)
                WHERE
                  ".$where2." 
                GROUP BY f.id";
    
        $query2=Yii::$app->db->createCommand($sql)->queryAll();
        
        $where11 = $where1;
        // para mostrar con filtro de estado de pago con nc o nd
       if(isset($params['FacturasSearch']['estado_factura'])) {
            if ($params['FacturasSearch']['estado_factura'] !== '4') {
                $where1 .= " AND f.id is NULL ";
            }
        }
        else
        {
            $where1 .= " AND f.id is NULL ";
        }
            
        $sql = "SELECT f.id as idFactura,DATEDIFF(CURDATE(),f.fecha) as diasTrans,f.numero,f.fecha as fecha,p.nombre as persona,p.apellido,e.nombre as empresa,i.id_persona,i.id_empresa,f.subtotal,f.iva,f.total,ef.nombre as estado,pr.nombre as producto,"
                        . "df.id_estado_factura as id_estado_factura,i.id as id_inscripcion,i.estado as estadoInscripcion,i.is_facturado,i.id_estado_factura as id_estado_factura_inscrito,i.is_presence,f.tipo_factura,case WHEN f.id_serie = 1 THEN 'FENT' WHEN f.id_serie = 2 THEN 'CONT' WHEN f.id_serie = 3 THEN 'NCNT' ELSE 'FENT' END AS serie
                FROM
                  inscripciones i
                LEFT JOIN
                  detalle_factura df ON(df.id_inscripcion= i.id)
                LEFT JOIN
                  facturas f ON(df.id_factura= f.id)
                LEFT JOIN
                  personas p ON(i.id_persona=p.id)
                LEFT JOIN
                    tipo_asistentes ta ON(ta.id=p.id_tipo_asistente)
                LEFT JOIN
                  empresas e ON(i.id_empresa=e.id )
                LEFT JOIN
                  estados_factura ef ON(i.id_estado_factura=ef.id)
                LEFT JOIN
                  productos pr ON(df.id_producto=pr.id)
                WHERE
                  ".$where1." 
                 AND i.estado > 0  AND (p.id_evento=".$event_id." OR e.id_evento =".$event_id.")"; 
        $query1=Yii::$app->db->createCommand($sql)->queryAll();
       
        if(isset($params['FacturasSearch']['estado_factura']))
        {
            if($params['FacturasSearch']['estado_factura']!=='')
            {
                $where11 .=  " AND df.id_estado_factura=".$params['FacturasSearch']['estado_factura'] ;
            }
        }
        
        $where11 .= " AND pr.id_evento=".$event_id;
        //Con facturas
        $sql = "SELECT f.id as idFactura,DATEDIFF(CURDATE(),f.fecha) as diasTrans,f.numero,f.fecha as fecha,p.nombre as persona,p.apellido,e.nombre as empresa,i.id_persona,i.id_empresa,f.subtotal,f.iva,f.total,ef.nombre as estado,pr.nombre as producto,"
                        . "df.id_estado_factura,i.id as id_inscripcion,i.estado as estadoInscripcion,i.is_facturado,i.id_estado_factura as id_estado_factura_inscrito,i.is_presence,f.tipo_factura,case WHEN f.id_serie = 1 THEN 'FENT' WHEN f.id_serie = 2 THEN 'CONT' WHEN f.id_serie = 3 THEN 'NCNT' ELSE 'FENT' END AS serie
            
                FROM
                  inscripciones i
                LEFT JOIN
                  detalle_factura df ON(df.id_inscripcion= i.id)
                LEFT JOIN
                  facturas f ON(df.id_factura= f.id)  
                LEFT JOIN
                  personas p ON(i.id_persona=p.id)
                LEFT JOIN
                    tipo_asistentes ta ON(ta.id=p.id_tipo_asistente)
                LEFT JOIN
                  empresas e ON(i.id_empresa=e.id )
                LEFT JOIN
                  estados_factura ef ON(df.id_estado_factura=ef.id)
                LEFT JOIN
                  productos pr ON(df.id_producto=pr.id)
                WHERE
                  ".$where11." 
                 AND i.estado > 0";
        $query11=Yii::$app->db->createCommand($sql)->queryAll();
        
        // var_dump($sql);die;//->createCommand()->getRawSql()
        $data = array();
        $d = array("valor"=>1580000,"iva"=>300200,"valorTotal"=>1880200);
        $i=0;
        $fecha = 0;
        if(isset($params['FacturasSearch']['fechaPago']) && $params['FacturasSearch']['fechaPago']!="")
        {
            $fecha = $this->FormatoFechas($params['FacturasSearch']['fechaPago']);
        }
        if(!isset($params['FacturasSearch']['estado_pago']) || (isset($params['FacturasSearch']['estado_pago']) && $params['FacturasSearch']['estado_pago']!=1)){
            foreach ($query1 as $inscritos)
            {
                $fechaPago = $this->getFechaPago($inscritos['idFactura'],$inscritos['id_inscripcion']);
                if($fecha>0)
                {
                    if($fechaPago==$fecha)
                    {
                        $data[$i]['idFactura']=$inscritos['idFactura'];
                        $data[$i]['diasTrans']=$inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                        $data[$i]['numero']=$inscritos['numero'];
                        $data[$i]['fecha']=$inscritos['fecha'];
                        $data[$i]['persona']=$inscritos['persona'];
                        $data[$i]['apellido']=$inscritos['apellido'];
                        $data[$i]['empresa']=$inscritos['empresa'];
                        $data[$i]['id_persona']=$inscritos['id_persona'];
                        $data[$i]['id_empresa']=$inscritos['id_empresa'];
                        $pagos = $inscritos['idFactura'] ? $this->getTotal($inscritos['idFactura'],$inscritos['id_inscripcion']) : $this->getTotalInscripciones($inscritos['id_inscripcion']);
                        $data[$i]['subtotal']=$pagos['valor'];
                        $data[$i]['iva']=$pagos['valor'] * 0.19;
                        $data[$i]['total']=$data[$i]['iva'] + $pagos['valor'] ;
                        $data[$i]['estado']=$inscritos['estado'];
                        $data[$i]['producto']=$inscritos['producto'] ? $inscritos['producto'] : 'INSCRIPCION CONGRESO NATURGAS '.$year[0];
                        $data[$i]['id_estado_factura']=$inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                        $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                        $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                        $data[$i]['is_facturado']=$inscritos['is_facturado'];
                        $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                        $data[$i]['is_presence']=$inscritos['is_presence'];
                        $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura'],$inscritos['id_inscripcion']);
                        $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                        $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                        $data[$i]['serie']=$inscritos['serie'];
						if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
						}	
						if ($inscritos['serie']== 'NCNT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
						}	
                        $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                        $i++;
                    }
                }
                if($fecha==0)
                {
                    $data[$i]['idFactura']=$inscritos['idFactura'];
                    $data[$i]['diasTrans']= $inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                    $data[$i]['numero']=$inscritos['numero'];
                    $data[$i]['fecha']=$inscritos['fecha'];
                    $data[$i]['persona']=$inscritos['persona'];
                    $data[$i]['apellido']=$inscritos['apellido'];
                    $data[$i]['empresa']=$inscritos['empresa'];
                    $data[$i]['id_persona']=$inscritos['id_persona'];
                    $data[$i]['id_empresa']=$inscritos['id_empresa'];
                    $pagos = $inscritos['idFactura'] ? $this->getTotal($inscritos['idFactura'],$inscritos['id_inscripcion']) : $this->getTotalInscripciones($inscritos['id_inscripcion']);
                    $data[$i]['subtotal']=$pagos['valor'];
                    $data[$i]['iva']=$pagos['valor'] * 0.19;
                    $data[$i]['total']=$data[$i]['iva'] + $pagos['valor'] ;
                    $data[$i]['estado']=$inscritos['estado'];
                    $data[$i]['producto']=$inscritos['producto'] ? $inscritos['producto'] : 'INSCRIPCION CONGRESO NATURGAS '.$year[0];
                    $data[$i]['id_estado_factura']=$inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                    $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                    $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                    $data[$i]['is_facturado']=$inscritos['is_facturado'];
                    $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                    $data[$i]['is_presence']=$inscritos['is_presence'];
                    $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura'],$inscritos['id_inscripcion']);
                    $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                    $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                    $data[$i]['serie']=$inscritos['serie'];
                    if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
						$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
					}	
					if ($inscritos['serie']== 'NCNT') {
						$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
					}	
                    $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                    $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                    $i++;
                }
            }
        }
        
        foreach ($query11 as $inscritos)
        {
            $fechaPago = $this->getFechaPago($inscritos['idFactura'],$inscritos['id_inscripcion']);
           
                if($fecha>0)
                {
                    if($fechaPago==$fecha)
                    {
                        $data[$i]['idFactura']=$inscritos['idFactura'];
                        $data[$i]['diasTrans']=$inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                        $data[$i]['numero']=$inscritos['numero'];
                        $data[$i]['fecha']=$inscritos['fecha'];
                        $data[$i]['persona']=$inscritos['persona'];
                        $data[$i]['apellido']=$inscritos['apellido'];
                        $data[$i]['empresa']=$inscritos['empresa'];
                        $data[$i]['id_persona']=$inscritos['id_persona'];
                        $data[$i]['id_empresa']=$inscritos['id_empresa'];
                        $pagos = $inscritos['idFactura'] ? $this->getTotal($inscritos['idFactura'],$inscritos['id_inscripcion']) : $this->getTotalInscripciones($inscritos['id_inscripcion']);
                        $data[$i]['subtotal']=$pagos['valor'];
                        $data[$i]['iva']=$pagos['valor'] * 0.19;
                        $data[$i]['total']=$data[$i]['iva'] + $pagos['valor'] ;
                        $data[$i]['estado']=$inscritos['estado'];
                        $data[$i]['producto']=$inscritos['producto'] ? $inscritos['producto'] : 'INSCRIPCION CONGRESO NATURGAS '.$year[0];
                        $data[$i]['id_estado_factura']=$inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                        $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                        $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                        $data[$i]['is_facturado']=$inscritos['is_facturado'];
                        $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                        $data[$i]['is_presence']=$inscritos['is_presence'];
                        $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura'],$inscritos['id_inscripcion']);
                        $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                        $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                        $data[$i]['serie']=$inscritos['serie'];
                        if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
						}	
						if ($inscritos['serie']== 'NCNT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
						}	
                        $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                        $i++; 
                    }
                }
                if($fecha==0)
                {        
                    $data[$i]['idFactura']=$inscritos['idFactura'];
                    $data[$i]['diasTrans']=$inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                    $data[$i]['numero']=$inscritos['numero'];
                    $data[$i]['fecha']=$inscritos['fecha'];
                    $data[$i]['persona']=$inscritos['persona'];
                    $data[$i]['apellido']=$inscritos['apellido'];
                    $data[$i]['empresa']=$inscritos['empresa'];
                    $data[$i]['id_persona']=$inscritos['id_persona'];
                    $data[$i]['id_empresa']=$inscritos['id_empresa'];
                    $pagos = $inscritos['idFactura'] ? $this->getTotal($inscritos['idFactura'],$inscritos['id_inscripcion']) : $this->getTotalInscripciones($inscritos['id_inscripcion']);
                    $data[$i]['subtotal']=$pagos['valor'];
                    $data[$i]['iva']=$pagos['valor'] * 0.19;
                    $data[$i]['total']=$data[$i]['iva'] + $pagos['valor'] ;
                    $data[$i]['estado']=$inscritos['estado'];
                    $data[$i]['producto']=$inscritos['producto'] ? $inscritos['producto'] : 'INSCRIPCION CONGRESO NATURGAS '.$year[0];
                    $data[$i]['id_estado_factura']=$inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                    $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                    $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                    $data[$i]['is_facturado']=$inscritos['is_facturado'];
                    $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                    $data[$i]['is_presence']=$inscritos['is_presence'];
                    $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura'],$inscritos['id_inscripcion']);
                    $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                    $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                    $data[$i]['serie']=$inscritos['serie'];
                    if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
						$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
					}	
					if ($inscritos['serie']== 'NCNT') {
						$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
					}	
                        $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                    $i++; 
                }
        }
        
        foreach ($query2 as $inscritos)
        {
            $fechaPago = $this->getFechaPago($inscritos['idFactura']);
            if($fecha>0)
            {
                if($fechaPago==$fecha)
                {
                    $data[$i]['idFactura']=$inscritos['idFactura'];
                    $data[$i]['diasTrans']=$inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                    $data[$i]['numero']=$inscritos['numero'];
                    $data[$i]['fecha']=$inscritos['fecha'];
                    $data[$i]['persona']=$inscritos['persona'];
                    $data[$i]['apellido']=$inscritos['apellido'];
                    $data[$i]['empresa']=$inscritos['empresa'];
                    $data[$i]['id_persona']=$inscritos['id_persona'];
                    $data[$i]['id_empresa']=$inscritos['id_empresa'];
                    $data[$i]['subtotal']=$inscritos['subtotal'];
                    $data[$i]['iva']=$inscritos['iva'];
                    $data[$i]['total']=$inscritos['total'];
                    $data[$i]['estado']=$inscritos['estado'];
                    $data[$i]['producto']=$inscritos['producto'];
                    $data[$i]['iva']=$inscritos['iva'];
                    $data[$i]['id_estado_factura']= $inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                    $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                    $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                    $data[$i]['is_facturado']=$inscritos['is_facturado'];
                    $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                    $data[$i]['is_presence']=$inscritos['is_presence'];
                    $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura']);
                    $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                    $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                    $data[$i]['serie']=$inscritos['serie'];
                    if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
						}	
						if ($inscritos['serie']== 'NCNT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
						}	
                        $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                    $i++;
                }
            }
            if($fecha==0)
            {
                $data[$i]['idFactura']=$inscritos['idFactura'];
                $data[$i]['diasTrans']=$inscritos['id_estado_factura'] == 3 ? 0 : $inscritos['diasTrans'];
                $data[$i]['numero']=$inscritos['numero'];
                $data[$i]['fecha']=$inscritos['fecha'];
                $data[$i]['persona']=$inscritos['persona'];
                $data[$i]['apellido']=$inscritos['apellido'];
                $data[$i]['empresa']=$inscritos['empresa'];
                $data[$i]['id_persona']=$inscritos['id_persona'];
                $data[$i]['id_empresa']=$inscritos['id_empresa'];
                $data[$i]['subtotal']=$inscritos['subtotal'];
                $data[$i]['iva']=$inscritos['iva'];
                $data[$i]['total']=$inscritos['total'];
                $data[$i]['estado']=$inscritos['estado'];
                $data[$i]['producto']=$inscritos['producto'];
                $data[$i]['iva']=$inscritos['iva'];
                $data[$i]['id_estado_factura']=$inscritos['serie']== 'NCNT' ? 4 : $inscritos['id_estado_factura'];
                $data[$i]['id_inscripcion']=$inscritos['id_inscripcion'];
                $data[$i]['estadoInscripcion']=$inscritos['estadoInscripcion'];
                $data[$i]['is_facturado']=$inscritos['is_facturado'];
                $data[$i]['id_estado_factura_inscrito']=$inscritos['id_estado_factura_inscrito'];
                $data[$i]['is_presence']=$inscritos['is_presence'];
                $data[$i]['pagos']=  $this->getPagos($inscritos['idFactura']);
                $data[$i]['fecha_pago']=$fechaPago == NULL ? '' : date_format(date_create($fechaPago),"d/m/Y");
                $data[$i]['tipo_factura']=$inscritos['tipo_factura'];
                $data[$i]['serie']=$inscritos['serie'];     
                if ($inscritos['serie']== 'FENT' || $inscritos['serie']== 'CONT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionNC($inscritos['idFactura']) : '';
						}	
						if ($inscritos['serie']== 'NCNT') {
							$relacion_nc = $inscritos['idFactura'] ? $this->getRelacionFactura($inscritos['idFactura']) : '';
						}	
                        $data[$i]['relacion_nc']= $relacion_nc ? $relacion_nc['numero'] : '';
                        $data[$i]['id_nc']= $relacion_nc ? $relacion_nc['idFactura'] : '';
                        if($relacion_nc)
                        {
                            switch ($relacion_nc['serie']) {
                                case 1:
                                    $serie="FENT";
                                    break;
                                case 2:
                                    $serie="CONT";
                                    break;
                                case 3:
                                    $serie="NCNT";
                                    break;
                                default:
                                    $serie="";
                            }
                        }
                        $data[$i]['serie_nc']= $relacion_nc ? $serie : '';
                $i++;
            }
        }
        
        //var_dump($data);
        
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 60,
            ],
            'sort' => [
                'attributes' => ['empresa','numero'],
            ],
        ]);
        $provider->sort->attributes["empresa_nombre"]["asc"] = ["empresa" => SORT_ASC];
        $provider->sort->attributes["empresa_nombre"]["desc"] = ["empresa" => SORT_DESC];
        $provider->sort->attributes["empresa_nombre"]["label"] = "Empresa"; 
        $provider->sort->attributes["persona_nombre"]["asc"] = ["persona" => SORT_ASC];
        $provider->sort->attributes["persona_nombre"]["desc"] = ["persona" => SORT_DESC];
        $provider->sort->attributes["persona_nombre"]["label"] = "Participante"; 
        $provider->sort->attributes["producto"]["asc"] = ["producto" => SORT_ASC];
        $provider->sort->attributes["producto"]["desc"] = ["producto" => SORT_DESC];
        $provider->sort->attributes["producto"]["label"] = "Producto"; 
        $provider->sort->attributes["numero"]["asc"] = ["numero" => SORT_ASC];
        $provider->sort->attributes["numero"]["desc"] = ["numero" => SORT_DESC];
        $provider->sort->attributes["numero"]["label"] = "Numero"; 
        $this->load($params); 
       // $this->addCondition($query, 'empresa_nombre',true);
         
        return $provider; 
/*         
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->sort->attributes["empresa_nombre"]["asc"] = ["empresa" => SORT_ASC];
        $dataProvider->sort->attributes["empresa_nombre"]["desc"] = ["empresa" => SORT_DESC];
        $dataProvider->sort->attributes["empresa_nombre"]["label"] = "Empresa";
        
        $dataProvider->sort->attributes["persona_nombre"]["asc"] = ["persona" => SORT_ASC];
        $dataProvider->sort->attributes["persona_nombre"]["desc"] = ["persona" => SORT_DESC];
        $dataProvider->sort->attributes["persona_nombre"]["label"] = "Persona";
        
        $dataProvider->sort->attributes["estado_factura"]["asc"] = ["estado" => SORT_ASC];
        $dataProvider->sort->attributes["estado_factura"]["desc"] = ["estado" => SORT_DESC];
        $dataProvider->sort->attributes["estado_factura"]["label"] = "Estado";
        
         $dataProvider->sort->attributes["estado_pago"]["asc"] = ["inscripciones.is_facturado" => SORT_ASC];
        $dataProvider->sort->attributes["estado_pago"]["desc"] = ["inscripciones.is_facturado" => SORT_DESC];
        $dataProvider->sort->attributes["estado_pago"]["label"] = "Estado PAgo";
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
         $this->addCondition($query, 'empresa_nombre',true);
         $this->addCondition($query, 'persona_nombre',true);
         $this->addCondition($query, 'estado_factura');
         $this->addCondition($query, 'estado_pago');
        // grid filtering conditions
        $query->andFilterWhere([
            'facturas.id' => $this->id,
            'subtotal' => $this->subtotal,
            'iva' => $this->iva,
            'total' => $this->total,
            'id_estado_factura' => $this->id_estado_factura,
            'id_moneda' => $this->id_moneda,
            'descuento' => $this->descuento,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'numero', $this->numero])
            ->andFilterWhere(['like', 'observaciones', $this->observaciones]);

        return $dataProvider;*/
    }
    
     protected function addCondition($query, $attribute, $partialMatch = false) {
        $value = $this->$attribute;
        if (trim($value) === '') {
            return;
        }
        
        if ($attribute == "estado_pago") {
            $attribute = "is_facturado";
        }
         if ($attribute == "estado_factura") {
            $attribute = "id_estado_factura";
        }
        if ($attribute == "empresa_nombre") {
            $attribute = "empresa";//nombre de la tabla del join
        }
         if ($attribute == "persona_nombre") {
            $attribute = "persona";//nombre de la tabla del join
        }
       
        if($partialMatch){
                $query->andWhere(['like', $attribute, $value]);
        } else {
                $query->andWhere([$attribute => $value]);
        }
        
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchTransmision($params)
    {
        
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $query = \app\models\Facturas::find()->where(['=','cufe',''])->andWhere(['=','id_evento',$event_id])->andWhere(['=','tipo_factura','FA'])->orderBy('id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'numero', $this->numero])
              ->andFilterWhere(['like', 'tipo_factura', $this->tipo_factura]);

        // grid filtering conditions
       /* $query->andFilterWhere([
            'id' => $this->id,
            'id_cargo' => $this->id_cargo,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'movil', $this->movil])
            ->andFilterWhere(['like', 'correo', $this->correo]);
*/
        return $dataProvider;
    }
}
