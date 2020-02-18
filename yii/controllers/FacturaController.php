<?php

namespace app\controllers;

use app\models\Parametros;
use app\models\RelacionNcFactura;
use Yii;
use app\models\Facturas;
use app\models\FacturasSearch;
use app\models\InscripcionSearch;
use app\models\DetalleFactura;
use app\models\Monedas;
use app\models\Contactos;
//use app\models\TipoIdentificacion;
use app\components\EnLetras;
use app\models\Inscripciones;
use app\models\Impuestos;
use app\models\MedioPago;
use app\models\TipoNota;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\helpers\Url;
use mPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use  yii\db\Query;
use  yii\web\Session;
use yii\web\ForbiddenHttpException;

use SoapClient;
//use app\controllers\ClientDispapelesApi;
require_once 'ClientDispapelesApi.class.php';
/**
 * FacturaController implements the CRUD actions for Facturas model.
 */
class FacturaController extends Controller
{
    public $session;
    public $isUserApproved;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function init(){
        $this->session = Yii::$app->session;
        $this->isUserApproved = $this->session->get( 'isUserApproved');
    }
    
     public function beforeAction($action){
        // Check only when the user is logged in
        if ( !Yii::$app->user->isGuest)  {
            if ( Yii::$app->session->get('userSessionTimeout') < time() ) {
                // timeout
                Yii::$app->user->logout();
                $this->redirect(array('/site/login'));  //
            } else {
                Yii::$app->session->set('userSessionTimeout', time() + Yii::$app->params['sessionTimeoutSeconds']) ;
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Lists all Facturas models.
     * @return mixed
     */ 
    public function actionIndex()
    {
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
            $searchModel = new FacturasSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else{
            throw new ForbiddenHttpException;
        }
    }


    public function actionTransmision()
    {
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
            $searchModel = new FacturasSearch();
            $dataProvider = $searchModel->searchTransmision(Yii::$app->request->queryParams);

            return $this->render('facturas-transmitidas', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else{
            throw new ForbiddenHttpException;
        }
    }

    public function actionCountTransmision()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
          /*  $facturas= Facturas::find()
                ->where([ 'cufe'=> ''])
                ->andWhere(['tipo_factura' => 'FA'])
                ->andWhere(['>','numero' , 900000])
                ->orderBy(['numero' => SORT_ASC])
                ->limit(10)->all();*/
                
            $numeroFactura = \Yii::$app->params['consecutivoFactura']+1;
            $numeroContigencia=\Yii::$app->params['consecutivoFacturaContingencia'] + 1;
            $mensajeFactura = 0;
            $mensajeContingencia = 0;
            $informacionEmpresa = $this->findModelInformacionEmpresa();
            if($numeroFactura>=$informacionEmpresa->desde_factura && $numeroFactura<=$informacionEmpresa->hasta_factura){
                $mensajeFactura = 1;
            }
            if($numeroContigencia>=$informacionEmpresa->desde_contingencia && $numeroContigencia<=$informacionEmpresa->hasta_contingencia){
                $mensajeContingencia = 1;
            }
            // return count($facturas);
             
            return ['respuesta'=>1,'mensajeFactura' => $mensajeFactura,'mensajeContingencia'=>$mensajeContingencia];
        }
         return ['respuesta'=>0,'mensajeFactura' => $mensajeFactura,'mensajeContingencia'=>$mensajeContingencia];
    }
    
    public function getTipoIdentification($model)
    {
        if($model->id_empresa>0) {
            $sql = "SELECT t.is_check_digit FROM empresas e INNER JOIN tipo_identificacion t ON(e.id_tipo_identificacion=t.id) WHERE e.id=" . $model->id_empresa;
        }else{
            $sql = "SELECT t.is_check_digit FROM personas e INNER JOIN tipo_identificacion t ON(e.id_tipo_identificacion=t.id) WHERE e.id=" . $model->id_persona;
        }
        $facturaInfo = Yii::$app->db->createCommand($sql)->queryOne();
        
        return $facturaInfo['is_check_digit'] || 0;
    }

    public function actionTransmitirFactura()
    {
        $facturas= Facturas::find()
            ->where([ 'cufe'=> ''])
            ->andWhere(['tipo_factura' => 'FA'])
            ->andWhere(['>','numero' , 0])
            ->orderBy(['numero' => SORT_ASC])
            ->limit(10)->all();//all()->
        
        foreach ($facturas as $factura)
        {
            $sql = "SELECT f.numero,f.fecha_transmision,f.tipo_factura,f.fecha FROM facturas f WHERE id=" . $factura->id;
            $facturaInfo = Yii::$app->db->createCommand($sql)->queryOne();
            if ($facturaInfo) {
                if($facturaInfo['fecha_transmision'])
                    $fecha = date("Y-m-d", strtotime($facturaInfo['fecha_transmision']));
                else if($facturaInfo['fecha'])
                    $fecha = date("Y-m-d", strtotime($facturaInfo['fecha']));
                $params['consecutivo'] = $facturaInfo['numero'];
                $params['fechafacturacion'] = $fecha;
                $params['tipodocumento'] = $factura['tipo_factura'] == 'FA' ? 1 : 2;
                $params['prefijo'] = "FENT";
            }
            $facturaWsdl = new FacturaWsdl();
            $xmlInvoice = $facturaWsdl->sendFile('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
            $objClientDispapelesApis = new ClientDispapelesApi();
            //$response = $objClientDispapelesApis->consultarPdfFactura($xmlInvoice);
           // var_dump($response->return);
            //if ($response->return->consecutivo) {
            if($factura->id==946){
                $detalle_factura = $factura->detalleFacturas;
                $model = $factura;
                $model->direccion = $model->idEmpresa->direccion;
                $model->telefonoContacto = $model->idEmpresa->telefono;
                $model->identificacion = $model->idEmpresa->identificacion;
                $model->tipoDocumento= 1;
            
                try {
                
                    $informacionEmpresa = $this->findModelInformacionEmpresa();
                    //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                    $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                    $params = $facturaWsdl->loadSF($model, $detalle_factura,$informacionEmpresa);
                    $xmlInvoice = $facturaWsdl->createFactura('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
                    $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
                    $modelFactura = $this->findModel($model->id);
                    $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                    $modelFactura->respuesta = json_encode((array)$response->return);
                    if ($response->return->mensaje == 'OK') {
                        $modelFactura->cufe = $response->return->cufe;
                        $modelFactura->fecha_transmision = $response->return->fechaFactura;
                        $modelFactura->save(false);
                    }
                    else{
                        var_dump($response);die;
                    }
                }catch (\SoapFault $e){
                    var_dump($e);die;
                }
            }
            //var_dump("Dios");
        }
    }
    
    /**
     * 
     * @return type
     * @throws ForbiddenHttpException
     */
    public function actionFacturacion()
    {
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
            $searchModel = new InscripcionSearch();
            $queryParams = array_merge(array(),Yii::$app->request->getQueryParams());
            $queryParams["InscripcionSearch"]["estado"] = 1;
            $dataProvider = $searchModel->search($queryParams);
            $dataProvider->setSort(['defaultOrder' => ['id_empresa'=>SORT_DESC]]);

            return $this->render('facturacion', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,     
            ]);
        }
        else{
            throw new ForbiddenHttpException;
        }
    }
    
    /*
     * 
     */
   public function actionEstadisticas()
    {
        if($this->isUserApproved && Yii::$app->user->can('facturacion')) {
            $searchModel = new InscripcionSearch();
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal
                    END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente order by estados";

            $modelEstadisticasTipo=Yii::$app->db->createCommand($sql)->queryAll();
            $valoresCount = array();
            $valoresFacturados = array();
            $valoresPagos= array();
            $valoresSinPagos= array();
            $valoresNC= array();
            $valoresCC=array();
            foreach ($modelEstadisticasTipo as $a)
            {
                array_push($valoresCount,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>1,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(ta.id)as inscritos,'FACTURADOS' as estados,22 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,'NO FACTURADOS' as estados,32 as idEstados,
                    (CASE 
                        WHEN  COUNT(*) > 0 THEN pr.valor * COUNT(*)
                        ELSE 0  END)  as subtotal 
                    FROM inscripciones i
                    INNER JOIN personas p on(i.id_persona=p.id)
                    LEFT JOIN productos pr ON(i.id_producto = pr.id)
                    LEFT JOIN detalle_factura df on(i.id=df.id_inscripcion)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE  i.is_presence=1 AND ta.facturable='SI' AND i.estado> 0 AND df.id is null AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>0]);
            }

            $sql = "SELECT count(distinct f.id)as inscritos,'NOTAS CREDITO' as estados,33 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                        FROM 
                        facturas f
                        inner join detalle_factura df on(f.id=df.id_factura)
                        where id_serie=3  and df.id_inscripcion >0 and id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresNC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql ="SELECT count(i.id) as inscritos,ef.nombre as estados,ef.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    INNER JOIN personas p ON(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    LEFT JOIN facturas f ON(i.id_factura=f.id)
					INNER JOIN detalle_factura df ON (df.id_factura=f.id)
                    LEFT JOIN relacion_nc_factura nc ON(nc.factura_id=f.id)
                    LEFT JOIN estados_factura ef ON(ef.id=df.id_estado_factura)
                    WHERE p.id_evento = ".$event_id."
                    and ta.facturable='SI'
                    and ef.id is not null
					and i.is_presence=1 
					and nc.nc_id is null
                    GROUP BY ef.id order by 3";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND (ta.id=38 OR ta.id=37)
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar Afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=38
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            // cuenta de cobro no afiliados
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar No afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=37
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }

            $sql = "select nombre,sum(total) as total,sum(iva) as iva FROM (SELECT distinct numero,ef.nombre,f.id_estado_factura as estados,(CASE 
                    WHEN f.trm > 0 THEN f.subtotal * f.trm
                    ELSE f.subtotal  END) AS total,(CASE 
                        WHEN f.trm > 0 THEN f.iva * f.trm 
                        ELSE f.iva  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id.") as t  group by estados";

            $patrocinios=Yii::$app->db->createCommand($sql)->queryAll();

            $sql = "SELECT 'Facturados' as nombre,sum(CASE 
                    WHEN f.trm > 0 THEN df.subtotal * f.trm
                    ELSE df.subtotal  END) AS total,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id;

            $modelPatrociniosFacturados=Yii::$app->db->createCommand($sql)->queryAll();
            $patrociniosFacturados = array();
            foreach ($modelPatrociniosFacturados as $a) {
                array_push($patrociniosFacturados,["nombre"=>$a['nombre'],"valor"=>$a['total'],"iva"=>$a['iva']]);
                array_push($patrociniosFacturados,["nombre"=>'No Facturados',"valor"=>0,"iva"=>0]);
            }
            $sql = "SELECT (p.valor * COUNT(*)) as valor,4 as id,'NO FACTURADOS' as estados "
                    . "FROM inscripciones i "
                    . "LEFT JOIN productos p ON(i.id_producto = p.id) "
                    . "WHERE i.estado=1 AND p.id_evento = ".$event_id;

            $m=Yii::$app->db->createCommand($sql)->queryOne();
            $valoresEstadisticos = array();
            array_push($valoresEstadisticos,["id"=>$m['id'],"valor"=>$m['valor'],"estados"=>$m['estados']]);

            $sql = "SELECT sum(subtotal) as valor, ef.nombre as estados,ef.id as id "
                    . "FROM facturas f "
                    . "LEFT JOIN estados_factura ef ON(ef.id=f.id_estado_factura) "
                    . "WHERE is_patrocinios=0 and f.id_estado_factura is not NULL AND f.id_estado_factura < 4 and f.id_evento = ".$event_id
                    . " GROUP by id_estado_factura";

            $m2=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($m2 as $a)
            {
                array_push($valoresEstadisticos,["id"=>$a['id'],"valor"=>$a['valor'],"estados"=>$a['estados']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.facturable
                    FROM inscripciones i 
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE 
                      i.is_presence=1
					 AND i.estado> 0
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente";
            $inscritosTipo=Yii::$app->db->createCommand($sql)->queryAll();
            return $this->render('estadisticas', [
                'modelEstadisticasCount' => $valoresCount,'modelPatrocinios'=>$patrocinios,
                'modelEstadisticasSum' => $valoresEstadisticos,'pdf'=>0,'inscritosTipo'=>$inscritosTipo,
                'valoresFacturados'=>$valoresFacturados,'valoresSinPagos'=>$valoresSinPagos,'valoresPagos'=>$valoresPagos,
                'patrociniosFacturados'=>$patrociniosFacturados,'valoresCC'=>$valoresCC
            ]);
        
        }
        else{
            throw new ForbiddenHttpException;
        }
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionChangeStatus($id){
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
            $model = \app\models\Inscripciones::findOne($id);
            if ($model->load(Yii::$app->request->post())) {
                if($model->validate())
                {
                    if($this->safeModel($model))
                    {
                          return $this->redirect(['factura/facturados']);
                    }
                    else
                    {
                        return [['respuesta'=>0,'error'=>$model->getErrors()]];
                    }
                }
            } else {
               return $this->renderPartial('_update_status', [
                    'model' => $model,     
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionChangePaymment($id){
        if($this->isUserApproved && Yii::$app->user->can('facturacion'))
        {
            $model = \app\models\Inscripciones::findOne($id);
            if ($model->load(Yii::$app->request->post())) {
                if($model->validate())
                {
                    if($this->safeModel($model))
                    {
                          return $this->redirect(['factura/facturados']);
                    }
                    else
                    {
                        return [['respuesta'=>0,'error'=>$model->getErrors()]];
                    }
                }
            } else {
               return $this->renderPartial('_update_paymment', [
                    'model' => $model,     
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }
    }
    
    /**
     * 
     * @return type
     */
    public function actionFacturados()
    {
        $searchModel = new FacturasSearch();
        $queryParams = array_merge(array(),Yii::$app->request->getQueryParams());
        $dataProvider = $searchModel->search($queryParams);
        //$dataProvider->setPagination([ 'pageSize' => 10 ]);
        //$dataProvider->setSort(['defaultOrder' => ['id'=>SORT_DESC]]);
       
        return $this->render('facturados', [
               'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,     
        ]);
    }
    
     protected function findModelDetalleFactura($id)
    {
        
        $dataProvider = new ActiveDataProvider([
            'query' => DetalleFactura::find()->where(['=','id_factura',$id]),
            //'query' => Personas::find()->where(['>','id',$id]),
        ]);
        
        return $dataProvider;
    }

    /**
     * Displays a single Facturas model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPdf(){
        $facturaWsdl = new FacturaWsdl();
        $objClientDispapelesApis = new ClientDispapelesApi();
        $params['consecutivo'] = '1';
        $params['prefijo'] = 'FENT';
        $params['tipodocumento'] = "1";
        $params['fechafacturacion'] = '2019-02-04';
        $xmlInvoice = $facturaWsdl->sendFile('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
        $response=$objClientDispapelesApis->consultarEstadoFactura($xmlInvoice);
        var_dump($response);
    }

    /**
     * Creates a new Facturas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_inscripcion=0)
    {
        if(!$this->isUserApproved || !Yii::$app->user->can('facturacion')){
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
		    $model->cantidadLineas = $count;
        if($count){
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_factura = new DetalleFactura();
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {//\yii\base\Model::loadMultiple($detalle_factura, Yii::$app->request->post()
                $detalle_factura = Yii::$app->request->post('DetalleFactura');  
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                Yii::$app->response->format = Response::FORMAT_JSON;
                //Verificar Si el model is patrocinios
                if($model->is_patrocinios && $model->id_contacto!=''){
                    $contact = explode("-",$model->id_contacto);
                    if(count($contact) > 1){
                        $model->id_contacto = $contact[0]=="e" ? NULL : $contact[1];
                    }else {
                        $model->id_contacto =  $model->id_contacto;
                    }
                    $client = explode("-",$model->clientes);
                    $client[0]=='p' ? $model->id_persona =  $client[1] : $model->id_empresa =  $client[1];
                }
                else{
                    if($model->idEmpresa && $model->id_contacto=='')
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    $model->id_contacto= NULL;
                }
				        $model->tipoidentificacion = $model->idEmpresa->tipoIdentificacion->codigo;
                $model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion; 
                $model->verificacion = $model->id_empresa ? $model->idEmpresa->verificacion : ""; 
                $model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
				
                if($model->validate() && $detalle_factura){
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->fecha = $this->FormatoFechas($model->fecha);
                    
                        $model->fecha_vencimiento = $this->FormatoFechas($model->fecha_vencimiento);
                        $model->fechaemisionordencompra = $model->fechaemisionordencompra;
                        $session = Yii::$app->session;
                        $event_id = $session->get('event_id');
                        $model->id_evento = $event_id;
                        $model->subtotal=str_replace(",","",$model->subtotal);
                        $model->total=str_replace(",","",$model->total);
                        $model->iva=str_replace(",","",$model->iva);
                        $model->tipo_factura = 'FA';
                        $model->id_serie = 1;
                        $model->trm= $model->trm ? floatval(str_replace(",","",$model->trm)) : 0;

                        $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->andWhere(['numero' => $model->numero])->one();
                        if($oldFactura){
                            $model->numero = strval(\Yii::$app->params['consecutivoFactura'] + 1);
                        }
                        if($model->validate()){
                            $this->safeModel($model);
                            $id=$model->getPrimaryKey();
							              $i = 0;
                            foreach ($detalle_factura as $df) {
                                $detalle_facturas = new DetalleFactura();
                                $detalle_facturas->id_factura=$id;
                                $detalle_facturas->cantidad=$df['cantidad'];
                                $detalle_facturas->subtotal=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));    
                                $detalle_facturas->valor=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->observacion=$df['descripcion'];
                                $detalle_facturas->id_estado_factura = 1;
                                $detalle_facturas->iva=$df['iva'];
								                //$detalle_facturas->user_id = Yii::$app->user->id;
                                
                                $v = explode("-",$df['id_inscripcion']);
                                $detalle_facturas->id_inscripcion=  $v[0] == 'i' ? intval($v[1]) : null;
                           
                                if($v[0] == 'i'){
                                    $modelDetalleRecibos = \app\models\DetalleRecibos::find()->where(['id_inscripcion'=>$v[1]])->all();
                                    foreach ($modelDetalleRecibos as $modelDetalleRecibo){
                                        $modelDetalleRecibo->id_factura = $id;
                                        $modelDetalleRecibo->save(false);
                                    }
                                    $inscripciones = $this->findModelInscripcion($detalle_facturas->id_inscripcion);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 2;
                                    $inscripciones->id_factura = $id;
                                    $inscripciones->save(false);
                                }
                                $modelProductos = $this->findModelProducto($detalle_factura[$i]["id_producto"]);
                                $codigo_producto = $modelProductos->tipo_codigo_producto;
                                $nombre_producto = $modelProductos->nombre;
                                $impuesto_producto = $modelProductos->tipo_impuesto;
                                $detalle_factura[$i]["tipo_codigo_producto"] =  $codigo_producto;
                                $detalle_factura[$i]["nombre_producto"] =  $nombre_producto;
                                $detalle_factura[$i]["tipo_impuesto"] = $impuesto_producto;
                                
                                $detalle_facturas->id_producto= $v[0] == 'i' ? $id_producto : intval($v[1]);
                                if($detalle_facturas->validate()){
                                    $detalle_facturas->save(false);
                                }
                                else{
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                                }
								                $i++;
                            }
                            $transaction->commit();

                            if ($id) {
                                $modelParametro = Parametros::find()->where(['=', 'nombre', 'consecutivoFactura'])->all();
                                $modelParametro[0]->value = \Yii::$app->params['consecutivoFactura'] + 1;
                                $modelParametro[0]->save(false);
                            }

                            try {
                                $facturaWsdl = new FacturaWsdl();
                                $objClientDispapelesApis = new ClientDispapelesApi();
                                $informacionEmpresa = $this->findModelInformacionEmpresa();
                                if($model->id_empresa) { // validar para cuando es solo personas 
                                    $modelEmpresa = $this->findModelEmpresas($model->id_empresa);
                                    $model->ciudad = $modelEmpresa->id_ciudad;
                                    $modelCiudad = $this->findModelDepartamento($model->ciudad);
                                    $modelDepartamento = $this->findModelDepartamento($modelCiudad->id_padre);
                                    $model->departamento = $modelDepartamento->id;
                                    $model->departamentoNombre = $modelDepartamento->nombre;
                                }
                                //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                                $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                $model->tipoDocumento= 1; //Factura de Venta
                                $model->orden_compra = ""; 
                                $model->fechaemisionordencompra = date("Y-m-d"); 
                                $model->numeroaceptacioninterno = ""; 
                                $model->id_impuesto = 1; //01 = IVA
                                $modelFactura = $this->findModel($id);
                                $params = $facturaWsdl->loadNC($model, $detalle_factura,$informacionEmpresa); // token antigiuo e2556e2f2dc65b60653fab4fc380996647363a01
                                
								                $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                                
                                $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
								
								                $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                                $modelFactura->respuesta = json_encode((array)$response->return);

                                if($response->return->estadoProceso == 0){
                                    $modelFactura->save(false);
                                  //return [['respuesta'=>3,'id' => $id,"mensaje"=>"1. ".$response->return->descripcionProceso]];
                                  //Listado de errores presentados por el WS
                                  $listaMensajesProceso = "";
                                  foreach($response->return->listaMensajesProceso as $row){
                                    if($row->rechazoNotificacion == "R"){
                                      $listaMensajesProceso .= $row->descripcionMensaje."\n";
                                    }
                                  }
                                  return [[
                                    'respuesta'=>3
                                    ,'id' => $id
                                    ,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
                                    ,"listaMensajesProceso" => $listaMensajesProceso
                                    ,'redirect'=>Url::toRoute(['/factura/facturados'])
                                  ]];
                                }
                                else{
                                  //if ($response->return->mensaje == 'OK') {
                                  if ($response->return->estadoProceso == 1) {
                                    $modelFactura->cufe = $response->return->cufe;
                                    $modelFactura->fecha_transmision = $response->return->fechaFactura;
                                    $modelFactura->save(false);
                                    return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
                                  }
                                  else
                                  {
                                    $modelFactura->save(false);
                                    return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                                  }
                                }
                            }catch (\SoapFault $e){
                              return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                            }
                        }
                        else{
                            $transaction->rollBack();
                            return [['respuesta'=>20,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                        }
                    } catch (Exception $e) {
                        var_dump($e);
                        $transaction->rollBack();
                        return [['respuesta'=>30,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    }
                 }else {
                    return [['respuesta'=>40,'data'=> \yii\widgets\ActiveForm::validate($model)]];                      
                }
            } else {
               echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->orderBy(['numero' => SORT_DESC])->one();
            $numeroFactura = \Yii::$app->params['consecutivoFactura'];
            $consecutivoFactura = 1;
            if($numeroFactura)
            {
                $consecutivoFactura =  $numeroFactura + 1;
                \Yii::$app->params['consecutivoFactura'] = $consecutivoFactura;
            }
            /*if(!$numeroFactura)
            {
                $consecutivoFactura = $oldFactura->numero + 1;
            }*/
            $model->numero = $consecutivoFactura;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;
            $model->serie=1;
            // $id_inscripcion=1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            $detalle_factura->id_inscripcion=$id_inscripcion;
            $model->id_empresa=$id_empresa;
            $model->id_contacto = $inscripcion->idEmpresa ? $inscripcion->idEmpresa->contactos[0]->id : 0;
            $model->direccion = '';
            $model->telefonoContacto = 0;
            if($inscripcion->idEmpresa) {
                $model->direccion = $inscripcion->idEmpresa->contactos[0]->direccion;
                $model->telefonoContacto = $inscripcion->idEmpresa->contactos[0]->telefono;
                $model->tipoidentificacion = $inscripcion->idEmpresa->tipoIdentificacion->codigo;
                $model->ciudad = $inscripcion->idEmpresa->ciudad->codigo;
                if($inscripcion->idPersona->ciudad->id_pais == 1){
                  $depto = $this->findModelDepartamento($inscripcion->idEmpresa->ciudad->id_padre);
                  $model->departamento = $depto->codigo;
                }
                else{
                  $model->departamento = "";
                }
            } elseif($inscripcion->id_persona) {
                $model->direccion = $inscripcion->idPersona->direccion;
                $model->telefonoContacto = $inscripcion->idPersona->telefono;
                $model->tipoidentificacion = $inscripcion->idPersona->tipoIdentificacion->codigo;
                $model->ciudad = $inscripcion->idPersona->ciudad->codigo;
                
                if($inscripcion->idPersona->ciudad->id_pais == 1){
                  $depto = $this->findModelDepartamento($inscripcion->idPersona->ciudad->id_padre);					
                  $model->departamento = $depto->codigo;
                }
                else{
                  $model->departamento = "";
                }
            }
            
            $model->id_persona=$inscripcion->id_persona ? $inscripcion->id_persona : 0;
            $model->fecha=Yii::$app->formatter->asDate('now', 'php:d/m/Y');
            $model->fecha_factura=Yii::$app->formatter->asDate('03-02-2000', 'php:d/m/Y');
            
            return $this->render('create', [
                'model' => $model,'detalle_factura'=>  $detalle_factura,'listProducto'=>  $this->toList($id_inscripcion),'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList()
                ,'listContacto'=>  Contactos::toList($id_empresa),
                'listEmpresas'=>  $this->tolistEmpresa()
                ,'listPersonas'=> $this->tolistPersona()
                ,'listImpuestos'=> Impuestos::toList()
                ,'listMedioPago'=> MedioPago::toList()
            ]);
        }           
    }


    public function actionCreateNoteMult($id_inscripcion=0){
         if(!$this->isUserApproved || !Yii::$app->user->can('facturacion'))
        {
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
		    $model->cantidadLineas = $count;
        if($count)
        {
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_factura = new DetalleFactura();
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {//\yii\base\Model::loadMultiple($detalle_factura, Yii::$app->request->post()
                $detalle_factura = Yii::$app->request->post('DetalleFactura');
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                Yii::$app->response->format = Response::FORMAT_JSON;
				
				$modelContacto = $this->findModelContacto($model->id_contacto);
				$idEmpresa = $modelContacto->id_empresa;
				$modelEmpresa = $this->findModelEmpresas($idEmpresa);
				$modelTipoIdentificacion = $this->findModelTipoIdentificacion($modelEmpresa->id_tipo_identificacion);
				
				$model->tipoidentificacion  = $modelTipoIdentificacion->codigo;
				$model->identificacion = $modelEmpresa->identificacion; 
                $model->verificacion = $modelEmpresa->verificacion; 
                //$model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
				
                if($model->validate() && $detalle_factura)
                {
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->fecha = $this->FormatoFechas($model->fecha);
                        $session = Yii::$app->session;
                        $event_id = $session->get('event_id');
                        $model->id_evento = $event_id;
                        $model->subtotal=str_replace(",","",$model->subtotal);
                        $model->total=str_replace(",","",$model->total);
                        $model->iva=str_replace(",","",$model->iva);
                        $model->tipo_factura = 'NC';
                        $model->id_serie = 3;
                        $client = explode("-",$model->clientes);
                        $client[0]=='p' ? $model->id_persona =  $client[1] : $model->id_empresa =  $client[1];
                        $model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion;
                        if($model->validate())
                        {
                            $this->safeModel($model);
                            $id=$model->getPrimaryKey();
							$i = 0;
                            foreach ($detalle_factura as $df) {
                                $detalleFacturaOld = $this->findModelDetalleFac($df['id']);
                                $detalleFacturaOld->nc_id = $id;
                                $detalleFacturaOld->save(false);
                                $detalle_facturas = new DetalleFactura();
                                $detalle_facturas->id_factura=$id;
                                $detalle_facturas->cantidad=$df['cantidad'];
                                $detalle_facturas->subtotal=floatval(str_replace(",","",$df['valor']));
                                $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));
                                $detalle_facturas->valor=floatval(str_replace(",","",$df['valor']));
                                $detalle_facturas->observacion=$df['observacion'];
                                $detalle_facturas->iva=$df['iva'];
                                $detalle_facturas->id_inscripcion=$df['id_inscripcion'];
                                $detalle_facturas->id_producto = $df['id_producto'];
                                $detalle_facturas->nc_id = $df['id_producto'];
                                $detalle_facturas->user_id = Yii::$app->user->id;
                                if($df['id_inscripcion']>0){
                                    $inscripciones = $this->findModelInscripcion($df['id_inscripcion']);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 1;
                                    $inscripciones->id_factura = null;
                                    $inscripciones->save(false);
                                }
								
								$modelProductos = $this->findModelProducto($detalle_factura[$i]["id_producto"]);
								$codigo_producto = $modelProductos->tipo_codigo_producto;
								$nombre_producto = $modelProductos->nombre;
								$impuesto_producto = $modelProductos->tipo_impuesto;
								$detalle_factura[$i]["tipo_codigo_producto"] =  $codigo_producto;
								$detalle_factura[$i]["producto"] =  $nombre_producto;
								$detalle_factura[$i]["nombre_producto"] =  $nombre_producto;
								$detalle_factura[$i]["tipo_impuesto"] = $impuesto_producto;

                                if($detalle_facturas->validate())
                                {
                                    $detalle_facturas->save(false);
                                }
                                else
                                {
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                                }
								
								$i++;
                            }
							//var_dump($detalle_factura);
							//exit();
							
                            //create relation nc invoice
                            foreach ($model->facturas as $facturaId) {
                                $ncFactura = new RelacionNcFactura();
                                $ncFactura->factura_id = $facturaId;
                                $ncFactura->nc_id = $id;
                                $ncFactura->tipo = 1;
                                $ncFactura->monto = $model->total;
                                $ncFactura->created_date = Yii::$app->formatter->asDate('now', 'php:Y/m/d');
                                $ncFactura->save(false);
                                //liberar inscripcion
                                $invoiceTotales = $this->getInvoiceTotal($facturaId);
                                $invoiceTotal = $invoiceTotales[0]['total'];
                                foreach ($invoiceTotales as $invoice) {
                                    if ($invoice['tipo'] == 1)
                                        $invoiceTotal -= $invoice['monto'];
                                    if ($invoice['tipo'] == 2)
                                        $invoiceTotal += $invoice['monto'];
                                }
                                //if ($invoiceTotal <= 0) {
                                if($invoiceTotal <= -2){
                                    $transaction->rollBack();
                                    return [['respuesta' => 3, 'data' => $invoiceTotal]];
                                }
                                $infoFactura[] = $this->findModel($facturaId);
                            }
                            $transaction->commit();
                            //var_dump("ddd");
                            if($id)
                            {
                                $modelParametro = Parametros::find()->where(['=', 'nombre', 'consecutivoNC'])->all();
                                $modelParametro[0]->value = \Yii::$app->params['consecutivoNC'] + 1;
                                $modelParametro[0]->save(false);
                            }
                            try{
                                $facturaWsdl = new FacturaWsdl();
                                $objClientDispapelesApis = new ClientDispapelesApi();
                                $informacionEmpresa = $this->findModelInformacionEmpresa();
                                $model->tipoDocumento= 2;
								                $model->tipoNota = 2; //$model->id_tipo_nota; //1. DEVOLUCIÓN DE PARTE DE LOS BIENES 2. ANULACIÓN DE FACTURA 3. REBAJA TOTAL APLICADA 4. DESCUENTO TOTAL APLICADO 5. RESCISIÓN: NULIDAD POR FALTA DE REQUISITOS 6. OTROS.
                                //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                                $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                $params = $facturaWsdl->loadNC($model,$detalle_factura,$informacionEmpresa,$infoFactura[0],$infoFactura);
                                //$xmlInvoice = $facturaWsdl->createNC('5ce21110c1e2d29f448f9ee0a10f26fed0238d25',$params);
								                $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                                
                                $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
                                //var_dump($response);die;
                                $modelFactura = $this->findModel($id);
                                $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                                $modelFactura->respuesta = json_encode((array)$response->return);
								
								if($response->return->estadoProceso == 0){
								    $modelFactura->save(false);
									//Listado de errores presentados por el WS
									$listaMensajesProceso = "";
									foreach($response->return->listaMensajesProceso as $row){
										if($row->rechazoNotificacion == "R"){
											$listaMensajesProceso .= $row->descripcionMensaje."\n";
										}
									}
									return [[
										'respuesta'=>4
										,'id' => $id
										,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
										,"listaMensajesProceso" => $listaMensajesProceso
										,'redirect'=>Url::toRoute(['/factura/facturados'])
									]];
								}
								else{
									//if($response->return->mensaje=='OK')
									if ($response->return->estadoProceso == 1)
									{
										$modelFactura->fecha_transmision = $response->return->fechaFactura;
										$modelFactura->save(false);
										//return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
										return [['respuesta'=>4,"mensaje"=>"1. - ".$response->return->descripcionProceso]];
									}
									else
									{
										$modelFactura->save(false);
										//return ['respuesta'=>2,'error' => $e,'redirect'=>Url::toRoute(['/factura/facturados'])];
										return [['respuesta'=>4,"mensaje"=>"2. - ".$response->return->descripcionProceso]];
									}
								}
                            }catch (\SoapFault $e){
                                //return ['respuesta'=>2,'error' => $e,'redirect'=>Url::toRoute(['/factura/facturados'])];
								return [['respuesta'=>4,"mensaje"=>"3. - ".$response->return->descripcionProceso]];
                            }
                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data1'=> \yii\widgets\ActiveForm::validate($model)]];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        var_dump("ee",$e);
                        return [['respuesta'=>0,'data2'=> \yii\widgets\ActiveForm::validate($model)]];
                    }
                }
                else {
                    return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];

                }
            } else {
                echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->orderBy(['numero' => SORT_DESC])->one();
            $numeroFactura = \Yii::$app->params['consecutivoFactura'];
            $numeroNC = \Yii::$app->params['consecutivoNC'];
            $consecutivoNC = 1;
            if($numeroNC)
            {
                $consecutivoNC =  $numeroNC + 1;
                \Yii::$app->params['consecutivoNC'] = $consecutivoNC;
            }

            $model->numero = $consecutivoNC;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;

            // $id_inscripcion=1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            $detalle_factura->id_inscripcion=$id_inscripcion;
            $model->id_empresa=$id_empresa;
            $model->id_contacto = $inscripcion->idEmpresa ? $inscripcion->idEmpresa->contactos[0]->id : 0;
            $model->direccion = '';
            $model->telefonoContacto = 0;
            if($inscripcion->idEmpresa)
            {
                $model->direccion = $inscripcion->idEmpresa->contactos[0]->direccion;
                $model->telefonoContacto = $inscripcion->idEmpresa->contactos[0]->telefono;
            }
            else if($inscripcion->id_persona)
            {
                $model->direccion = $inscripcion->idPersona->direccion;
                $model->telefonoContacto = $inscripcion->idPersona->telefono;
            }

			      $model->tipo_compra = 1;
            $model->periodo_pago = 30;
            $model->fecha_vencimiento = date('Y-m-d');
            $model->id_persona=$inscripcion->id_persona ? $inscripcion->id_persona : 0;
            $model->fecha=Yii::$app->formatter->asDate('now', 'php:d/m/Y');
            return $this->render('_creditFormMult', [
                'model' => $model,'detalle_factura'=>  $detalle_factura,'listProducto'=>  $this->toList($id_inscripcion),'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList(),'listContacto'=>  Contactos::toList($id_empresa),'listEmpresas'=>  $this->tolistEmpresa(),'listPersonas'=> $this->tolistPersona()
            ]);
        }
    }

    public function actionValidateDateInvoice(){
        $date=  Yii::$app->request->post('date','');
        $serie=  Yii::$app->request->post('serie','');
        $number=  Yii::$app->request->post('number','');
        $serie = $serie ==1 ? 'FA' : 'NC';

        $sql = "SELECT * FROM facturas WHERE tipo_factura='".$serie."' order by fecha DESC";
        $modelFactura=Yii::$app->db->createCommand($sql)->queryOne();

        $sql = "SELECT * FROM informacion_empresa";
        $modelNumber=Yii::$app->db->createCommand($sql)->queryOne();

        $desdeInfo = intval($serie =='FA' ? $modelNumber['desde_factura'] : $modelNumber['desde_contingencia']);
        $hastaInfo = intval($serie =='FA' ? $modelNumber['hasta_factura'] : $modelNumber['hasta_contingencia']);
        $number = intval($number);

        $numberValidataion = $number >= $desdeInfo && $number <= $hastaInfo;


        var_dump($numberValidataion);

        $date = $this->FormatoFechas($date);
        $band= $modelFactura['fecha'] > $date ? 0 : 1;
        $response = $band == 1 || $numberValidataion ? 1 : 0;

        return $response;
    }

    public function actionContingencia($id_inscripcion=0){
         if(!$this->isUserApproved || !Yii::$app->user->can('facturacion'))
        {
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
		$model->cantidadLineas = $count;
        if($count)
        {
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_factura = new DetalleFactura();
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {//\yii\base\Model::loadMultiple($detalle_factura, Yii::$app->request->post()
                $detalle_factura = Yii::$app->request->post('DetalleFactura');
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                Yii::$app->response->format = Response::FORMAT_JSON;

                //Verificar Si el model is patrocinios
                if($model->is_patrocinios && $model->id_contacto!='')
                {
                    $contact = explode("-",$model->id_contacto);
                    if(count($contact) > 1)
                    {
                        $model->id_contacto = $contact[0]=="e" ? NULL : $contact[1];
                    }else {
                        $model->id_contacto =  $model->id_contacto;
                    }
                    $client = explode("-",$model->clientes);
                    $client[0]=='p' ? $model->id_persona =  $client[1] : $model->id_empresa =  $client[1];

                }
                else{
                    if($model->idEmpresa && $model->id_contacto=='')
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    $model->id_contacto= NULL;
                }
				        $model->tipoidentificacion = ($model->idEmpresa->tipoIdentificacion->codigo);
                $model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion;
				        $model->verificacion = $model->id_empresa ? $model->idEmpresa->verificacion : ""; 
                $model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
                if($model->validate() && $detalle_factura)
                {
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->fecha = $this->FormatoFechas($model->fecha);
						            $model->fecha_vencimiento = $this->FormatoFechas($model->fecha_vencimiento);
                        $model->fechaemisionordencompra = $this->FormatoFechas($model->fechaemisionordencompra);
                        $session = Yii::$app->session;
                        $event_id = $session->get('event_id');
                        $model->id_evento = $event_id;
                        $model->subtotal=str_replace(",","",$model->subtotal);
                        $model->total=str_replace(",","",$model->total);
                        $model->iva=str_replace(",","",$model->iva);
                        $model->tipo_factura = 'FA';
                        $model->id_serie = 2;
						            $model->trm= $model->trm ? floatval(str_replace(",","",$model->trm)) : 0;
                        $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->andWhere(['numero' => $model->numero])->one();
                        if($oldFactura)
                        {
                            $model->numero = strval(\Yii::$app->params['consecutivoFacturaContingencia'] + 1);
                        }
                        if($model->validate())
                        {
                            $this->safeModel($model);
                            $id=$model->getPrimaryKey();
							              $i = 0;
                            foreach ($detalle_factura as $df) {
                                $detalle_facturas = new DetalleFactura();
                                $detalle_facturas->id_factura=$id;
                                $detalle_facturas->cantidad=$df['cantidad'];
                                $detalle_facturas->subtotal=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));
                                $detalle_facturas->valor=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->observacion=$df['descripcion'];
                                $detalle_facturas->iva=$df['iva'];
                                
                                $v = explode("-",$df['id_inscripcion']);
                                $detalle_facturas->id_inscripcion=  $v[0] == 'i' ? intval($v[1]) : null;
                              
                                if($v[0] == 'i'){
                                    $modelDetalleRecibos = \app\models\DetalleRecibos::find()->where(['id_inscripcion'=>$v[1]])->all();
                                    foreach ($modelDetalleRecibos as $modelDetalleRecibo)
                                    {
                                        $modelDetalleRecibo->id_factura = $id;
                                        $modelDetalleRecibo->save(false);
                                    }
                                    $inscripciones = $this->findModelInscripcion($detalle_facturas->id_inscripcion);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 2;
                                    $inscripciones->id_factura = $id;
                                    $inscripciones->save(false);
                                }
                                $modelProductos = $this->findModelProducto($detalle_factura[$i]["id_producto"]);
                                $codigo_producto = $modelProductos->tipo_codigo_producto;
                                $nombre_producto = $modelProductos->nombre;
                                $impuesto_producto = $modelProductos->tipo_impuesto;
                                $detalle_factura[$i]["tipo_codigo_producto"] =  $codigo_producto;
                                $detalle_factura[$i]["nombre_producto"] =  $nombre_producto;
                                $detalle_factura[$i]["tipo_impuesto"] = $impuesto_producto;


                                $detalle_facturas->id_producto= $v[0] == 'i' ? $id_producto : intval($v[1]);
                                if($detalle_facturas->validate())
                                {
                                    $detalle_facturas->save(false);
                                }
                                else
                                {
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                                }
								                $i++;
                            }
                            $transaction->commit();

                            if ($id) {
                                $modelParametro = Parametros::find()->where(['=', 'nombre', 'consecutivoFacturaContingencia'])->all();
                                $modelParametro[0]->value = \Yii::$app->params['consecutivoFacturaContingencia'] + 1;
                                $modelParametro[0]->save(false);
                            }

                            try {
                                $facturaWsdl = new FacturaWsdl();
                                $objClientDispapelesApis = new ClientDispapelesApi();
                                $informacionEmpresa = $this->findModelInformacionEmpresa();
                                //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                                $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                $model->tipoDocumento= 5; //Factura de Contingencia
                                $modelFactura = $this->findModel($id);
                                $params = $facturaWsdl->loadNC($model, $detalle_factura,$informacionEmpresa);
                                //$xmlInvoice = $facturaWsdl->createFactura('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
                                $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                                
                                $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
                                $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                                $modelFactura->respuesta = json_encode((array)$response->return);
								
                                if($response->return->estadoProceso == 0){
                                  $modelFactura->save(false);
                                  //return [['respuesta'=>3,'id' => $id,"mensaje"=>"1. ".$response->return->descripcionProceso]];
                                  //Listado de errores presentados por el WS
                                  $listaMensajesProceso = "";
                                  foreach($response->return->listaMensajesProceso as $row){
                                    if($row->rechazoNotificacion == "R"){
                                      $listaMensajesProceso .= $row->descripcionMensaje."\n";
                                    }
                                  }
                                  return [[
                                    'respuesta'=>3
                                    ,'id' => $id
                                    ,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
                                    ,"listaMensajesProceso" => $listaMensajesProceso
                                    ,'redirect'=>Url::toRoute(['/factura/facturados'])
                                  ]];
                                }
                                else{
                                  //if ($response->return->mensaje == 'OK') {
                                  if ($response->return->estadoProceso == 1) {
                                    $modelFactura->cufe = $response->return->cufe;
                                    $modelFactura->fecha_transmision = $response->return->fechaFactura;
                                    $modelFactura->save(false);
                                    return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
                                  }
                                  else
                                  {
                                    $modelFactura->save(false);
                                    return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                                  }
                                }
                            }catch (\SoapFault $e){
                                return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                            }

                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    }
                }
                else {
                    return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];

                }
            } else {
                echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->orderBy(['numero' => SORT_DESC])->one();
            $numeroFactura = \Yii::$app->params['consecutivoFacturaContingencia'];
            $consecutivoFactura = 1;
            if($numeroFactura)
            {
                $consecutivoFactura =  $numeroFactura + 1;
                \Yii::$app->params['consecutivoFacturaContingencia'] = $consecutivoFactura;
            }
            /*if(!$numeroFactura)
            {
                $consecutivoFactura = $oldFactura->numero + 1;
            }*/
            $model->numero = $consecutivoFactura;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;
            $model->serie=2;
            // $id_inscripcion=1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            $detalle_factura->id_inscripcion=$id_inscripcion;
            $model->id_empresa=$id_empresa;
            $model->id_contacto = $inscripcion->idEmpresa ? $inscripcion->idEmpresa->contactos[0]->id : 0;
            $model->direccion = $inscripcion->idEmpresa ? $inscripcion->idEmpresa->contactos[0]->direccion : 0;
            $model->telefonoContacto = $inscripcion->idEmpresa ? $inscripcion->idEmpresa->contactos[0]->telefono : 0;
            $model->id_persona=$inscripcion->id_persona ? $inscripcion->id_persona : 0;

            if($inscripcion->idEmpresa) {
              $model->direccion = $inscripcion->idEmpresa->contactos[0]->direccion;
              $model->telefonoContacto = $inscripcion->idEmpresa->contactos[0]->telefono;
              $model->tipoidentificacion = $inscripcion->idEmpresa->tipoIdentificacion->codigo;
              $model->ciudad = $inscripcion->idEmpresa->ciudad->codigo;
              if($inscripcion->idPersona->ciudad->id_pais == 1){
                $depto = $this->findModelDepartamento($inscripcion->idEmpresa->ciudad->id_padre);
                $model->departamento = $depto->codigo;
              }
              else{
                $model->departamento = "";
              }
          } elseif($inscripcion->id_persona) {
              $model->direccion = $inscripcion->idPersona->direccion;
              $model->telefonoContacto = $inscripcion->idPersona->telefono;
              $model->tipoidentificacion = $inscripcion->idPersona->tipoIdentificacion->codigo;
              $model->ciudad = $inscripcion->idPersona->ciudad->codigo;
              
              if($inscripcion->idPersona->ciudad->id_pais == 1){
                $depto = $this->findModelDepartamento($inscripcion->idPersona->ciudad->id_padre);					
                $model->departamento = $depto->codigo;
              }
              else{
                $model->departamento = "";
              }
          }


            $model->fecha=Yii::$app->formatter->asDate('now', 'php:d/m/Y');
            $model->fecha_factura=Yii::$app->formatter->asDate('03-02-2000', 'php:d/m/Y');

            return $this->render('create', [
                'model' => $model,'detalle_factura'=>  $detalle_factura,'listProducto'=>  $this->toList($id_inscripcion),'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList()
                ,'listContacto'=>  Contactos::toList($id_empresa)
                ,'listEmpresas'=>  $this->tolistEmpresa()
                ,'listPersonas'=> $this->tolistPersona()
                ,'listImpuestos'=> Impuestos::toList()
                ,'listMedioPago'=> MedioPago::toList()
            ]);
        }
    }
    
    


    /**
     * @param int $id_inscripcion
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionCreateNote($id=0)
    {
         if(!$this->isUserApproved || !Yii::$app->user->can('facturacion'))
        {
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
	  	$model->cantidadLineas = $count;
		  if($count)
        {
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_facturas = [new DetalleFactura()];
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                $detalle_factura = Yii::$app->request->post('DetalleFactura');
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                Yii::$app->response->format = Response::FORMAT_JSON;
				
				$model->tipoidentificacion  = $model->idEmpresa->tipoIdentificacion->codigo;
				$model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion; 
                $model->verificacion = $model->id_empresa ? $model->idEmpresa->verificacion : ""; 
                $model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
                if($model->validate() && $detalle_factura)
                {
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->fecha = $this->FormatoFechas($model->fecha);
						$model->fecha_vencimiento = $model->fecha_vencimiento;
                        $model->fechaemisionordencompra = $model->fechaemisionordencompra;
                        $session = Yii::$app->session;
                        $event_id = $session->get('event_id');
                        $model->id_evento = $event_id;
                        $model->subtotal=str_replace(",","",$model->subtotal);
                        $model->total=str_replace(",","",$model->total);
                        $model->iva=str_replace(",","",$model->iva);
                        $model->tipo_factura = 'NC';
						$model->tipoDocumentoFacturaModificada = $model->id_serie; 
                        $model->id_serie = 3;
                        $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->andWhere(['numero' => $model->numero])->one();
                        if($oldFactura)
                        {
                            $model->numero = $model->numero == 1 ? $model->numero : strval(\Yii::$app->params['consecutivoNC'] + 1);
                        }
                       // var_dump("id",$model->numero);die;
                        if($model->validate())
                        {
                            $this->safeModel($model);
                            $id=$model->getPrimaryKey();
							$i = 0;
                            foreach ($detalle_factura as $df) {
                                $detalleFacturaOld = $this->findModelDetalleFac($df['id']);
                                $detalleFacturaOld->nc_id = $id;
                                $detalleFacturaOld->save(false);

                                $detalle_facturas = new DetalleFactura();
                                $detalle_facturas->id_factura=$id;
                                $detalle_facturas->cantidad=$df['cantidad'];
                                $detalle_facturas->subtotal=floatval(str_replace(",","",$df['valor']));
                                $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));
                                $detalle_facturas->valor=floatval(str_replace(",","",$df['valor']));
                                $detalle_facturas->observacion=$df['observacion'];
                                $detalle_facturas->iva=$df['iva'];
                                $detalle_facturas->id_inscripcion=$df['id_inscripcion'];
								$detalle_facturas->id_producto = $df['id_producto'];
                                $detalle_facturas->nc_id = $df['id_producto'];
								$detalle_facturas->user_id = Yii::$app->user->id;
                                if($df['id_inscripcion']){
                                    $inscripciones = $this->findModelInscripcion($df['id_inscripcion']);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 1;
                                    $inscripciones->id_factura = null;
                                    $inscripciones->save(false);
                                }
								$modelProductos = $this->findModelProducto($detalle_factura[$i]["id_producto"]);
								$codigo_producto = $modelProductos->tipo_codigo_producto;
								$nombre_producto = $modelProductos->nombre;
								$impuesto_producto = $modelProductos->tipo_impuesto;
								$detalle_factura[$i]["tipo_codigo_producto"] =  $codigo_producto;
								$detalle_factura[$i]["nombre_producto"] =  $nombre_producto;
								$detalle_factura[$i]["tipo_impuesto"] = $impuesto_producto;
								
								
                                if($detalle_facturas->validate())
                                {
                                    $detalle_facturas->save(false);
                                }
                                else
                                {
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                                }
								$i++;
                            }
                            //create relation nc invoice
                            $ncFactura = new RelacionNcFactura();
                            $ncFactura->factura_id = $model->facturaNC;
                            $ncFactura->nc_id = $id;
                            $ncFactura->tipo = 1;
                            $ncFactura->monto = $model->total;
                            $ncFactura->created_date=Yii::$app->formatter->asDate('now', 'php:Y/m/d');
                            $ncFactura->save(false);
                            //liberar inscripcion
                            $invoiceTotales = $this->getInvoiceTotal($model->facturaNC);
                            $invoiceTotal = $invoiceTotales[0]['total'];
                            foreach ($invoiceTotales as $invoice)
                            {
                                if($invoice['tipo'] == 1)
                                    $invoiceTotal -=$invoice['monto'];
                                if($invoice['tipo'] == 2)
                                    $invoiceTotal +=$invoice['monto'];
                            }
                            if($invoiceTotal <= -2)
                            {
                                $transaction->rollBack();
                                return [['respuesta'=>3,'data'=> $invoiceTotal]];
                            }

                            $transaction->commit();
                            if($id)
                            {
                                $modelParametro = Parametros::find()->where(['=', 'nombre', 'consecutivoNC'])->all();
                                $modelParametro[0]->value = \Yii::$app->params['consecutivoNC'] + 1;
                                $modelParametro[0]->save(false);
                            }
                            try{
                                $facturaWsdl = new FacturaWsdl();
                                $objClientDispapelesApis = new ClientDispapelesApi();
                                $informacionEmpresa = $this->findModelInformacionEmpresa();
                                $infoFactura = $this->findModel($model->facturaNC);
                                //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                                $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                $model->tipoDocumento = 2; //Nota Crédito
                                $model->tipoNota = $model->id_tipo_nota; //1. DEVOLUCIÓN DE PARTE DE LOS BIENES 2. ANULACIÓN DE FACTURA 3. REBAJA TOTAL APLICADA 4. DESCUENTO TOTAL APLICADO 5. RESCISIÓN: NULIDAD POR FALTA DE REQUISITOS 6. OTROS.
                                $params = $facturaWsdl->loadNC($model,$detalle_factura,$informacionEmpresa,$infoFactura);
                                //$xmlInvoice = $facturaWsdl->createNC('5ce21110c1e2d29f448f9ee0a10f26fed0238d25',$params);
								                $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                                
                                $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
                                $modelFactura = $this->findModel($id);
                                $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                                $modelFactura->respuesta = json_encode((array)$response->return);
                                
								
                                if($response->return->estadoProceso == 0){
                                    $modelFactura->save(false);
									//return [['respuesta'=>4,'id' => $id,"mensaje"=>"1. ".$response->return->descripcionProceso]];
									//Listado de errores presentados por el WS
									$listaMensajesProceso = "";
									foreach($response->return->listaMensajesProceso as $row){
										if($row->rechazoNotificacion == "R"){
											$listaMensajesProceso .= $row->descripcionMensaje."\n";
										}
									}
									return [[
										'respuesta'=>4
										,'id' => $id
										,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
										,"listaMensajesProceso" => $listaMensajesProceso
										,'redirect'=>Url::toRoute(['/factura/facturados'])
									]];
								}
								else{
									//if($response->return->mensaje=='OK'){
									if ($response->return->estadoProceso == 1)
									{
										$modelFactura->fecha_transmision = $response->return->fechaFactura;
										$modelFactura->save(false);
										return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
									}
									else
									{
										$modelFactura->save(false);
										return ['respuesta'=>2,'error' => $e,'redirect'=>Url::toRoute(['/factura/facturados'])];
									}
								}
                            }catch (\SoapFault $e){
                                //return ['respuesta'=>2,'error' => $e,'redirect'=>Url::toRoute(['/factura/facturados'])];
								return [['respuesta'=>4,"mensaje"=>"3. - ".$response->return->descripcionProceso]];
                            }
                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                            //return [['respuesta'=>0,'data'=> "1111"]];
                        }
                    } catch (Exception $e) {
						var_dump($e);
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                        //return [['respuesta'=>0,'data'=> $e]];
                    }
                }
                else {
                    return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];

                }
            } else {
                echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $numeroNC = \Yii::$app->params['consecutivoNC'];
            $consecutivoNC = 1;
            if($numeroNC)
            {
                if($numeroNC>1)
                $consecutivoNC =  $numeroNC + 1;
                \Yii::$app->params['consecutivoNC'] = $consecutivoNC;
            }
            $factura = $this->getFacturas($id);
            //var_dump($factura);die;
            foreach ($factura['Factura'] as $i=>$df) {
				$detalle_facturas[$i] = new DetalleFactura();
                $detalle_facturas[$i]->id=$df['dfId'];
				$detalle_facturas[$i]->id_factura=$id;
				$detalle_facturas[$i]->id_producto=$df['id_producto'];
				$detalle_facturas[$i]->id_inscripcion=$df['id_inscripcion'];	
				$detalle_facturas[$i]->cantidad=$df['cantidad'];
				$detalle_facturas[$i]->subtotal=floatval(str_replace(",","",$df['valorSubtotal']));
				$detalle_facturas[$i]->valorTotal=$df['valorTotal'];
				$detalle_facturas[$i]->valor=floatval(str_replace(",","",$df['valorSubtotal']));
				$detalle_facturas[$i]->observacion=$df['descripcion'];
				$detalle_facturas[$i]->iva=$df['valorIva'];
				$model->subtotal = $df['subtotal'];
				$model->iva = $df['iva'];
				$model->total = $df['total'];
				$model->id_contacto = $df['id_contacto'];
				$model->id_empresa=$df['id_empresa'];
				$model->clientes=$df['cliente'];
				$model->identificacion=$df['identificaciones'];
				$model->cufe=$df['cufe'];
				$numero = $df['numero'];
			}
			
            $model->numero = $consecutivoNC;
            $id_inscripcion = 0;
            $model->facturaNC = $id;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;
            // $id_inscripcion=1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            //$detalle_factura->id_inscripcion=$id_inscripcion;
            //$model->id_empresa=$id_empresa;
            //var_dump($factura['Contacto']['direccion']);die;
            $model->direccion = $factura['Contacto']['direccion'];
            $model->telefonoContacto = $factura['Contacto']['telefono'];
            $model->tipo_compra = $factura['Factura'][0]['tipo_compra'];
            $model->id_moneda = $factura['Factura'][0]['id_moneda'];
            $model->trm = $factura['Factura'][0]['trm'];
            //var_dump($factura['Factura']);die;
            $model->periodo_pago = $factura['Factura'][0]['periodo_pago'] ? $factura['Factura'][0]['periodo_pago'] : 0 ;
            $model->fecha_vencimiento = $factura['Factura'][0]['fecha_vencimiento'];
            $model->id_impuesto = $factura['Factura'][0]['id_impuesto'];
            $model->id_medio_pago = $factura['Factura'][0]['id_medio_pago'];
            $model->orden_compra = $factura['Factura'][0]['orden_compra'];
            $model->fechaemisionordencompra = $factura['Factura'][0]['fechaemisionordencompra'];
            $model->numeroaceptacioninterno = $factura['Factura'][0]['numeroaceptacioninterno'];
            $model->facturaNumero = $numero;
			$model->id_serie = $factura["Factura"][0]["id_serie"];
			$model->fecha_factura =Yii::$app->formatter->asDate( $factura['Factura'][0]['fecha'], 'php:d/m/Y');
            $model->fecha=Yii::$app->formatter->asDate('now', 'php:d/m/Y');
			/*var_dump($factura);
			print "<hr>";
			var_dump($model);
			exit();*/
            return $this->render('_creditForm', [
                'model' => $model,
                'title' => $numero,
                'detalle_factura'=>  $detalle_facturas,
                'listProducto'=>  $this->toList($id_inscripcion),
                'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList(),
                'listContacto'=>  Contactos::toList($model->id_empresa),
                'listEmpresas'=>  $this->tolistEmpresa(),
                'listPersonas'=> $this->tolistPersona()
				,'listImpuestos'=> Impuestos::toList()
				,'listMedioPago'=> MedioPago::toList()
				,'listTNCredito'=> TipoNota::toListCredito()
            ]);
        }
    }


    public function actionCreateDebit($id=0){
         if(!$this->isUserApproved || !Yii::$app->user->can('facturacion'))
        {
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
		    $model->cantidadLineas = $count;
        if($count)
        {
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_factura = new DetalleFactura();
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {//\yii\base\Model::loadMultiple($detalle_factura, Yii::$app->request->post()
                $detalle_factura = Yii::$app->request->post('DetalleFactura');
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                Yii::$app->response->format = Response::FORMAT_JSON;

                //Verificar Si el model is patrocinios
                if($model->is_patrocinios && $model->id_contacto!='')
                {
                    $contact = explode("-",$model->id_contacto);
                    $model->id_contacto = count($contact) > 1 ? $contact[1] : $model->id_contacto;
                    $client = explode("-",$model->clientes);
                    //$client[0]=='p' ? $model->id_persona =  $client[1] : $model->id_empresa =  $client[1];
                }
				$model->tipoidentificacion  = $model->idEmpresa->tipoIdentificacion->codigo;
				$model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion; 
                $model->verificacion = $model->id_empresa ? $model->idEmpresa->verificacion : ""; 
                $model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
				//print "<pre>";
				//var_dump($model->id_serie);
				//print "</pre>";
				//exit();
                if($model->validate() && $detalle_factura)
                {
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->fecha = $this->FormatoFechas($model->fecha);
						$model->fecha_vencimiento = $model->fecha_vencimiento;
                        $model->fechaemisionordencompra = $model->fechaemisionordencompra;
                        $session = Yii::$app->session;
                        $event_id = $session->get('event_id');
                        $model->id_evento = $event_id;
                        $model->subtotal=str_replace(",","",$model->subtotal);
                        $model->total=str_replace(",","",$model->total);
                        $model->iva=str_replace(",","",$model->iva);
                        $model->tipo_factura = 'ND';
						$model->tipoDocumentoFacturaModificada = $model->id_serie; 
                        $model->id_serie = 4;
                        if($model->validate())
                        {
                            $this->safeModel($model);
                            $id=$model->getPrimaryKey();
							$i = 0;
                            foreach ($detalle_factura as $df) {
                                $detalle_facturas = new DetalleFactura();
                                $detalle_facturas->id_factura=$id;
                                $detalle_facturas->cantidad=$df['cantidad'];
                                $detalle_facturas->subtotal=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));
                                $detalle_facturas->valor=floatval(str_replace(",","",$df['subtotal']));
                                $detalle_facturas->observacion=$df['descripcion'];
                                $detalle_facturas->iva=$df['iva'];
                                $detalle_facturas->user_id = Yii::$app->user->id;

                                $v = explode("-",$df['id_inscripcion']);
                                $detalle_facturas->id_inscripcion=  $v[0] == 'i' ? intval($v[1]) : null;

                                if($v[0] == 'i'){
                                    $modelDetalleRecibos = \app\models\DetalleRecibos::find()->where(['id_inscripcion'=>$v[1]])->all();
                                    foreach ($modelDetalleRecibos as $modelDetalleRecibo)
                                    {
                                        $modelDetalleRecibo->id_factura = $id;
                                        $modelDetalleRecibo->save(false);
                                    }
                                    $inscripciones = $this->findModelInscripcion($detalle_facturas->id_inscripcion);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 2;
                                    $inscripciones->id_factura = $id;
                                    $inscripciones->save(false);
                                }
								$modelProductos = $this->findModelProducto($detalle_factura[$i]["id_producto"]);
								$codigo_producto = $modelProductos->tipo_codigo_producto;
								$nombre_producto = $modelProductos->nombre;
								$impuesto_producto = $modelProductos->tipo_impuesto;
								$detalle_factura[$i]["tipo_codigo_producto"] =  $codigo_producto;
								$detalle_factura[$i]["nombre_producto"] =  $nombre_producto;
								$detalle_factura[$i]["tipo_impuesto"] = $impuesto_producto;
								
                                $detalle_facturas->id_producto= $v[0] == 'i' ? $id_producto : intval($v[1]);
                                if($detalle_facturas->validate())
                                {
                                    $detalle_facturas->save(false);
                                }
                                else
                                {
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                                }
								$i++;
                            }
                            $transaction->commit();

                            if ($id) {
                                $modelParametro = Parametros::find()->where(['=', 'nombre', 'consecutivoND'])->all();
                                $modelParametro[0]->value = \Yii::$app->params['consecutivoND'] + 1;
                                $modelParametro[0]->save(false);
                            }
                            //create relation nc invoice
                            $ncFactura = new RelacionNcFactura();
                            $ncFactura->factura_id = $model->facturaNC;
                            $ncFactura->nc_id = $id;
                            $ncFactura->tipo = 2;
                            $ncFactura->monto = $model->total;
                            $ncFactura->created_date=Yii::$app->formatter->asDate('now', 'php:Y/m/d');
                            $ncFactura->save(false);
							
							//var_dump($model);

                            try {
                                $facturaWsdl = new FacturaWsdl();
                                $objClientDispapelesApis = new ClientDispapelesApi();
                                $informacionEmpresa = $this->findModelInformacionEmpresa();
                                $infoFactura = $this->findModel($model->facturaNC);
                                $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                $model->tipoDocumento= 3; //Nota Debito
								                $model->tipoNota = $model->id_tipo_nota; //7. INTERESES 8. GASTOS POR COBRAR 9. CAMBIO DEL VALOR 10.OTROS
                                $params = $facturaWsdl->loadNC($model,$detalle_factura,$informacionEmpresa,$infoFactura);
                                
                                //$xmlInvoice = $facturaWsdl->createND('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
								                $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                                
                                $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
                                $modelFactura = $this->findModel($id);
                                $modelFactura->send_xml = json_encode((array)$xmlInvoice);
                                $modelFactura->respuesta = json_encode((array)$response->return);
                                
								if($response->return->estadoProceso == 0){
								    $modelFactura->save(false);
									//return [['respuesta'=>3,'id' => $id,"mensaje"=>"1. ".$response->return->descripcionProceso]];
									//Listado de errores presentados por el WS
									$listaMensajesProceso = "";
									foreach($response->return->listaMensajesProceso as $row){
										if($row->rechazoNotificacion == "R"){
											$listaMensajesProceso .= $row->descripcionMensaje."\n";
										}
									}
									return [[
										'respuesta'=>3
										,'id' => $id
										,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
										,"listaMensajesProceso" => $listaMensajesProceso
										,'redirect'=>Url::toRoute(['/factura/facturados'])
									]];
								}
								else{
									//if($response->return->mensaje=='OK')
									if ($response->return->estadoProceso == 1) 
									{
										$modelFactura->fecha_transmision = $response->return->fechaFactura;
										$modelFactura->save(false);
										return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
									}
									else
									{
										$modelFactura->save(false);
										return ['respuesta'=>2,'error' => $e,'redirect'=>Url::toRoute(['/factura/facturados'])];
									}
								}
                            }catch (\SoapFault $e){
                                return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                            }

                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    }
                }
                else {
                    return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];

                }
            } else {
                echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $oldFactura = Facturas::find()->where(['=', 'id_evento', $event_id])->orderBy(['numero' => SORT_DESC])->one();
            $numeroFactura = \Yii::$app->params['consecutivoND'];
            $consecutivoFactura = 1;
            if($numeroFactura)
            {
                if($numeroFactura>1)
                $consecutivoFactura =  $numeroFactura + 1;
                \Yii::$app->params['consecutivoND'] = $consecutivoFactura;
            }
            
            
            $factura = $this->getFacturas($id);
            $numero = '';
            foreach ($factura['Factura'] as $i=>$df) {
                $numero = $df['numero'];
                $model->id_contacto = $df['id_contacto'];
                $model->id_empresa=$df['id_empresa'];
                $model->clientes=$df['cliente'];
                $model->identificacion=$df['identificaciones'];
                $model->cufe=$df['cufe'];
                $numero = $df['numero'];
            }
            $model->facturaNC = $id;
            /*if(!$numeroFactura)
            {
                $consecutivoFactura = $oldFactura->numero + 1;
            }*/
            $model->numero = $consecutivoFactura;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id > 0 ? 0 : 1;

            $id_inscripcion = 0;
            $model->facturaNC = $id;
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;
            // $id_inscripcion=1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            
            $model->direccion = $factura['Contacto']['direccion'];
            $model->telefonoContacto = $factura['Contacto']['telefono'];
			
			//$model->id_estado_factura = $model->id_estado_factura;
			$model->tipo_compra = $factura['Factura'][0]['tipo_compra'];
            $model->periodo_pago = $factura['Factura'][0]['periodo_pago'] ? $factura['Factura'][0]['periodo_pago'] : 0;
            $model->fecha_vencimiento = $factura['Factura'][0]['fecha_vencimiento'];
            $model->id_impuesto = $factura['Factura'][0]['id_impuesto'];
            $model->id_medio_pago = $factura['Factura'][0]['id_medio_pago'];
            $model->orden_compra = $factura['Factura'][0]['orden_compra'];
            $model->fechaemisionordencompra = $factura['Factura'][0]['fechaemisionordencompra'];
            $model->numeroaceptacioninterno = $factura['Factura'][0]['numeroaceptacioninterno'];
            $model->facturaNumero = $numero;
			$model->id_serie = $factura["Factura"][0]["id_serie"];
			$model->id_moneda = $factura['Factura'][0]['id_moneda'];
            $model->trm = $factura['Factura'][0]['trm'] ? $factura['Factura'][0]['trm'] : 0;
			
            $model->fecha=Yii::$app->formatter->asDate('now', 'php:d/m/Y');

            
            return $this->render('_debitForm', [
                'model' => $model,
                'title'=>$numero,
                'detalle_factura'=>  $detalle_factura,
                'listProducto'=>$this->toList($id),
                'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList(),
                'listContacto'=>  Contactos::toList($model->id_empresa),
                'listEmpresas'=>  $this->tolistEmpresa(),
                'listPersonas'=> $this->tolistPersona()
				,'listImpuestos'=> Impuestos::toList()
				,'listMedioPago'=> MedioPago::toList()
				,'listTNDebito'=> TipoNota::toListDebito()
            ]);
        }
    }
    /**
     * Updates an existing Facturas model.
     * If update is successful, the browser will adbe redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    public function tolistEmpresa(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model= \app\models\Empresas::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    
     public function tolistPersona(){
         $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model= \app\models\Personas::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', function($model) {
        return $model['nombre'].' '.$model['apellido'];
    });
    }
    
    /**
     * Lista Clientes cuando el producto no es una inscripcion
     */
    public function toListClientes(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $sql = "SELECT CONCAT('p-',p.id) as ids,CONCAT(p.nombre, ' ',p.apellido) as nombres
                FROM personas p 
                LEFT join inscripciones i on (i.id_persona=p.id)
                where i.id_empresa IS NULL
                AND p.deleted=0 AND p.id_evento=".$event_id."
                UNION
                SELECT CONCAT('e-',id) as ids,nombre as nombres FROM `empresas` WHERE deleted=0 and id_evento=".$event_id." ORDER BY nombres ";
        
        $lista2=Yii::$app->db->createCommand($sql)->queryAll();
        

        return \yii\helpers\ArrayHelper::map($lista2, 'ids', 'nombres');
    }

    public function getInvoiceTotal($idFactura){
        $idFactura = $idFactura === NULL ? 0 : $idFactura;
        $where = "f.id=".$idFactura;
        $query = (new \yii\db\Query())
            ->select("f.total,rf.monto,rf.tipo")
            ->from('facturas f')
            ->leftJoin("relacion_nc_factura rf","rf.factura_id=f.id")
            ->where($where)->all();
        return count($query) > 0 ? $query : 0;
    }
    
    public function tolist($idInscripcion,$list=1){
//        $subQuery = Inscripciones::find()->select('id_empresa')->where(['id'=>$idInscripcion]);
//        $query = Inscripciones::find()->where(['=', 'id_empresa', $subQuery]);
////        $models = $query->all();
//        var_dump(\yii\helpers\ArrayHelper::map($models, 'id', 'id_empresa'));
        $sql = "SELECT CONCAT('i-',i.id) as id,CONCAT(SUBSTRING(pro.nombre,1,35),'-',p.nombre, ' ',p.apellido) as nombre "
                . "FROM inscripciones i "
                . "LEFT JOIN personas p ON (i.id_persona=p.id) "
                . "LEFT JOIN productos pro ON(i.id_producto=pro.id) "
                . "WHERE id_empresa =(SELECT id_empresa FROM `inscripciones` WHERE id=".$idInscripcion.")";
        
        $lista=Yii::$app->db->createCommand($sql)->queryAll();
        if(!count($lista))
        {
            $sql = "SELECT CONCAT('i-',i.id) as id,CONCAT(pro.nombre,'-',p.nombre, ' ',p.apellido) as nombre "
                . "FROM inscripciones i "
                . "LEFT JOIN personas p ON (i.id_persona=p.id) "
                . "LEFT JOIN productos pro ON(i.id_producto=pro.id) "
                . "WHERE i.id=".$idInscripcion;
            $lista=Yii::$app->db->createCommand($sql)->queryAll();
        }
        
        $sql = "SELECT CONCAT('p-',id) as id,SUBSTRING(pro.nombre,1,100) as nombre "
                . "FROM `productos` pro WHERE  activo = 'S' AND inscripciones='N' AND id <>8";
        
        $lista2=Yii::$app->db->createCommand($sql)->queryAll();

        $listas[] =  $lista;
        $listas[] =  $lista2;
        $result =  \yii\helpers\ArrayHelper::map($listas, 'id', 'nombre');
         //$result .=  \yii\helpers\ArrayHelper::map($lista2, 'id', 'nombre');
        //$result = $list == 1 ?  \yii\helpers\ArrayHelper::map($lista, 'id', 'nombre') : \yii\helpers\ArrayHelper::map($lista2, 'id', 'nombre');
        
        return $result;
        
    }   
    
    
    public function actionIndexMenu()
    {
        $searchModel = new InscripcionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
     
        return $this->render('index-menu', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
     public function actionVerificar()
    {
        return $this->render('verificar', [
        ]);
    }
    
    
    public static function actionDropdownLista($idInscripcion=0){
        if(Yii::$app->request->post('idInscripcion',''))
        {
            $idInscripcion =  Yii::$app->request->post('idInscripcion','');
            $linscrp = Yii::$app->request->post('listInscripcion','');
            $listInscripcion = $linscrp ? json_decode($linscrp) : 0;

            $in = array();
            $inscripcion='';
            if($listInscripcion)
            {
                 foreach ($listInscripcion as $ins){
                     array_push($in, $ins);
                 }
                  $inscripcion = join("','",$in); 
            }
            
            $sql = "SELECT CONCAT('i-',i.id) as id,CONCAT(SUBSTRING(pro.nombre,1,35),'-',p.nombre, ' ',p.apellido) as nombre "
            . "FROM inscripciones i "
            . "LEFT JOIN personas p ON (i.id_persona=p.id) "
            . "LEFT JOIN productos pro ON(i.id_producto=pro.id) "
            . "WHERE id_empresa =(SELECT id_empresa FROM `inscripciones` WHERE id=$idInscripcion) "
            . "AND i.id not in ('$inscripcion')"
            . " AND i.estado=1 AND i.is_presence=1";
    
            $lista=Yii::$app->db->createCommand($sql)->queryAll();
            if(!count($lista))
            {
                $sql = "SELECT CONCAT('i-',i.id) as id,CONCAT(pro.nombre,'-',p.nombre, ' ',p.apellido) as nombre "
                    . "FROM inscripciones i "
                    . "LEFT JOIN personas p ON (i.id_persona=p.id) "
                    . "LEFT JOIN productos pro ON(i.id_producto=pro.id) "
                    . "WHERE i.id_empresa is null "
                    . "AND i.id=".$idInscripcion
                    ." AND i.estado=1";
                $lista=Yii::$app->db->createCommand($sql)->queryAll();
            }
            
            $data  = \yii\helpers\ArrayHelper::map($lista, 'id', 'nombre');
            $data2 = FacturaController::actionDropdownListaPatrocinio(1);
            
            $result = $data + $data2;
           
            return json_encode($result);
        }

      } 
      
      
    public static function actionDropdownListaPatrocinio($id=0){
           
        //$linscrp = Yii::$app->request->post('listProducto','');
        //$listProducto  =   $linscrp ? json_decode($linscrp) : 0;
        $listProducto  =   0;
        $pro = array();
        $producto='';
        $session = Yii::$app->session;
        $evento = $session->get('event_id');


        if($listProducto)
        {
             foreach ($listProducto as $ins){
                 array_push($pro, $ins);
             }     
             $producto = join("','",$pro); 
        } 
       
        $sql = "SELECT CONCAT('p-',id) as id,SUBSTRING(pro.nombre,1,100) as nombre "
                . "FROM `productos` pro WHERE  activo = 'S' AND id <>8 AND id not in ('$producto') AND id_evento=".$evento;
        //var_dump($sql);die;
               // $sql = "SELECT CONCAT('i-',id) as id,SUBSTRING(pro.nombre,1,100) as nombre "
            //    . "FROM `productos` pro WHERE  activo = 'S' AND inscripciones='S'  AND id_evento=".$evento;
        
        $lista=Yii::$app->db->createCommand($sql)->queryAll();
        
        if($id)
            return \yii\helpers\ArrayHelper::map($lista, 'id', 'nombre');

        $respuesta['producto'] =\yii\helpers\ArrayHelper::map($lista, 'id', 'nombre');
        
        return json_encode($respuesta);
    } 

    /**
     * Deletes an existing Facturas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function safeModel($model){
        try {
            return $model->save();
         } catch (\Exception $e) {
             var_dump($e);die;
             return $e;
         }   
    }

    /**
     * Finds the Facturas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Facturas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Facturas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findModelInscripcion($id)
    {
        if (($model = \app\models\Inscripciones::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	protected function findModelDepartamento($id = "", $codigo = ""){
		if ($id && ($model = \app\models\Departamento::findOne($id)) !== null) {
            return $model;
        }else if (($codigo && $model = \app\models\Departamento::findOne(['codigo'=>$codigo])) !== null) {
			return $model;
		}else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
	}

    protected function findModelInformacionEmpresa($id = "")
    {
		if (($model = \app\models\InformacionEmpresa::find()->orderBy(['id' => SORT_DESC])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	protected function findModelEmpresas($id){
		if ($id && ($model = \app\models\Empresas::findOne($id)) !== null) {
            return $model;
		}
	}
	
	protected function findModelContacto($id){
		if ($id && ($model = \app\models\Contactos::findOne($id)) !== null) {
            return $model;
		}
	}
	
	protected function findModelTipoIdentificacion($id){
		if ($id && ($model = \app\models\TipoIdentificacion::findOne($id)) !== null) {
            return $model;
		}
	}

    protected function findModelDetalleFac($id)
    {
        if (($model = \app\models\DetalleFactura::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
	protected function findModelProducto($id){
		 if (($model = \app\models\Productos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
	}
		
    public function revertConsecutivo($serie_id,$consecutivo){
         $sql = "UPDATE parametros SET value = ".$consecutivo
                . " WHERE id_serie =".$serie_id;
    
        $lista=Yii::$app->db->createCommand($sql)->execute();
       
        //$model->modified_at
        return $lista;
    }
    
    public function actionDeleteAjax($id,$accion)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $factura=$this->findModel($id);
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $this->UpdateInscripcion($id);
            $this->_DeleteDetalleFactura($id);
            $this->_DeleteFactura($id);
            $this->revertConsecutivo($factura['id_serie'],$factura['numero']);
            $transaction->commit();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#evento_grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>3]];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
              $transaction->rollBack();
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>$e]];
         }   
    }
    
    /**
     * Anular Factura
     * @param type $id
     * @param type $accion
     * @return type
     */
    public function actionAnularAjax($id,$accion)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $this->UpdateInscripcion($id);
            $this->_DeleteDetalleFactura($id);
            $this->_AnularFactura($id);
            $transaction->commit();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#evento_grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>3]];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
              $transaction->rollBack();
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>$e]];
         }   
    }
    /**
     * Delete logical Factura
     * @param type $id
     */
    public function _AnularFactura($id){
        $sql = "UPDATE facturas SET deleted = 1,subtotal=0,iva=0,total=0,id_estado_factura=4"
                . " WHERE id =".$id;
    
        $lista=Yii::$app->db->createCommand($sql)->execute();
       
        //$model->modified_at
        return $lista;
    }
    /**
     * Delete logical Factura
     * @param type $id
     */
    public function _DeleteFactura($id){
        $sql = "DELETE FROM facturas "
                . "WHERE id =".$id;
    
        $lista=Yii::$app->db->createCommand($sql)->execute();
       
        //$model->modified_at
        return $lista;
    }
    
    public function UpdateInscripcion($id){
        $sql = "UPDATE inscripciones SET estado=1 WHERE id IN(SELECT id_inscripcion FROM detalle_factura "
                . "WHERE id_factura =".$id.")";
         $lista=Yii::$app->db->createCommand($sql)->execute();
       
        //$model->modified_at
        return $lista;
    }


    public function _DeleteDetalleFactura($id){
        $sql = "DELETE FROM detalle_factura "
                . "WHERE id_factura =".$id;
    
        $lista=Yii::$app->db->createCommand($sql)->execute();
       
        //$model->modified_at
        return $lista;
    }
    
    
     public static function actionDropdownContactos($id=0,$tipo=0){
        if(Yii::$app->request->post('id',''))
        {
            $id = Yii::$app->request->post('id');
            $tipo = Yii::$app->request->post('tipo');
            
            if($tipo=='e')
            {
                $sql = "SELECT id,nombre FROM contactos WHERE id_empresa=".$id;
                $lista=Yii::$app->db->createCommand($sql)->queryAll();
            }
            else
            {
                $lista=array();
            }
            
            return json_encode(\yii\helpers\ArrayHelper::map($lista, 'id', 'nombre'));
        }

      }
      
      public function actionGetContacto(){

        $id = Yii::$app->request->post('id');
        $tipo = Yii::$app->request->post('tipo');
        $diaFacturacion = '';
        //direccion de contacto
        if($tipo==1)
        {
            $contactos = Contactos::findOne($id);
            $respuesta= $contactos ? 1 : 0;
            $direccion = $respuesta ? $contactos->direccion : '';
            $telefono = $respuesta ? $contactos->telefono : '';
            $diaFacturacion = $respuesta ? $contactos->diamax_facturacion : '';
        }
        else  if($tipo==2)
        {
            $contactos = \app\models\Personas::findOne($id);
            $respuesta= $contactos ? 1 : 0;
            $direccion = $respuesta ? $contactos->direccion : '';
            $telefono = $respuesta ? $contactos->telefono : '';
        }
         else  if($tipo==3)
        {
            $contactos = \app\models\Empresas::findOne($id);
            $respuesta= $contactos ? 1 : 0;
            $direccion = $respuesta ? $contactos->direccion : '';
            $telefono = $respuesta ? $contactos->telefono : '';
            foreach ($contactos->contactos as $contacto)
            {
                if($contacto->diamax_facturacion!=NULL) {
                    $diaFacturacion = $contacto->diamax_facturacion;
                    break;
                }
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;

        
    return [['respuesta'=>$respuesta,
             'direccion'=>$direccion,
             'telefono'=>$telefono,
             'diaFacturacion'=>$diaFacturacion
           ]];          
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
     * 
     * @param type $orden_de_compra_idorden_de_compra
     * @return boolean
     */
    public function actionSendEmail()
    {
         if($this->isUserApproved && Yii::$app->user->can('facturacion')) {
            $searchModel = new InscripcionSearch();
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal
                    END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente order by estados";

            $modelEstadisticasTipo=Yii::$app->db->createCommand($sql)->queryAll();
            $valoresCount = array();
            $valoresFacturados = array();
            $valoresPagos= array();
            $valoresSinPagos= array();
            $valoresNC= array();
            $valoresCC=array();
            foreach ($modelEstadisticasTipo as $a)
            {
                array_push($valoresCount,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>1,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(ta.id)as inscritos,'FACTURADOS' as estados,22 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,'NO FACTURADOS' as estados,32 as idEstados,
                    (CASE 
                        WHEN  COUNT(*) > 0 THEN pr.valor * COUNT(*)
                        ELSE 0  END)  as subtotal 
                    FROM inscripciones i
                    INNER JOIN personas p on(i.id_persona=p.id)
                    LEFT JOIN productos pr ON(i.id_producto = pr.id)
                    LEFT JOIN detalle_factura df on(i.id=df.id_inscripcion)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE  i.is_presence=1 AND ta.facturable='SI' AND i.estado> 0 AND df.id is null AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>0]);
            }

            $sql = "SELECT count(distinct f.id)as inscritos,'NOTAS CREDITO' as estados,33 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                        FROM 
                        facturas f
                        inner join detalle_factura df on(f.id=df.id_factura)
                        where id_serie=3  and df.id_inscripcion >0 and id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresNC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql ="SELECT count(i.id) as inscritos,ef.nombre as estados,ef.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    INNER JOIN personas p ON(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    LEFT JOIN facturas f ON(i.id_factura=f.id)
INNER JOIN detalle_factura df ON (df.id_factura=f.id)
                    LEFT JOIN relacion_nc_factura nc ON(nc.factura_id=f.id)
                    LEFT JOIN estados_factura ef ON(ef.id=df.id_estado_factura)
                    WHERE p.id_evento = ".$event_id."
                    and ta.facturable='SI'
                    and ef.id is not null
and i.is_presence=1 
and nc.nc_id is null
                    GROUP BY ef.id order by 3";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND (ta.id=38 OR ta.id=37)
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar Afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=38
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            // cuenta de cobro no afiliados
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar No afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=37
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }

            $sql = "select nombre,sum(total) as total,sum(iva) as iva FROM (SELECT distinct numero,ef.nombre,f.id_estado_factura as estados,(CASE 
                    WHEN f.trm > 0 THEN f.subtotal * f.trm
                    ELSE f.subtotal  END) AS total,(CASE 
                        WHEN f.trm > 0 THEN f.iva * f.trm 
                        ELSE f.iva  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id.") as t  group by estados";

            $patrocinios=Yii::$app->db->createCommand($sql)->queryAll();

            $sql = "SELECT 'Facturados' as nombre,sum(CASE 
                    WHEN f.trm > 0 THEN df.subtotal * f.trm
                    ELSE df.subtotal  END) AS total,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id;

            $modelPatrociniosFacturados=Yii::$app->db->createCommand($sql)->queryAll();
            $patrociniosFacturados = array();
            foreach ($modelPatrociniosFacturados as $a) {
                array_push($patrociniosFacturados,["nombre"=>$a['nombre'],"valor"=>$a['total'],"iva"=>$a['iva']]);
                array_push($patrociniosFacturados,["nombre"=>'No Facturados',"valor"=>0,"iva"=>0]);
            }
            $sql = "SELECT (p.valor * COUNT(*)) as valor,4 as id,'NO FACTURADOS' as estados "
                    . "FROM inscripciones i "
                    . "LEFT JOIN productos p ON(i.id_producto = p.id) "
                    . "WHERE i.estado=1 AND p.id_evento = ".$event_id;

            $m=Yii::$app->db->createCommand($sql)->queryOne();
            $valoresEstadisticos = array();
            array_push($valoresEstadisticos,["id"=>$m['id'],"valor"=>$m['valor'],"estados"=>$m['estados']]);

            $sql = "SELECT sum(subtotal) as valor, ef.nombre as estados,ef.id as id "
                    . "FROM facturas f "
                    . "LEFT JOIN estados_factura ef ON(ef.id=f.id_estado_factura) "
                    . "WHERE is_patrocinios=0 and f.id_estado_factura is not NULL AND f.id_estado_factura < 4 and f.id_evento = ".$event_id
                    . " GROUP by id_estado_factura";

            $m2=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($m2 as $a)
            {
                array_push($valoresEstadisticos,["id"=>$a['id'],"valor"=>$a['valor'],"estados"=>$a['estados']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.facturable
                    FROM inscripciones i 
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE 
                      i.is_presence=1
AND i.estado> 0
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente";
            $inscritosTipo=Yii::$app->db->createCommand($sql)->queryAll();
           
       
            $txt = $this->renderPartial('estadisticas2', ['modelEstadisticasCount' => $valoresCount,'modelPatrocinios'=>$patrocinios,
            'modelEstadisticasSum' => $valoresEstadisticos,'pdf'=>0,'inscritosTipo'=>$inscritosTipo,
            'valoresFacturados'=>$valoresFacturados,'valoresSinPagos'=>$valoresSinPagos,'valoresPagos'=>$valoresPagos,
            'patrociniosFacturados'=>$patrociniosFacturados,'valoresCC'=>$valoresCC,'pdf'=>1
            ]);
            $to = \Yii::$app->params['EmailEstadisticas']; 
           $subject = 'Estadisticas Congreso 2019 Naturgas a la fecha : ' . date("m.d.y");
           
           $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
           $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    
            // Cabeceras adicionales
            $cabeceras .= 'To: Alberto Duque <duque.alberto@gmail.com>' . "\r\n";
            $cabeceras .= 'From: Recordatorio <desarrollo.tinger@gmail.com>' . "\r\n";
            if( mail($to,$subject,$txt,$cabeceras))
            {
                Yii::$app->session->setFlash('success', "Mensaje enviado con exito");
                 return $this->redirect(['estadisticas']);
            }
            else //pagina de error
            {
                 return $this->redirect(['estadisticas']);
            }
        }
    }


    public function getContactoFacturacion($factura){
        $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.email 
                            WHEN f.id_contacto > 0 THEN co.correo
                     END) AS correo,
                    e.nombre as empresa
                    FROM facturas f
                    LEFT JOIN contactos co ON(f.id_contacto=co.id)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura;
        $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();

        return $modelContacto;
    }
    
    
       public function actionGenerarPdf($id)
    {
        $facturaWsdl = new FacturaWsdl();
        $objClientDispapelesApis = new ClientDispapelesApi();

        $sql = "SELECT f.numero,f.fecha_transmision,f.tipo_factura,case WHEN f.id_serie = 1 THEN 'FENT' WHEN f.id_serie = 2 THEN 'CONT' ELSE 'FENT' END AS serie FROM facturas f WHERE id=" . $id;
        $factura = Yii::$app->db->createCommand($sql)->queryOne();
        if ($factura) {
            $fecha = date("Y-m-d", strtotime($factura['fecha_transmision']));
            $params['consecutivo'] = $factura['numero'];
            $params['fechafacturacion'] = $fecha;
            $params['tipodocumento'] = $factura['tipo_factura'] == 'FA' ? 1 : 2;
            $params['prefijo'] = $factura['tipo_factura'] == 'FA' ? $factura['serie'] : 'NCNT';
        }
        $xmlInvoice = $facturaWsdl->sendFile('5ce21110c1e2d29f448f9ee0a10f26fed0238d25', $params);
    
        $response = $objClientDispapelesApis->consultarPdfFactura($xmlInvoice);
        //var_dump($response);die;
        if ($response->return->consecutivo) {
            $ruta= 'facturas/Factura.pdf';
            $rutaweb = "../".$ruta;
            header("Content-type: application/pdf");
            header('Content-Disposition: attachment; filename="facturas/Factura.pdf"');
            $content = file_put_contents($ruta,$response->return->streamFile);
            echo $content;
           // $mpdf->Output($ruta,'F');

            //$partial = " <iframe src=\"$ruta\"></iframe>";
            $partial="<object data=\"$rutaweb\"#view=Fit\" type=\"application/pdf\" width=\"100%\" height=\"750\">
                    <p>
                        It appears your Web browser is not configured to display PDF files. No worries, just <a href=\"$rutaweb\">click here to download the PDF file.</a>
                    </p>
                </object>";
            return $partial;
            //echo $response->return->streamFile;
        }else
            echo "Error no se encontro ningun archivo";
    }
    /**
     * Crea el Pdf de la factura
     * @param type $id
     * @return type
     */
    public function actionGenerarPdfs($id){
        
        //$mpdf=new mPDF('utf-8', 'Letter-L', 6, 'freesans', 10, 10, 77,57, 0,0, 'L');
        $mpdf = new mPDF('','A4',0,'Arial','11','12','24','8','3','80', 'P');
       //  $mpdf=new mPDF('utf-8', 'Letter-L', 6, 'freesans', 10, 10, 0, 0,2,0, 'L');
        
        
         $sql = "SELECT f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                WHERE
                f.id=".$id;
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
       $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    concat(p.nombre,' ',p.apellido) as persona, 
                    e.nombre as empresa
                    FROM facturas f
                    LEFT JOIN contactos co ON(f.id_contacto=co.id)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    WHERE
                    f.id=".$id;
       
        $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();
       
        //var_dump($modelContacto);die;
        $sourcePath = '../web/css/pdf.css';
        $stylesheet = file_get_contents($sourcePath);
        //var_dump($modelContacto[0]['direccion']);die;
       //$my_htmls=$this->renderPartial('view_header', [
        //        'model' => $modelContacto
          //  ]);
       // $mpdf->setAutoTopMargin='stretch';
        //;
       // $mpdf->SetHTMLFooter($my_htmls2.'{PAGENO}', 'O', true);
        
       // $mpdf->SetHTMLHeader($my_htmls, 'O', true);
        //$mpdf->defaultheaderline=0;
        
        //$mpdf->writingHTMLheader($my_htmls, 'O', true);
       
        $mpdf->WriteHTML($stylesheet,1);
        
        $enLetras = new EnLetras();
        //var_dump($modelFactura);
        
        $my_html=$this->renderPartial('view_content_anexo', [
                'factura' => $modelFactura,'contacto'=>$modelContacto,'totalLetras'=> $enLetras->ValorEnLetras($modelFactura[0]['total'], ucfirst($modelFactura[0]['moneda'])),'simbolo'=>$modelFactura[0]['simbolo']
            ]);
         //  var_dump($my_html);die;
        $mpdf->WriteHTML($my_html,2);
        
        $htmlfoot = $this->renderPartial('view_oc', [
                'factura' => $modelFactura,'contacto'=>$modelContacto,'totalLetras'=> $enLetras->ValorEnLetras($modelFactura[0]['total'], ucfirst($modelFactura[0]['moneda'])),'simbolo'=>$modelFactura[0]['simbolo']
            ]);
            
            
            
        $mpdf->defaultfooterline=0;    
            
            
            
         
         $mpdf->setFooter($htmlfoot);
        //$mpdf->SetHTMLFooter('{PAGENO}', 'E', true);

        // var_dump($mpdf);die;
        //  $mpdf->DeletePages(7,7);
        $ruta= 'facturas/Factura.pdf';
        $rutaweb = "../".$ruta;
        $mpdf->Output($ruta,'F');
        
        //$partial = " <iframe src=\"$ruta\"></iframe>";
        $partial="<object data=\"$rutaweb\"#view=Fit\" type=\"application/pdf\" width=\"100%\" height=\"750\">
                    <p>
                        It appears your Web browser is not configured to display PDF files. No worries, just <a href=\"$rutaweb\">click here to download the PDF file.</a>
                    </p>
                </object>";
         return $partial;
    }


    public function getPagos($idFactura,$idInscripcion=0)
    {
        $sql = "SELECT SUM(valor) as pagos
                  FROM detalle_recibos
                    WHERE id_factura=".$idFactura;
        if($idInscripcion)
        {
            $sql .=   " AND id_inscripcion = ".$idInscripcion;
        }
                   
        $query=Yii::$app->db->createCommand($sql)->queryOne();

        return count($query) > 0 ? $query['pagos'] : 0;
    }
    
    public function getIsFacturaNull($idFactura)
    {
        $sql = "SELECT id
                  FROM relacion_nc_factura
                    WHERE factura_id=".$idFactura;
        
                   
        $query=Yii::$app->db->createCommand($sql)->queryOne();

        return count($query) > 0 ? $query['id'] : 0;
    }

    public function getFormaPago($idFactura,$idInscripcion=0)
    {
        $sql = "SELECT fp.nombre as pago
                  FROM detalle_recibos dr
                  LEFT JOIN formas_pago fp ON(dr.id_forma_pago=fp.id)
                    WHERE id_factura=".$idFactura.
            " AND id_inscripcion = ".$idInscripcion;
        $result=Yii::$app->db->createCommand($sql)->queryOne();

        return  $result['pago'] || '';
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
    
    public function actionGenerarSostenimiento(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $sql = "SELECT f.id as id_factura,pro.iva as prodIva,f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,
                df.valorTotal,pro.nombre as producto ,f.observaciones,e.nombre as empresa,e.identificacion as nit,ef.nombre as estados_factura,
                m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,cie.nombre as ciudad_empresa,f.tipo_factura,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT1111111'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE  df.id_inscripcion IS NULL AND f.id_serie = 1 AND f.id_evento=".$event_id." GROUP BY f.id";
        $modelPatrocinios=Yii::$app->db->createCommand($sql)->queryAll();

        $pila = array();
        $pila2 = array();
        $i=0;
        $fila = array();
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['empresa'] = "Compañia";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['estado_factura'] = "Estado Pago";
        $fila['estado_pago'] = "Estado Factura";
        array_push($pila2, $fila);

        foreach ($modelPatrocinios as $factura){
            $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN co.id_empresa IS NULL THEN '' 
                            WHEN co.id_empresa > 0 THEN co.correo
                     END) AS correo,
                    e.nombre as empresa,
                    e2.nombre as empresa2
                    FROM facturas f
                   LEFT JOIN contactos co ON(f.id_contacto=co.id)
                    LEFT JOIN empresas e2 on (co.id_empresa = e2.id)
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura['id_factura']." AND f.id_evento=".$event_id;
            $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();
            if($this->getIsFacturaNull($factura['id_factura'])==0)
            {
                $fila = array();
                $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
                $fila['empresa'] = $factura['empresa'] ? $factura['empresa'] : $modelContacto['empresa2'];
                $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ;
                $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ;
                $fila['nit'] = $factura['nit'];
                $fila['direccion'] = $modelContacto['direccion'];
                $fila['telefono_facturacion'] = $modelContacto['telefono'];
                $fila['correo_factutacion'] = $modelContacto['correo'];
                
                if($factura["serie"] !== 'NCNT')
                {
                    if ($factura["serie"])
                    {
                        $fila['subtotal'] = $factura['subtotal'];
                        $fila['iva'] =  $factura['iva'];
                        $fila['total'] = $factura['total'];
                    }
                    else{
                        $fila['subtotal'] = 'N/A';
                        $fila['iva'] = 'N/A';
                        $fila['total'] = 'N/A';
                    }
                }
                else
                {
                    $fila['subtotal'] = $factura['subtotal'] * -1;
                    $fila['iva'] =  $factura['iva'] * -1;
                    $fila['total'] = $factura['total']  * -1;
                    
                }
                
                $fila['pagado'] =   $this->getPagos($factura['id_factura']);
                $fila['serie'] = $factura['serie'];
                $fila['factura'] = $factura['numero'];
                $fila['estado_pago'] = $factura['tipo_factura'] == 'FA' ? $factura['estados_factura'] : '';
                $fila['estados_factura'] = $factura['tipo_factura'] == 'FA' ? "Facturado" : '';
                //$fila['ciudad_'] = $modelContacto['estados_factura'];
                array_push($pila2, $fila);
            }
        }

        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=informe-sostenimiento.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("sostenimiento_y_extraordinarias")
            ->fromArray(
                $pila2,  // The data to set
                NULL,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
            );
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }

    public function actionGenerarSostenimientonc(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $sql = "SELECT f.id as id_factura,pro.iva as prodIva,f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,
                df.valorTotal,pro.nombre as producto ,f.observaciones,e.nombre as empresa,e.identificacion as nit,ef.nombre as estados_factura,
                m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,cie.nombre as ciudad_empresa,f.tipo_factura,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE  df.id_inscripcion IS NULL AND f.id_serie = 3 AND f.id_evento=".$event_id." GROUP BY f.id";
        $modelPatrocinios=Yii::$app->db->createCommand($sql)->queryAll();

        $pila = array();
        $pila2 = array();
        $i=0;
        $fila = array();
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['empresa'] = "Compañia";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['estado_factura'] = "Estado Pago";
        $fila['estado_pago'] = "Estado Factura";
        array_push($pila2, $fila);

        foreach ($modelPatrocinios as $factura){
            $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN co.id_empresa IS NULL THEN '' 
                            WHEN co.id_empresa > 0 THEN co.correo
                     END) AS correo,
                    e.nombre as empresa,
                    e2.nombre as empresa2
                    FROM facturas f
                   LEFT JOIN contactos co ON(f.id_contacto=co.id)
                    LEFT JOIN empresas e2 on (co.id_empresa = e2.id)
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura['id_factura']." AND f.id_evento=".$event_id;
            $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();
            if($this->getIsFacturaNull($factura['id_factura'])==0)
            {
                $fila = array();
                $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
                $fila['empresa'] = $factura['empresa'] ? $factura['empresa'] : $modelContacto['empresa2'];
                $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ;
                $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ;
                $fila['nit'] = $factura['nit'];
                $fila['direccion'] = $modelContacto['direccion'];
                $fila['telefono_facturacion'] = $modelContacto['telefono'];
                $fila['correo_factutacion'] = $modelContacto['correo'];
                
                if($factura["serie"] !== 'NCNT')
                {
                    if ($factura["serie"])
                    {
                        $fila['subtotal'] = $factura['subtotal'];
                        $fila['iva'] =  $factura['iva'];
                        $fila['total'] = $factura['total'];
                    }
                    else{
                        $fila['subtotal'] = 'N/A';
                        $fila['iva'] = 'N/A';
                        $fila['total'] = 'N/A';
                    }
                }
                else
                {
                    $fila['subtotal'] = $factura['subtotal'] * -1;
                    $fila['iva'] =  $factura['iva'] * -1;
                    $fila['total'] = $factura['total']  * -1;
                    
                }
                
                $fila['pagado'] =   $this->getPagos($factura['id_factura']);
                $fila['serie'] = $factura['serie'];
                $fila['factura'] = $factura['numero'];
                $fila['estado_pago'] = $factura['tipo_factura'] == 'FA' ? $factura['estados_factura'] : '';
                $fila['estados_factura'] = $factura['tipo_factura'] == 'FA' ? "Facturado" : '';
                //$fila['ciudad_'] = $modelContacto['estados_factura'];
                array_push($pila2, $fila);
            }
        }

        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=informe-nc.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("sostenimiento_nc")
            ->fromArray(
                $pila2,  // The data to set
                NULL,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
            );
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }
    
    public function actionGenerarExcel(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        
        $sql = "SELECT f.id as id_factura,f.numero,pro.iva as prodIva,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,cie.nombre as ciudad_empresa,i.id as idInscripcion,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,p.id_tipo_asistente,i.is_presence,df.nc_id
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=0 AND (i.is_presence = 1 OR (i.is_presence = 0 AND f.id > 0)) AND f.id_evento=".$event_id;
        
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        $sql = "SELECT f.id as id_factura,pro.iva as prodIva,f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,
               cie.nombre as ciudad_empresa,(CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=1 AND df.id_producto=18 AND f.id_evento=".$event_id." GROUP BY f.id"  ;
        
        $modelPatrocinios=Yii::$app->db->createCommand($sql)->queryAll();
        
        
        $pila = array();
        $pila2 = array();
        $i=0;
        $fila = array();
        
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['tipo_participante'] = "Tipo Pax";
        $fila['no_asistio'] = "No Asistio";
        $fila['nombre'] = "Nombre"; 
        $fila['apellido'] = "Apellido"; 
        $fila['cedula'] = "Cedula";
        $fila['empresa'] = "Compañia";
        $fila['cargo'] = "Cargo";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['telefono'] = "Telefono";
        $fila['correo'] = "Correo";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['contacto'] = "Contacto";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['fecha_factura'] = "Fecha Factura";
        $fila['estado_factura'] = "Estado Pago";
        $fila['estado_pago'] = "Estado Factura";
        $fila['nc_id'] = "Nota Credito";
        array_push($pila, $fila);
        
        $fila = array();
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['empresa'] = "Compañia";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['estado_factura'] = "Estado Pago";
        $fila['estado_pago'] = "Estado Factura";
        $fila['nc_id'] = "Nota Credito";
        array_push($pila2, $fila);
        
        foreach ($modelFactura as $factura){
            $modelContacto = $this->getContactoFacturacion($factura['id_factura']);
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['no_asistio'] = $factura['is_presence'] ? 'ASISTIO' : 'NO ASISTIO';
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['contacto'] = $modelContacto['contacto'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo']; 
            if($factura["serie"] !== 'NCNT')
            {
                if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
                {
                    $fila['subtotal'] = $factura['valor'];
                    $fila['iva'] = ($factura['valor'] * $factura['prodIva'] / 100);
                    $fila['total'] = ($fila['iva'] + $factura['valor']);
                }
                else{
                    $fila['subtotal'] = 'N/A';
                    $fila['iva'] = 'N/A';
                    $fila['total'] = 'N/A';
                }
            }
            else
            {
                $fila['subtotal'] = ($factura['valor'] * -1);
                $fila['iva'] = (($factura['valor'] * $factura['prodIva'] / 100)* -1);
                $fila['total'] = $fila['iva'] - $factura['valor'];
                
            }
            //$fila['subtotal'] = $factura['valor'];
            //$fila['iva'] = $factura['valor'] * $factura['prodIva'] / 100;
            //$fila['total'] = $factura['valor'] +  $fila['iva'];
            $fila['pagado'] =  $factura['idInscripcion'] ? $this->getPagos($factura['id_factura'],$factura['idInscripcion']) : $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura['serie'];
            $fila['factura'] = $factura['numero']; 
            $fila['fecha_factura'] = $factura['fecha'];
            $fila['estados_factura'] = "Facturado";
            $fila['estado_pago'] = $factura['estados_factura'];
            $fila['nc_id'] = $factura['nc_id'];
            $fila['forma_pago'] = $this->getFormaPago($factura['id_factura'],0);
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
              
             array_push($pila, $fila);
             
        }
        //sin facturas
        $sql = "SELECT i.id as idInscripcion,pr.iva as prodIva,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,e.direccion as direccion,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,p.id_tipo_asistente,i.is_presence
				FROM inscripciones i
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (i.id_empresa = e.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN productos pr on(pr.id=i.id_producto)
                where i.estado=1  AND i.is_presence = 1 AND pr.id_evento=".$event_id;
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
        foreach ($modelFactura as $factura){
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['no_asistio'] = $factura['is_presence'] ? 'ASISTIO' : 'NO ASISTIO';
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad']; 
            $fila['pais'] = $factura['pais']; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $factura['direccion'];
            $fila['contacto'] = $factura['nombre']."".$factura['apellido'];
            $fila['telefono_facturacion'] = $factura['telefono'];
            $fila['correo_factutacion'] = $factura['email']; 
            $pagos = $this->getTotalInscripciones($factura['idInscripcion']);
            if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
            {
                $fila['subtotal'] = $pagos['valor'];
                $fila['iva'] = ($pagos['valor'] * $factura['prodIva'] / 100);
                $fila['total'] = ($fila['iva'] + $pagos['valor']);
                $fila['serie'] = $factura['serie'];
            }
            else{
                $fila['subtotal'] = 'N/A';
                $fila['iva'] = 'N/A';
                $fila['total'] = 'N/A';
                $fila['serie'] = 'N/A';
            }

            $fila['pagado'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';
            $fila['factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A'; 
            $fila['fecha_factura'] = ' ';
            $fila['estados_factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? "No Facturado" : 'N/A';
            $fila['estado_pago'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';  
            array_push($pila, $fila);
             
        }
        
        
        foreach ($modelPatrocinios as $factura){
             $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN co.id_empresa IS NULL THEN '' 
                            WHEN co.id_empresa > 0 THEN co.correo
                     END) AS correo,
                    em.nombre as empresa
                    FROM facturas f
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN contactos co ON(e.id=co.id_empresa)
                    LEFT JOIN contactos con on (f.id_contacto = con.id)
                    LEFT JOIN empresas em on(em.id=con.id_empresa)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura['id_factura']." AND f.id_evento=".$event_id;
            $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['empresa'] = $factura['empresa'] ? $factura['empresa'] : $modelContacto['empresa'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo']; 
            $fila['subtotal'] = $factura["serie"] !== 'NCNT' ? $factura['subtotal'] : ($factura['subtotal'] * -1);
            $fila['iva'] = $factura["serie"] !== 'NCNT' ? $factura['iva'] : ($factura['iva'] * -1);
            $fila['total'] = $factura["serie"] !== 'NCNT' ? $factura['total'] : ($factura['total'] * -1);
            $fila['pagado'] =   $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura["serie"];
            $fila['factura'] = $factura['numero']; 
            $fila['estados_factura'] = "Facturado"; 
            $fila['estado_pago'] = $factura['estados_factura'];  
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
              
            array_push($pila2, $fila);
             
        }
        
        
        
        
        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=informe-general.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("Participantes")
        ->fromArray(
        $pila,  // The data to set
        NULL,        // Array values with this value will not be set
        'A1'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );
                
         $spreadsheet->createSheet();
         $spreadsheet->setActiveSheetIndex(1); 
                
                 $spreadsheet->getActiveSheet()->setTitle("Patrocinios")
        ->fromArray(
        $pila2,  // The data to set
        NULL,        // Array values with this value will not be set
        'A1'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
    );
                
       
//
//$spreadsheet = new Spreadsheet();
//$sheet = $spreadsheet->getActiveSheet();
//$sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
       //60 filas
        //filtro funcione 
        //que el estado se actualize
        //
        $writer->save('php://output');
       // print file_get_contents("informe-general.xlsx");
        
    }



    public function getFacturas($id){
        $sql = "SELECT f.numero,f.subtotal,f.total,f.iva,f.fecha
				, f.id_serie
				, id_impuesto, f.tipo_compra, f.periodo_pago, f.fecha_vencimiento, f.orden_compra, f.fechaemisionordencompra, f.numeroaceptacioninterno
				, f.id_medio_pago
				,df.cantidad,df.valor,df.subtotal as valorSubtotal,df.valorTotal,df.iva as valorIva
				,pro.nombre as producto ,f.observaciones,df.id as dfId,
            (CASE
                WHEN f.id_persona IS NULL THEN e.nombre 
                WHEN f.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,df.observacion as descripcion,
             df.id_producto,df.id_inscripcion,f.id_contacto,f.id_empresa,f.cufe,
             (CASE
                WHEN f.id_persona IS NULL THEN e.identificacion 
                WHEN f.id_persona > 0 THEN p.identificacion
             END) AS identificaciones,f.id_moneda,f.trm,f.id_persona
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                WHERE 
                f.id=".$id;
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();

        $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    concat(p.nombre,' ',p.apellido) as persona, 
                    e.nombre as empresa
                    FROM facturas f
                    LEFT JOIN contactos co ON(f.id_contacto=co.id)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    WHERE
                    f.id=".$id;

        $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();

        $model['Factura'] = $modelFactura;
        $model['Contacto'] = $modelContacto;
        return $model;
    }
    
    
    /**
     * Crea el Pdf de la estadisticas
     * @param type $id
     * @return type
     */
  
    public function actionGenerarEstadisticasPdf(){
        //$mpdf=new mPDF(['mode' => 'utf-8', 'format' => 'A4-L','orientation' => 'L']);
        
       
        if($this->isUserApproved && Yii::$app->user->can('facturacion')) {
            $searchModel = new InscripcionSearch();
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal
                    END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente order by estados";

            $modelEstadisticasTipo=Yii::$app->db->createCommand($sql)->queryAll();
            $valoresCount = array();
            $valoresFacturados = array();
            $valoresPagos= array();
            $valoresSinPagos= array();
            $valoresNC= array();
            $valoresCC=array();
            foreach ($modelEstadisticasTipo as $a)
            {
                array_push($valoresCount,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>1,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(ta.id)as inscritos,'FACTURADOS' as estados,22 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum( CASE WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100 
                         WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                    ELSE 0 END)  as iva
                    FROM facturas f 
                    LEFT JOIN detalle_factura df on(df.id_factura=f.id )
                    LEFT JOIN inscripciones i on(df.id_inscripcion=i.id)
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE ta.facturable='SI'
                     AND f.id_serie <>3
                     AND i.is_presence=1
                     AND df.nc_id is null
                     AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,'NO FACTURADOS' as estados,32 as idEstados,
                    (CASE 
                        WHEN  COUNT(*) > 0 THEN pr.valor * COUNT(*)
                        ELSE 0  END)  as subtotal 
                    FROM inscripciones i
                    INNER JOIN personas p on(i.id_persona=p.id)
                    LEFT JOIN productos pr ON(i.id_producto = pr.id)
                    LEFT JOIN detalle_factura df on(i.id=df.id_inscripcion)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE  i.is_presence=1 AND ta.facturable='SI' AND i.estado> 0 AND df.id is null AND p.id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresFacturados,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>0]);
            }

            $sql = "SELECT count(distinct f.id)as inscritos,'NOTAS CREDITO' as estados,33 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                        FROM 
                        facturas f
                        inner join detalle_factura df on(f.id=df.id_factura)
                        where id_serie=3  and df.id_inscripcion >0 and id_evento=".$event_id;
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresNC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }

            $sql ="SELECT count(i.id) as inscritos,ef.nombre as estados,ef.id as idEstados,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    INNER JOIN personas p ON(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    LEFT JOIN facturas f ON(i.id_factura=f.id)
INNER JOIN detalle_factura df ON (df.id_factura=f.id)
                    LEFT JOIN relacion_nc_factura nc ON(nc.factura_id=f.id)
                    LEFT JOIN estados_factura ef ON(ef.id=df.id_estado_factura)
                    WHERE p.id_evento = ".$event_id."
                    and ta.facturable='SI'
                    and ef.id is not null
and i.is_presence=1 
and nc.nc_id is null
                    GROUP BY ef.id order by 3";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'],"iva"=>$a['iva']]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND (ta.id=38 OR ta.id=37)
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresPagos,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar Afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=38
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }
            // cuenta de cobro no afiliados
            $sql = "SELECT count(f.id) as inscritos,'Cuentas por cobrar No afiliado' as estados,43 as idEstados ,sum(CASE 
                        WHEN f.trm > 0 THEN df.subtotal * f.trm
                        ELSE df.subtotal END)  as subtotal,sum(CASE 
                        WHEN df.iva > 0 AND f.trm > 0 THEN df.iva * df.subtotal * f.trm / 100
                        WHEN df.iva > 0 AND f.trm IS NULL THEN df.iva * df.subtotal / 100 
                        ELSE 0  END)  as iva
                    FROM inscripciones i
                    inner join personas p on(i.id_persona=p.id)
                    LEFT JOIN detalle_recibos dr ON(i.id=dr.id_inscripcion)
                    inner join tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    left join facturas f on(i.id_factura=f.id)
                    left join detalle_factura df on(i.id=df.id_inscripcion)
                    left join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where ta.facturable='SI'
                     and i.is_presence=1 
                     and nc.nc_id is null
                     and p.id_evento=".$event_id."
                    and f.id_serie <>3
                    and df.nc_id is null
                    AND i.estado> 0
                    AND ta.id=37
                    and dr.id_inscripcion is null";
            $modelEstadisticasCount=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($modelEstadisticasCount as $a)
            {
                array_push($valoresCC,["inscritos"=>$a['inscritos'],"estados"=>$a['estados'],"idEstados"=>$a['idEstados'],"isCount"=>0,"valor"=>$a['subtotal'] ? $a['subtotal'] : 0,"iva"=>$a['iva'] ? $a['iva'] : 0]);
            }

            $sql = "select nombre,sum(total) as total,sum(iva) as iva FROM (SELECT distinct numero,ef.nombre,f.id_estado_factura as estados,(CASE 
                    WHEN f.trm > 0 THEN f.subtotal * f.trm
                    ELSE f.subtotal  END) AS total,(CASE 
                        WHEN f.trm > 0 THEN f.iva * f.trm 
                        ELSE f.iva  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id.") as t  group by estados";

            $patrocinios=Yii::$app->db->createCommand($sql)->queryAll();

            $sql = "SELECT 'Facturados' as nombre,sum(CASE 
                    WHEN f.trm > 0 THEN df.subtotal * f.trm
                    ELSE df.subtotal  END) AS total,sum(CASE 
                        WHEN df.iva > 0 THEN df.iva * df.subtotal / 100
                        ELSE 0  END)  as iva
                    FROM facturas f
                     INNER JOIN detalle_factura df on(f.id=df.id_factura)
                     INNER JOIN estados_factura ef on(ef.id=f.id_estado_factura)
                     LEFT join relacion_nc_factura nc on(nc.factura_id=f.id)
                    where f.id_serie <>3
                     AND df.id_inscripcion is null
                     AND df.nc_id is null
                     AND f.id_evento=".$event_id;

            $modelPatrociniosFacturados=Yii::$app->db->createCommand($sql)->queryAll();
            $patrociniosFacturados = array();
            foreach ($modelPatrociniosFacturados as $a) {
                array_push($patrociniosFacturados,["nombre"=>$a['nombre'],"valor"=>$a['total'],"iva"=>$a['iva']]);
                array_push($patrociniosFacturados,["nombre"=>'No Facturados',"valor"=>0,"iva"=>0]);
            }
            $sql = "SELECT (p.valor * COUNT(*)) as valor,4 as id,'NO FACTURADOS' as estados "
                    . "FROM inscripciones i "
                    . "LEFT JOIN productos p ON(i.id_producto = p.id) "
                    . "WHERE i.estado=1 AND p.id_evento = ".$event_id;

            $m=Yii::$app->db->createCommand($sql)->queryOne();
            $valoresEstadisticos = array();
            array_push($valoresEstadisticos,["id"=>$m['id'],"valor"=>$m['valor'],"estados"=>$m['estados']]);

            $sql = "SELECT sum(subtotal) as valor, ef.nombre as estados,ef.id as id "
                    . "FROM facturas f "
                    . "LEFT JOIN estados_factura ef ON(ef.id=f.id_estado_factura) "
                    . "WHERE is_patrocinios=0 and f.id_estado_factura is not NULL AND f.id_estado_factura < 4 and f.id_evento = ".$event_id
                    . " GROUP by id_estado_factura";

            $m2=Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($m2 as $a)
            {
                array_push($valoresEstadisticos,["id"=>$a['id'],"valor"=>$a['valor'],"estados"=>$a['estados']]);
            }

            $sql = "SELECT count(ta.id) as inscritos,ta.nombre as estados,ta.facturable
                    FROM inscripciones i 
                    INNER JOIN personas p on(i.id_persona=p.id)
                    INNER JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                    WHERE 
                      i.is_presence=1
AND i.estado> 0
                     AND p.id_evento=".$event_id."
                    GROUP BY p.id_tipo_asistente";
            $inscritosTipo=Yii::$app->db->createCommand($sql)->queryAll();
           
       
        $my_html= $this->renderPartial('estadisticas2', ['modelEstadisticasCount' => $valoresCount,'modelPatrocinios'=>$patrocinios,
        'modelEstadisticasSum' => $valoresEstadisticos,'pdf'=>0,'inscritosTipo'=>$inscritosTipo,
        'valoresFacturados'=>$valoresFacturados,'valoresSinPagos'=>$valoresSinPagos,'valoresPagos'=>$valoresPagos,
        'patrociniosFacturados'=>$patrociniosFacturados,'valoresCC'=>$valoresCC,'pdf'=>1
        ]);
        $mpdf = new mPDF('','A4',0,'Arial','11','12','24','8','15','8', 'L');
        $sourcePath = 'tablepdf.css';
       
        $mpdf->WriteHTML($my_html);
        $ruta= 'facturas/estadisticas.pdf';
        $rutaweb = "../".$ruta;
        $mpdf->Output($ruta,'F');
        $partial="<object data=\"$rutaweb\"#view=Fit\" type=\"application/pdf\" width=\"100%\" height=\"750\">
                    <p>
                        It appears your Web browser is not configured to display PDF files. No worries, just <a href=\"$rutaweb\">click here to download the PDF file.</a>
                    </p>
                </object>";
         return $partial;
        }else{
            throw new ForbiddenHttpException;
        }
    }
    
    public function calculaDigitoVerificador($rut) {
        $n1 = substr($rut,0,3);
        $n2 = substr($rut,3,3);
        $n3 = substr($rut,-3);
        $rutFinal = $n1.".".$n2.".".$n3;
        $vpri = array();
        $x=0 ; $y=0 ; $z=strlen($rut);
        $vpri[1]=3;
        $vpri[2]=7;
        $vpri[3]=13;
        $vpri[4]=17;
        $vpri[5]=19;
        $vpri[6]=23;
        $vpri[7]=29;
        $vpri[8]=37;
        $vpri[9]=41;
        $vpri[10]=43;
        $vpri[11]=47;
        $vpri[12]=53;
        $vpri[13]=59;
        $vpri[14]=67;
        $vpri[15]=71;
        for($i=0 ; $i<$z ; $i++)
        {
            $y=($rut.substr($i,1));
            $x+=($y*$vpri[$z-$i]);
        }
        $y=$x%11;
        if ($y > 1)
        {
            $dv1=11-$y;
        } else {
            $dv1=$y;
        }
        $rutFinal .= "-".$dv1;
        return $rutFinal;
    }



    public static function actionDropdownFacturas($id=0,$tipo=0){
        if(Yii::$app->request->post('id',''))
        {
            $id = Yii::$app->request->post('id');
            $sql = "SELECT * FROM `facturas` WHERE  cufe is NOT NULL AND tipo_factura='FA' AND id_empresa =".$id;
            $lista=Yii::$app->db->createCommand($sql)->queryAll();
            if(empty($lista))
            {
                $lista=array();
            }

            return json_encode(\yii\helpers\ArrayHelper::map($lista, 'id', 'numero'));
        }

    }



    public function actionGetDetalleFactura(){

        $ids = Yii::$app->request->post('id');
        $ids = explode(",", $ids);
        $i=0;
        $model=new Facturas();
        foreach ($ids as $id) {
            $factura = $this->getFacturas($id);
            $ban=false;
            //var_dump($factura);die;
            foreach ($factura['Factura'] as $df) {
                $detalle_facturas[$i] = new DetalleFactura();
                $detalle_facturas[$i]->id = $df['dfId'];
                $detalle_facturas[$i]->id_factura = $id;
                $detalle_facturas[$i]->id_producto = $df['id_producto'];
                $detalle_facturas[$i]->id_inscripcion = $df['id_inscripcion'];
                $detalle_facturas[$i]->cantidad = $df['cantidad'];
                $detalle_facturas[$i]->subtotal = floatval(str_replace(",", "", $df['valorSubtotal']));
                $detalle_facturas[$i]->valorTotal = $df['valorTotal'];
                $detalle_facturas[$i]->valor = floatval(str_replace(",", "", $df['valorSubtotal']));
                $detalle_facturas[$i]->observacion = $df['producto'].'--'.$df['descripcion'];
                $detalle_facturas[$i]->iva = $df['valorIva'];
                if(!$ban) {
                    $model->subtotal += $df['subtotal'];
                    $model->iva += $df['iva'];
                    $model->total += $df['total'];
                    $ban=true;
                }
                $i++;
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;


        return ['respuesta'=>$detalle_facturas,'model'=>$model];
    }
    
    public function actionRetransmitirFactura($id)
    {
        if(!$this->isUserApproved || !Yii::$app->user->can('facturacion')){
            throw new ForbiddenHttpException;
        }
        $model = new Facturas();
        $model->id_estado_factura=1;
        $model->observaciones="";
        $count = count(Yii::$app->request->post('DetalleFactura', []));
		    $model->cantidadLineas = $count;
		    if($count){
            for($i = 1; $i < $count; $i++) {
                $detalle_facturas[] = new DetalleFactura();
            }
        }
        else{
            $detalle_facturas = [new DetalleFactura()];
        }
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {
                
                $detalle_factura = Yii::$app->request->post('DetalleFactura');
                $count = count($detalle_factura);
                $model->cantidadLineas = $count;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
				
				        $client = explode("-",$model->clientes);
                $client[0]=='p' ? $model->id_persona =  $client[1] : $model->id_empresa =  $client[1];
				
				        $model->tipoidentificacion  = $model->idEmpresa->tipoIdentificacion->codigo;
				        $model->identificacion = $model->id_empresa ? $model->idEmpresa->identificacion : $model->idPersona->identificacion; 
                $model->verificacion = $model->id_empresa ? $model->idEmpresa->verificacion : ""; 
                $model->clientes=$model->id_empresa ? $model->idEmpresa->nombre : $model->idPersona->nombre.' '.$model->idPersona->apellido;
                $model->fecha = $this->FormatoFechas($model->fecha);
                $model->fecha_vencimiento = $this->FormatoFechas($model->fecha_vencimiento);
                $model->fechaemisionordencompra = $model->fechaemisionordencompra;
                $session = Yii::$app->session;
                $event_id = $session->get('event_id');
                $model->id_evento = $event_id;
                $model->subtotal=str_replace(",","",$model->subtotal);
                $model->total=str_replace(",","",$model->total);
                $model->iva=str_replace(",","",$model->iva);
                $model->tipo_factura = 'FA';
                $model->id_serie = 1;
                $model->trm= $model->trm ? floatval(str_replace(",","",$model->trm)) : 0;

                if($model->validate() && $detalle_factura){
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $this->safeModel($model);
                        $this->_DeleteDetalleFactura($id);
                        $i=0;
                        foreach ($detalle_factura as $df) {
                            $detalle_facturas = new DetalleFactura();
                            $detalle_facturas->id_factura=$id;
                            $detalle_facturas->cantidad=$df['cantidad'];
                            $detalle_facturas->subtotal=floatval(str_replace(",","",$df['subtotal']));
                            $detalle_facturas->valorTotal=floatval(str_replace(",","",$df['valorTotal']));    
                            $detalle_facturas->valor=floatval(str_replace(",","",$df['subtotal']));
                            $detalle_facturas->observacion=$df['descripcion'];
                            $detalle_facturas->id_estado_factura = 1;
                            $detalle_facturas->iva=$df['iva'];
                            
                            $modelProductos = $this->findModelProducto($df["id_producto"]);
                            $detalle_factura[$i]["tipo_codigo_producto"] = $modelProductos->tipo_codigo_producto;
                            $detalle_factura[$i]["nombre_producto"] = $modelProductos->nombre;
                            $detalle_factura[$i]["tipo_impuesto"] = $modelProductos->tipo_impuesto;
                            $detalle_factura[$i]["descripcion"] = $detalle_facturas->observacion;

                            if($df['id_inscripcion']){
                                $v = explode("-",$df['id_inscripcion']);
                                $detalle_facturas->id_inscripcion=  $v[0] == 'i' ? intval($v[1]) : null;
                                if($df['id_inscripcion']>0 && $detalle_facturas->id_inscripcion==null)
                                  $detalle_facturas->id_inscripcion=$df['id_inscripcion'];
                                if($v[0] == 'i'){
                                    $modelDetalleRecibos = \app\models\DetalleRecibos::find()->where(['id_inscripcion'=>$v[1]])->all();
                                    foreach ($modelDetalleRecibos as $modelDetalleRecibo){
                                        $modelDetalleRecibo->id_factura = $id;
                                        $modelDetalleRecibo->save(false);
                                    }
                                    $inscripciones = $this->findModelInscripcion($detalle_facturas->id_inscripcion);
                                    $id_producto = $inscripciones->id_producto;
                                    $inscripciones->estado = 2;
                                    $inscripciones->id_factura = $id;
                                    $inscripciones->save(false);
                                }
                                else {
                                  $inscripciones = $this->findModelInscripcion($detalle_facturas->id_inscripcion);
                                  $detalle_factura[$i]["nombre_producto"] = $modelProductos->nombre.'\n'.$inscripciones->idPersona->nombre.' '.$inscripciones->idPersona->apellido;
                                }
                            }
                            if($df["id_producto"]){
                                $detalle_facturas->id_producto=$df["id_producto"];
                            }
                            else {
                                $detalle_facturas->id_producto= $v[0] == 'i' ? $id_producto : intval($v[1]);
                            }
                            if($detalle_facturas->validate()){
                                $detalle_facturas->save(false);
                            } 
                            else{
                                $transaction->rollBack();
                                return [['respuesta'=>70,'error'=>$detalle_facturas->getErrors(),'id'=>$detalle_facturas->id_factura]];
                            }
                            $i++;
                        }
                        $transaction->commit();
                        try {
                            $facturaWsdl = new FacturaWsdl();
                            $objClientDispapelesApis = new ClientDispapelesApi();
                            $informacionEmpresa = $this->findModelInformacionEmpresa();
                            if($model->id_empresa) { // validar para cuando es solo personas 
                                $modelEmpresa = $this->findModelEmpresas($model->id_empresa);
                                $model->ciudad = $modelEmpresa->id_ciudad;
                                $modelCiudad = $this->findModelDepartamento($model->ciudad);
                                $modelDepartamento = $this->findModelDepartamento($modelCiudad->id_padre);
                                $model->departamento = $modelDepartamento->id;
                                $model->departamentoNombre = $modelDepartamento->nombre;
                            }
                            $model->identificacionFormat = $this->getTipoIdentification($model) ? $this->calculaDigitoVerificador($model->identificacion) : '';
                                          //$model->identificacionFormat = $this->calculaDigitoVerificador($model->identificacion);
                            $model->tipoDocumento= 1; //Factura de Venta
                            $model->orden_compra = ""; 
                            $model->fechaemisionordencompra = date("Y-m-d"); 
                            $model->numeroaceptacioninterno = ""; 
                            $model->id_impuesto = 1; //01 = IVA
                            $modelFactura = $this->findModel($id);
                            $params = $facturaWsdl->loadNC($model, $detalle_factura,$informacionEmpresa); // token antigiuo e2556e2f2dc65b60653fab4fc380996647363a01
                            
                            $xmlInvoice = $facturaWsdl->createFactura('a676eeac3c09745ae19d35f952b94e942df9afae', $params); //Desarrollo
                            $response = $objClientDispapelesApis->enviarFactura($xmlInvoice);
							              $modelFactura->send_xml = json_encode((array)$xmlInvoice);
						                $modelFactura->respuesta = json_encode((array)$response->return);
                            if($response->return->estadoProceso == 0){
							                  $modelFactura->save(false);
								                $listaMensajesProceso = "";
                                foreach($response->return->listaMensajesProceso as $row){
                                  if($row->rechazoNotificacion == "R"){
                                    $listaMensajesProceso .= $row->descripcionMensaje."\n";
                                  }
                                }
                                return [[
                                  'respuesta'=>4
                                  ,'id' => $id
                                  ,"descripcionProceso" =>" 1. ".$response->return->descripcionProceso
                                  ,"listaMensajesProceso" => $listaMensajesProceso
                                  ,'redirect'=>Url::toRoute(['/factura/facturados'])
                                ]];
                            }
                            else{
                              //if ($response->return->mensaje == 'OK') {
                              if ($response->return->estadoProceso == 1) {
                                $modelFactura->cufe = $response->return->cufe;
                                $modelFactura->fecha_transmision = $response->return->fechaFactura;
                                $modelFactura->save(false);
                                return [['respuesta'=>1,'id' => $id,'redirect'=>Url::toRoute(['/factura/facturados'])]];
                              }
                              else
                              {
                                $modelFactura->save(false);
                                return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
                              }
                            }
                        }catch (\SoapFault $e){
                            return [['respuesta'=>2,'error' => 'error','redirect'=>Url::toRoute(['/factura/facturados'])]];
							          }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];
                    }
                }
                else {
                    return [['respuesta'=>0,'data'=> \yii\widgets\ActiveForm::validate($model)]];

                }
            } else {
                echo "0";
            }
        }
        else {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
            $factura = $this->getFacturas($id);
            $id_inscripcion = 0;
            foreach ($factura['Factura'] as $i=>$df) {
              $detalle_facturas[$i] = new DetalleFactura();
              $detalle_facturas[$i]->id=$df['dfId'];
              $detalle_facturas[$i]->id_factura=$id;
              $detalle_facturas[$i]->id_producto=$df['id_producto'];
              $detalle_facturas[$i]->id_inscripcion=$df['id_inscripcion'];	
              $detalle_facturas[$i]->cantidad=$df['cantidad'];
              $detalle_facturas[$i]->subtotal=floatval(str_replace(",","",$df['valorSubtotal']));
              $detalle_facturas[$i]->valorTotal=$df['valorTotal'];
              $detalle_facturas[$i]->valor=floatval(str_replace(",","",$df['valorSubtotal']));
              $detalle_facturas[$i]->descripcion=$df['descripcion'];
              $detalle_facturas[$i]->iva=$df['valorIva'];
              $nombre = $df['id_inscripcion'] ? "-".$detalle_facturas[$i]->idInscripcion->idPersona->nombre." ".$detalle_facturas[$i]->idInscripcion->idPersona->apellido : '';
              $detalle_facturas[$i]->producto=$df['producto'].$nombre;
              $model->subtotal = $df['subtotal'];
              $model->iva = $df['iva'];
              $model->total = $df['total'];
              $model->id_contacto = $df['id_contacto'];
              $model->id_empresa=$df['id_empresa'];
              $model->clientes= $df['id_empresa'] ? 'e-'.$df['id_empresa'] : 'p-'.$df['id_persona'];
              $model->identificacion=$df['identificaciones'];
              $model->cufe=$df['cufe'];
              $numero = $df['numero'];
              //$id_inscripcion = $df['id_inscripcion'] ? $df['id_inscripcion'] : 0;
              $contactosList = $df['id_persona'] ? \yii\helpers\ArrayHelper::map(array("0"=>array("id"=>$model->clientes,"nombre"=>"IGUAL AL CLIENTE")), 'id', 'nombre') : Contactos::toList($df['id_empresa']);
              //var_dump(\yii\helpers\ArrayHelper::map(array("0"=>array("id"=>$model->clientes,"nombre"=>"IGUAL AL CLIENTE")), 'id', 'nombre'));die;
            }
            $model->numero =  $factura['Factura'][0]['numero'];
            //Si No tiene inscripcion viene de patrocinio
            $model->is_patrocinios = $id_inscripcion > 0 ? 0 : 1;
            $inscripcion =  $id_inscripcion ? $this->findModelInscripcion($id_inscripcion) : new Inscripciones();
            $id_empresa = $inscripcion->id_empresa ? $inscripcion->id_empresa : 0;
            $model->direccion = $factura['Contacto']['direccion'];
            $model->telefonoContacto = $factura['Contacto']['telefono'];
            $model->tipo_compra = $factura['Factura'][0]['tipo_compra'];
            $model->id_moneda = $factura['Factura'][0]['id_moneda'];
            $model->trm = $factura['Factura'][0]['trm'];
            $model->periodo_pago = $factura['Factura'][0]['periodo_pago'] ? $factura['Factura'][0]['periodo_pago'] : 0 ;
            
            $model->fecha_vencimiento = $factura['Factura'][0]['fecha_vencimiento'] == '0000-00-00' ? '' : Yii::$app->formatter->asDate($factura['Factura'][0]['fecha_vencimiento'], 'php:d/m/Y');
            $model->id_impuesto = $factura['Factura'][0]['id_impuesto'];
            $model->id_medio_pago = $factura['Factura'][0]['id_medio_pago'];
            $model->orden_compra = $factura['Factura'][0]['orden_compra'];
            $model->fechaemisionordencompra = $factura['Factura'][0]['fechaemisionordencompra'];
            $model->numeroaceptacioninterno = $factura['Factura'][0]['numeroaceptacioninterno'];
            $model->facturaNumero = $numero;
            $model->id_serie = $factura["Factura"][0]["id_serie"];
            $model->fecha =Yii::$app->formatter->asDate( $factura['Factura'][0]['fecha'], 'php:d/m/Y');
            $model->fecha_factura=Yii::$app->formatter->asDate('01/01/1900', 'php:d/m/Y');
        
            /*var_dump($factura);
            print "<hr>";
            var_dump($model);
            exit();*/
            return $this->render('_editForm', [
                'model' => $model,
                'title' => $numero,
                'detalle_factura'=>  $detalle_facturas,
                'listProducto'=>  $this->toList($id_inscripcion),
                'listClientes'=> $this->toListClientes(),
                'listMoneda'=>  Monedas::toList(),
                'listContacto'=>  $contactosList,
                'listEmpresas'=>  $this->tolistEmpresa(),
                'listPersonas'=> $this->tolistPersona()
                ,'listImpuestos'=> Impuestos::toList()
                ,'listMedioPago'=> MedioPago::toList()
                ,'listTNCredito'=> TipoNota::toListCredito()
            ]);
        }
        
    }
    
     
    public function actionGenerarExcelParticipantes(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        
        $sql = "SELECT f.id as id_factura,f.numero,pro.iva as prodIva,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,cie.nombre as ciudad_empresa,i.id as idInscripcion,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,p.id_tipo_asistente,i.is_presence,df.nc_id,f.trm,f.id_moneda,m.simbolo
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=0 AND (f.id_serie = 1 OR f.id_serie= 2) AND (i.is_presence = 1 OR (i.is_presence = 0 AND f.id > 0)) AND f.id_evento=".$event_id;
        
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
        $pila = array();
        $i=0;
        $fila = array();
        
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['tipo_participante'] = "Tipo Pax";
        $fila['no_asistio'] = "No Asistio";
        $fila['nombre'] = "Nombre"; 
        $fila['apellido'] = "Apellido"; 
        $fila['cedula'] = "Cedula";
        $fila['empresa'] = "Compañia";
        $fila['cargo'] = "Cargo";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['telefono'] = "Telefono";
        $fila['correo'] = "Correo";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['contacto'] = "Contacto";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['moneda'] = "Moneda";
        $fila['trm'] = "TRM";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['fecha_factura'] = "Fecha Factura";
        $fila['estado_factura'] = "Estado Factura";
        $fila['estado_pago'] = "Estado Pago";
        $fila['nc_id'] = "NC/ND";
        array_push($pila, $fila);
        
        foreach ($modelFactura as $factura){
            $modelContacto = $this->getContactoFacturacion($factura['id_factura']);
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['no_asistio'] = $factura['is_presence'] ? 'ASISTIO' : 'NO ASISTIO';
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['contacto'] = $modelContacto['contacto'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo'];
            $trm = $factura['id_moneda'] <> 1 && $factura['trm']? $factura['trm'] : 1;
            $fila['moneda'] = $factura['simbolo'];
            $fila['trm'] = $factura['trm'];
            if($factura["serie"] !== 'NCNT')
            {
                if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
                {
                    $fila['subtotal'] = $factura['valor'] * $trm;
                    $fila['iva'] = ($factura['valor'] * $factura['prodIva'] / 100) * $trm;
                    $fila['total'] = ($fila['iva'] + $fila['subtotal']);
                }
                else{
                    $fila['subtotal'] = 'N/A';
                    $fila['iva'] = 'N/A';
                    $fila['total'] = 'N/A';
                }
            }
            else
            {
                $fila['subtotal'] = ($factura['valor'] * -1) * $trm;
                $fila['iva'] = (($factura['valor'] * $factura['prodIva'] / 100)* -1) * $trm;
                $fila['total'] = $fila['iva'] + $fila['subtotal'];
                
            }
            //$fila['subtotal'] = $factura['valor'];
            //$fila['iva'] = $factura['valor'] * $factura['prodIva'] / 100;
            //$fila['total'] = $factura['valor'] +  $fila['iva'];
            $fila['pagado'] =  $factura['idInscripcion'] ? $this->getPagos($factura['id_factura'],$factura['idInscripcion']) : $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura['serie'];
            $fila['factura'] = $factura['numero']; 
            $fila['fecha_factura'] = $factura['fecha'];
            $fila['estados_factura'] = "Facturado";
            $fila['estado_pago'] = $factura['estados_factura'];  
            // $fila['forma_pago'] = $this->getFormaPago($factura['id_factura'],0);
            if($factura['nc_id']){
                $modelFactura = $this->findModel($factura['nc_id']);
            }
            $fila['nc_id'] = $factura['nc_id'] ? 'NCNT-'.$modelFactura->numero : '';
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
              
             array_push($pila, $fila);
             
        }
        //sin facturas
        $sql = "SELECT i.id as idInscripcion,pr.iva as prodIva,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,e.direccion as direccion,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,p.id_tipo_asistente,i.is_presence
				FROM inscripciones i
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (i.id_empresa = e.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN productos pr on(pr.id=i.id_producto)
                where i.estado=1 AND i.is_presence = 1 AND pr.id_evento=".$event_id;
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
        foreach ($modelFactura as $factura){
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['no_asistio'] = $factura['is_presence'] ? 'ASISTIO' : 'NO ASISTIO';
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad']; 
            $fila['pais'] = $factura['pais']; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $factura['direccion'];
            $fila['contacto'] = $factura['nombre']."".$factura['apellido'];
            $fila['telefono_facturacion'] = $factura['telefono'];
            $fila['correo_factutacion'] = $factura['email'];
            $fila['moneda'] = 'N/A';
            $fila['trm']='';
            $pagos = $this->getTotalInscripciones($factura['idInscripcion']);
            
            if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48){
                $fila['subtotal'] = $pagos['valor'];
                $fila['iva'] = ($pagos['valor'] * $factura['prodIva'] / 100);
                $fila['total'] = ($fila['iva'] + $pagos['valor']);
                $fila['serie'] = $factura['serie'];
            }else{
                $fila['subtotal'] = 'N/A';
                $fila['iva'] = 'N/A';
                $fila['total'] = 'N/A';
                $fila['serie'] = 'N/A';
            }
            
            $fila['pagado'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';
            $fila['factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A'; 
            $fila['fecha_factura'] = ' ';
            $fila['estados_factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? "No Facturado" : 'N/A';
            $fila['estado_pago'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';
            $fila['nc_id'] = '';
            array_push($pila, $fila);
             
        }
        
        
        
        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=inscritos-evento.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("Participantes")
        ->fromArray($pila,NULL,'A1' );
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
    }

    public function actionGenerarExcelAsistentes(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        
        $sql = "SELECT f.id as id_factura,f.numero,pro.iva as prodIva,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,
                df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,
                p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,
                p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,
             cie.nombre as ciudad_empresa,i.id as idInscripcion,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,p.id_tipo_asistente,i.is_presence,df.nc_id,f.trm,f.id_moneda
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=0 AND (f.id_serie = 1 OR f.id_serie= 2) AND i.is_presence = 1 AND f.id_evento=".$event_id;
        
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
        $pila = array();
        $i=0;
        $fila = array();
        
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['tipo_participante'] = "Tipo Pax";
        $fila['nombre'] = "Nombre"; 
        $fila['apellido'] = "Apellido"; 
        $fila['cedula'] = "Cedula";
        $fila['empresa'] = "Compañia";
        $fila['cargo'] = "Cargo";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['telefono'] = "Telefono";
        $fila['correo'] = "Correo";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['contacto'] = "Contacto";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['fecha_factura'] = "Fecha Factura";
        $fila['estado_factura'] = "Estado Factura";
        $fila['estado_pago'] = "Estado Pago";
        //$fila['nc_id'] = "NC/ND";
        array_push($pila, $fila);
        
        foreach ($modelFactura as $factura){
            $modelContacto = $this->getContactoFacturacion($factura['id_factura']);
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['contacto'] = $modelContacto['contacto'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo'];
            $trm = $factura['id_moneda'] <> 1 && $factura['trm']? $factura['trm'] : 1;
            if($factura["serie"] !== 'NCNT')
            {
                if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
                {
                    $fila['subtotal'] = $factura['valor'] * $trm;
                    $fila['iva'] = ($factura['valor'] * $factura['prodIva'] / 100) * $trm;
                    $fila['total'] = ($fila['iva'] + $fila['subtotal']);
                }
                else{
                    $fila['subtotal'] = 'N/A';
                    $fila['iva'] = 'N/A';
                    $fila['total'] = 'N/A';
                }
            }
            else
            {
                $fila['subtotal'] = ($factura['valor'] * -1) * $trm;
                $fila['iva'] = (($factura['valor'] * $factura['prodIva'] / 100)* -1) * $trm;
                $fila['total'] = $fila['iva'] + $fila['subtotal'];
                
            }
            //$fila['subtotal'] = $factura['valor'];
            //$fila['iva'] = $factura['valor'] * $factura['prodIva'] / 100;
            //$fila['total'] = $factura['valor'] +  $fila['iva'];
            $fila['pagado'] =  $factura['idInscripcion'] ? $this->getPagos($factura['id_factura'],$factura['idInscripcion']) : $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura['serie'];
            $fila['factura'] = $factura['numero']; 
            $fila['fecha_factura'] = $factura['fecha'];
            $fila['estados_factura'] = "Facturado";
            $fila['estado_pago'] = $factura['estados_factura'];
            $fila['forma_pago'] = $this->getFormaPago($factura['id_factura'],0);
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
            //$fila['nc_id'] = $factura['nc_id'] ? 'NCNT-'.$factura['nc_id'] : '';

            array_push($pila, $fila);
             
        }
        //sin facturas
        $sql = "SELECT i.id as idInscripcion,pr.iva as prodIva,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,e.direccion as direccion,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,p.id_tipo_asistente,i.is_presence
				FROM inscripciones i
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (i.id_empresa = e.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN productos pr on(pr.id=i.id_producto)
                where i.estado=1  AND i.is_presence = 1 AND pr.id_evento=".$event_id;
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        
        foreach ($modelFactura as $factura){
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad']; 
            $fila['pais'] = $factura['pais']; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $factura['direccion'];
            $fila['contacto'] = $factura['nombre']."".$factura['apellido'];
            $fila['telefono_facturacion'] = $factura['telefono'];
            $fila['correo_factutacion'] = $factura['email']; 
            $pagos = $this->getTotalInscripciones($factura['idInscripcion']);
            if($factura["serie"] !== 'NCNT')
            {
                if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
                {
                    $fila['subtotal'] = $pagos['valor'];
                    $fila['iva'] = ($pagos['valor'] * $factura['prodIva'] / 100);
                    $fila['total'] = ($fila['iva'] + $pagos['valor']);
                    $fila['serie'] = $factura['serie'];
                }
                else{
                    $fila['subtotal'] = 'N/A';
                    $fila['iva'] = 'N/A';
                    $fila['total'] = 'N/A';
                    $fila['serie'] = 'N/A';
                }
            }
            else
            {
                $fila['subtotal'] = ($pagos['valor'] * -1);
                $fila['iva'] = (($pagos['valor'] * $factura['prodIva'] / 100)* -1);
                $fila['total'] = (($fila['iva'] + $pagos['valor'])* -1);
                
            }
            $fila['pagado'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';
            $fila['factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A'; 
            $fila['fecha_factura'] = ' ';
            $fila['estados_factura'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? "No Facturado" : 'N/A';
            $fila['estado_pago'] = $factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48 ? ' ' : 'N/A';
            // $fila['nc_id'] = '';
            array_push($pila, $fila);
             
        }
        
        
        
        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=asistentes-evento.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("Asistentes")
        ->fromArray($pila,NULL,'A1' );
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
    }
    
    public function actionGenerarExcelPatrocinios(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $sql = "SELECT f.id as id_factura,pro.iva as prodIva,f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,
               cie.nombre as ciudad_empresa,(CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,df.nc_id,m.simbolo,f.trm
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=1 AND df.id_producto=18 AND f.id_serie <3 AND f.id_evento=".$event_id." GROUP BY f.id"  ;
        
        $modelPatrocinios=Yii::$app->db->createCommand($sql)->queryAll();
        $pila2 = array();
        $i=0;
        $fila = array();
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['empresa'] = "Compañia";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['moneda'] = 'Moneda';
        $fila['trm']='TRM';
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        $fila['serie'] = "Serie";
        $fila['factura'] = "Factura";
        $fila['estado_factura'] = "Estado Pago";
        $fila['estado_pago'] = "Estado Factura";
        $fila['nc_id'] = "NC/ND";
        array_push($pila2, $fila);
            
        foreach ($modelPatrocinios as $factura){
             $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN co.id_empresa IS NULL THEN '' 
                            WHEN co.id_empresa > 0 THEN co.correo
                     END) AS correo,
                    em.nombre as empresa
                    FROM facturas f
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN contactos co ON(e.id=co.id_empresa)
                    LEFT JOIN contactos con on (f.id_contacto = con.id)
                    LEFT JOIN empresas em on(em.id=con.id_empresa)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura['id_factura']." AND f.id_evento=".$event_id;
            $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['empresa'] = $factura['empresa'] ? $factura['empresa'] : $modelContacto['empresa'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo'];
            $fila['moneda'] = $factura["simbolo"];
            $fila['trm']=$factura["trm"];
            $fila['subtotal'] = $factura["serie"] !== 'NCNT' ? $factura['subtotal'] : ($factura['subtotal'] * -1);
            $fila['iva'] = $factura["serie"] !== 'NCNT' ? $factura['iva'] : ($factura['iva'] * -1);
            $fila['total'] = $factura["serie"] !== 'NCNT' ? $factura['total'] : ($factura['total'] * -1);
            $fila['serie'] = $factura["serie"];
            $fila['factura'] = $factura['numero']; 
            $fila['estados_factura'] = "Facturado"; 
            $fila['estado_pago'] = $factura['estados_factura'];
            if($factura['nc_id']){
                $modelFactura = $this->findModel($factura['nc_id']);
            }
            $fila['nc_id'] = $factura['nc_id'] ? 'NCNT-'.$modelFactura->numero : '';
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
              
            array_push($pila2, $fila);
             
        }
        
        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=eventos-patrocinios.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("Patrocinios")
        ->fromArray($pila2,NULL,'A1' );
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
    }

    public function actionGenerarExcelNc(){
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        
        $sql = "SELECT f.id as id_factura,f.numero,pro.iva as prodIva,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,cie.nombre as ciudad_empresa,i.id as idInscripcion,
               (CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,p.id_tipo_asistente,i.is_presence,nc.factura_id,m.simbolo,f.trm,f.id_moneda
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
                LEFT JOIN relacion_nc_factura nc on(nc.nc_id=f.id)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=0 AND f.id_serie = 3 AND (i.is_presence = 1 OR (i.is_presence = 0 AND f.id > 0)) AND f.id_evento=".$event_id;
        
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        $sql = "SELECT f.id as id_factura,pro.iva as prodIva,f.numero,f.subtotal,f.total,f.iva,f.fecha,df.cantidad,df.valor,df.subtotal as valorsubtotal,df.valorTotal,pro.nombre as producto ,f.observaciones,ta.nombre as participante,e.nombre as empresa,p.identificacion as cedula,ca.nombre as cargo,p.email,e.identificacion as nit,ef.nombre as estados_factura,i.created_at as fecha_inscripcion,pa.nombre as pais,ci.nombre as ciudad,p.nombre,p.apellido,p.telefono,
            (CASE
                WHEN i.id_persona IS NULL THEN e.nombre 
                WHEN i.id_persona > 0 THEN concat(p.nombre,' ',p.apellido)
             END) AS cliente, concat(p.nombre,' ',p.apellido) as persona,m.nombre as moneda , m.simbolo as simbolo,f.fecha as fecha_empresa,pae.nombre as pais_empresa,
               cie.nombre as ciudad_empresa,(CASE
                            WHEN f.id_serie = 1   THEN 'FENT'
                            WHEN f.id_serie = 2   THEN 'CONT'
                            WHEN f.id_serie = 3   THEN 'NCNT'
                     END) AS serie,nc.factura_id,f.id_moneda,f.trm,m.simbolo
				FROM facturas f
                LEFT JOIN detalle_factura df ON(f.id=df.id_factura)
                LEFT JOIN relacion_nc_factura nc on(nc.nc_id=f.id)
              	LEFT JOIN productos pro ON (df.id_producto=pro.id)
                LEFT JOIN inscripciones i ON (df.id_inscripcion=i.id)
                LEFT JOIN personas p ON (i.id_persona=p.id)
                LEFT JOIN empresas e ON (f.id_empresa = e.id)
                LEFT JOIN monedas m on (f.id_moneda=m.id)
                LEFT JOIN tipo_asistentes ta on(p.id_tipo_asistente=ta.id)
                LEFT JOIN cargos ca on (p.id_cargo=ca.id)
                LEFT JOIN estados_factura ef on(f.id_estado_factura=ef.id)
                LEFT JOIN ciudad ci on(p.id_ciudad=ci.id)
                LEFT JOIN pais pa on(ci.id_pais=pa.id)
                LEFT JOIN ciudad cie on(e.id_ciudad=cie.id)
                LEFT JOIN pais pae on(cie.id_pais=pae.id)
                WHERE f.is_patrocinios=1 AND f.id_serie = 3 AND df.id_producto=18 AND f.id_evento=".$event_id." GROUP BY f.id"  ;
        
        $modelPatrocinios=Yii::$app->db->createCommand($sql)->queryAll();
        
        
        $pila = array();
        $pila2 = array();
        $i=0;
        $fila = array();
        
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['tipo_participante'] = "Tipo Pax";
        $fila['no_asistio'] = "No Asistio";
        $fila['nombre'] = "Nombre"; 
        $fila['apellido'] = "Apellido"; 
        $fila['cedula'] = "Cedula";
        $fila['empresa'] = "Compañia";
        $fila['cargo'] = "Cargo";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['telefono'] = "Telefono";
        $fila['correo'] = "Correo";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['contacto'] = "Contacto";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['moneda'] = 'Moneda';
        $fila['trm']='TRM';
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        //$fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "NC";
        $fila['fecha_factura'] = "Fecha NC";
        $fila['facturaNumero'] = "Factura";
        array_push($pila, $fila);
        
        $fila = array();
        $fila['fecha_inscripcion'] = "Fecha";
        $fila['empresa'] = "Compañia";
        $fila['ciudad'] = "Ciudad";
        $fila['pais'] = "Pais";
        $fila['nit'] = "Nit";
        $fila['direccion'] = "Dirección";
        $fila['telefono_facturacion'] = "Telefono Facturación";
        $fila['correo_factutacion'] = "Correo_Contacto";
        $fila['moneda'] = 'Moneda';
        $fila['trm']='TRM';
        $fila['subtotal'] = "Subtotal";
        $fila['iva'] = "Iva";
        $fila['total'] = "Total";
        //$fila['pagado'] = "Valor Pagado";
        $fila['serie'] = "Serie";
        $fila['factura'] = "NC";
        $fila['facturaNumero'] = "Factura";
        array_push($pila2, $fila);
        
        foreach ($modelFactura as $factura){
            $modelContacto = $this->getContactoFacturacion($factura['id_factura']);
           
            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['tipo_participante'] = $factura['participante'];
            $fila['no_asistio'] = $factura['is_presence'] ? 'ASISTIO' : 'NO ASISTIO';
            $fila['nombre'] = $factura['nombre']; 
            $fila['apellido'] = $factura['apellido']; 
            $fila['cedula'] = $factura['cedula']; 
            $fila['empresa'] = $factura['empresa']; 
            $fila['cargo'] = $factura['cargo'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ; 
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ; 
            $fila['telefono'] = $factura['telefono']; 
            $fila['correo'] = $factura['email']; 
            $fila['nit'] = $factura['nit']; 
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['contacto'] = $modelContacto['contacto'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo'];
            $fila['moneda'] = $factura['simbolo'];
            $fila['trm']=$factura['trm'];
            $trm = $factura['id_moneda'] <> 1 && $factura['trm']? $factura['trm'] : 1;
            if($factura["serie"] !== 'NCNT')
            {
                if ($factura['id_tipo_asistente'] == 37 || $factura['id_tipo_asistente'] == 38 || $factura['id_tipo_asistente'] == 39 || $factura['id_tipo_asistente'] == 48)
                {
                    $fila['subtotal'] = $factura['valor'] * $trm;
                    $fila['iva'] = ($factura['valor'] * $factura['prodIva'] / 100) * $trm;
                    $fila['total'] = $fila['iva'] + $fila['subtotal'];
                }
                else{
                    $fila['subtotal'] = 'N/A';
                    $fila['iva'] = 'N/A';
                    $fila['total'] = 'N/A';
                }
            }
            else
            {
                $fila['subtotal'] = $factura['valor'] * $trm;
                $fila['iva'] = ($factura['valor'] * $factura['prodIva'] / 100) * $trm;
                $fila['total'] = $fila['iva'] + $fila['subtotal'];
                
            }
            //$fila['subtotal'] = $factura['valor'];
            //$fila['iva'] = $factura['valor'] * $factura['prodIva'] / 100;
            //$fila['total'] = $factura['valor'] +  $fila['iva'];
            //$fila['pagado'] =  $factura['idInscripcion'] ? $this->getPagos($factura['id_factura'],$factura['idInscripcion']) : $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura['serie'];
            $fila['factura'] = $factura['numero']; 
            $fila['fecha_factura'] = $factura['fecha'];
            if($factura['factura_id']){
                $modelFactura = $this->findModel($factura['factura_id']);
                $serie = $modelFactura->id_serie ==1 ? 'FENT' : 'CONT';
            }
            $fila['facturaNumero'] = $factura['factura_id'] ? $serie.'-'.$modelFactura->numero : '';
            //$fila['ciudad_'] = $modelContacto['estados_factura'];
              
             array_push($pila, $fila);
             
        }

        foreach ($modelPatrocinios as $factura){
             $sql = "SELECT
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(e.direccion) 
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(p.direccion) 
                            WHEN f.id_contacto > 0 THEN co.direccion
                     END) AS direccion,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN e.telefono 
                     	    WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN p.telefono 
                            WHEN f.id_contacto > 0 THEN co.telefono
                     END) AS telefono,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN ''
                            WHEN f.id_contacto > 0 THEN co.telefono_extension
                     END) AS extension,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN p.movil
                            WHEN f.id_contacto > 0 THEN co.movil
                     END) AS movil,
                    (CASE
                            WHEN f.id_contacto IS NULL AND f.id_persona IS NULL THEN UPPER(cie.nombre)
                     		WHEN f.id_contacto IS NULL AND f.id_empresa IS NULL THEN UPPER(ci.nombre) 
                            WHEN f.id_contacto > 0 THEN UPPER(cic.nombre)
                     END) AS ciudad,
                     (CASE
                            WHEN f.id_contacto IS NULL THEN pa.nombre
                            WHEN f.id_contacto > 0 THEN pac.nombre
                     END) AS pais,
                    IFNULL(e.identificacion,0) as identificacion_empresa,
                    IFNULL(p.identificacion,0) as identificacion_persona,
                    (CASE
                            WHEN f.id_contacto IS NULL THEN concat(p.nombre,' ',p.apellido)  
                            WHEN f.id_contacto > 0 THEN co.nombre
                     END) AS contacto,
                     (CASE
                            WHEN co.id_empresa IS NULL THEN '' 
                            WHEN co.id_empresa > 0 THEN co.correo
                     END) AS correo,
                    em.nombre as empresa
                    FROM facturas f
                    LEFT JOIN empresas e on (f.id_empresa = e.id)
                    LEFT JOIN contactos co ON(e.id=co.id_empresa)
                    LEFT JOIN contactos con on (f.id_contacto = con.id)
                    LEFT JOIN empresas em on(em.id=con.id_empresa)
                    LEFT JOIN personas p ON(f.id_persona=p.id)
                    LEFT JOIN ciudad ci ON(p.id_ciudad=ci.id)
                    LEFT JOIN pais pa ON(ci.id_pais=pa.id)
                    LEFT JOIN ciudad cic ON(co.id_ciudad=cic.id)
                    LEFT JOIN pais pac ON(cic.id_pais=pac.id)
                    LEFT JOIN ciudad cie ON(e.id_ciudad=cie.id)
                    LEFT JOIN pais pae ON(cie.id_pais=pae.id)
                    WHERE
                    f.id=".$factura['id_factura']." AND f.id_evento=".$event_id;
            $modelContacto=Yii::$app->db->createCommand($sql)->queryOne();

            $fila = array();
            $fila['fecha_inscripcion'] = $factura['fecha_inscripcion'] ?  $factura['fecha_inscripcion'] : $factura['fecha_empresa'];
            $fila['empresa'] = $factura['empresa'] ? $factura['empresa'] : $modelContacto['empresa'];
            $fila['ciudad'] = $factura['ciudad'] ? $factura['ciudad']  :  $factura['ciudad_empresa']  ;
            $fila['pais'] = $factura['pais'] ? $factura['pais']  :  $factura['pais_empresa'] ;
            $fila['nit'] = $factura['nit'];
            $fila['direccion'] = $modelContacto['direccion'];
            $fila['telefono_facturacion'] = $modelContacto['telefono'];
            $fila['correo_factutacion'] = $modelContacto['correo'];
            $fila['moneda'] = $factura['simbolo'];
            $fila['trm']=$factura['trm'];
            $trm = $factura['id_moneda'] <> 1 && $factura['trm']? $factura['trm'] : 1;
            $fila['subtotal'] = $factura["serie"] !== 'NCNT' ? $factura['subtotal'] * $trm : $factura['subtotal'] * $trm;
            $fila['iva'] = $factura["serie"] !== 'NCNT' ? $factura['iva'] * $trm : ($factura['iva'] * $trm);
            $fila['total'] = $factura["serie"] !== 'NCNT' ? $factura['total'] * $trm : ($factura['total'] * $trm );
            //$fila['pagado'] =   $this->getPagos($factura['id_factura']);
            $fila['serie'] = $factura["serie"];
            $fila['factura'] = $factura['numero'];
            if($factura['factura_id']){
                $modelFactura = $this->findModel($factura['factura_id']);
                $serie = $modelFactura->id_serie ==1 ? 'FENT' : 'CONT';
            }
            $fila['facturaNumero'] = $factura['factura_id'] ? $serie.'-'.$modelFactura->numero : '';
            //$fila['ciudad_'] = $modelContacto['estados_factura'];

            array_push($pila2, $fila);

        }




        $spreadsheet = new Spreadsheet();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=nc-eventos.xlsx");
        header("Content-Transfer-Encoding: binary ");
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setTitle("Participantes")
        ->fromArray(
        $pila,  // The data to set
        NULL,        // Array values with this value will not be set
        'A1'         // Top left coordinate of the worksheet range where
                     //    we want to set these values (default is A1)
        );
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1); 
               
        $spreadsheet->getActiveSheet()->setTitle("Patrocinios")
            ->fromArray(
            $pila2,  // The data to set
            NULL,        // Array values with this value will not be set
            'A1'         // Top left coordinate of the worksheet range where
                            //    we want to set these values (default is A1)
        );

       $writer = new Xlsx($spreadsheet);
       $writer->save('php://output');
    }
    
}