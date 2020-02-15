<?php

namespace app\controllers;

use Yii;
use app\models\Eventos;
use app\models\Pais;
use app\models\Ciudad;
use app\models\Contactos;
use app\models\Personas;
use app\models\Facturas;
use app\models\Inscripciones;
use app\models\Empresas;
use app\models\SectoresEmpresas;
use app\models\EventosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
/**
 * EventoController implements the CRUD actions for Eventos model.
 */
class EventoController extends Controller
{
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

    /**
     * Lists all Eventos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Eventos model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Eventos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pais=0)
    {
        $model = new Eventos();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->validate()){
                $model->fecha_hora_inicio = $this->FormatoFechas($model->fecha_hora_inicio);
                $model->fecha_hora_fin = $this->FormatoFechas($model->fecha_hora_fin);
                if($this->safeModel($model))
                {
                    if($model->copyEvent)
                    {
                        $id=$model->getPrimaryKey();
                        $this->copyEvent($model->copyEventId,$id);
                    }
                    return [['respuesta'=>1,'id' => $model->id]];
                }   
            }else{
                /*Yii::$app->response->format = Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);*/
                 return [['respuesta'=>0,'error'=>$model->getErrors()]];
            }
        }else{
            return $this->render('create', [
                'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas()
                    ,'listTipoEventos' => $this->toListTipoEventos(),'listPais'=>  Pais::toList(),'listCiudad'=> Ciudad::toList($pais)
                    ,'listEventos'=>Eventos::toList()
            ]);
        }
    }
    
    public function copyEvent($idEvent=106,$eventos){
       // $empresas= Empresas::find()->where(['=','id_evento',$idEvent])->all();
        $personas= Personas::find()->where(['=','id_evento',$idEvent])->all();
        $where = "e.id_evento=".$idEvent;
        $empresas = (new \yii\db\Query())
            ->select("e.nombre as enombre,e.identificacion as eidentificacion,e.identificacion as eidentificacion"
                    . ",e.direccion as edireccion,e.telefono as etelefono,e.telefono_extension as etelefono_extension"
                    . ",e.movil as emovil,e.id_ciudad as eid_ciudad,e.afiliado_gremio as eafiliado_gremio,e.id_sector_empresa as eid_sector_empresa"
                    . ",e.estado as eestado,c.nombre as cnombre,c.telefono as ctelefono,c.correo as ccorreo,c.id_cargo as cid_cargo,c.id as idcontacto"
                    . ",c.telefono_extension as ctelefono_extension,c.diamax_facturacion,c.direccion as cdireccion,c.id_ciudad as cid_ciudad,c.observaciones")
            ->from('empresas e ')
            ->leftJoin("contactos c","e.id=c.id_empresa")
            ->where($where)->all();    
        foreach ($empresas as $empresa)
        {              
            $newEmpresa = new Empresas();
            
            $newEmpresa->id_evento = $eventos;
            $newEmpresa->nombre = $empresa['enombre'];
            $newEmpresa->identificacion = $empresa['eidentificacion'];
            $newEmpresa->direccion = $empresa['edireccion'];   
            $newEmpresa->telefono = $empresa['etelefono']; 
            $newEmpresa->telefono_extension = $empresa['etelefono_extension'];
            $newEmpresa->movil = $empresa['emovil'];
            $newEmpresa->id_ciudad = $empresa['eid_ciudad'];
            $newEmpresa->afiliado_gremio = $empresa['eafiliado_gremio'];
            $newEmpresa->estado = $empresa['eestado'];
            $newEmpresa->id_sector_empresa = $empresa['eid_sector_empresa'];
            $newEmpresa->save(false);
            if($empresa['idcontacto'])
            {
                $newContacto = new Contactos(); 
                $newContacto->id_empresa =  $newEmpresa->getPrimaryKey();
                $newContacto->nombre = $empresa['cnombre'];
                $newContacto->telefono = $empresa['ctelefono'];
                $newContacto->correo = $empresa['ccorreo'];
                $newContacto->id_cargo = $empresa['cid_cargo'];
                $newContacto->telefono_extension = $empresa['ctelefono_extension'];
                $newContacto->diamax_facturacion = $empresa['diamax_facturacion'];
                $newContacto->direccion = $empresa['cdireccion'];
                $newContacto->id_ciudad = $empresa['cid_ciudad'];
                $newContacto->observaciones = $empresa['observaciones'];
                $newContacto->save(false);
            }
        }   
        foreach ($personas as $persona)
        {
            $newPersona = new Personas();
            $newPersona->id_evento = $eventos;
            $newPersona->nombre = $persona['nombre'];
            $newPersona->apellido = $persona['apellido']; 
            $newPersona->identificacion = $persona['identificacion']; 
            $newPersona->tipo_documento = $persona['tipo_documento']; 
            $newPersona->telefono = $persona['telefono'];
            $newPersona->movil = $persona['movil'];
            $newPersona->direccion = $persona['direccion'];
            $newPersona->id_ciudad = $persona['id_ciudad'];
            $newPersona->email = $persona['email'];
            $newPersona->estado = $persona['estado'];
            $newPersona->id_cargo = $persona['id_cargo'];
            $newPersona->id_tipo_asistente = $persona['id_tipo_asistente'];
            $newPersona->save(false);
        }
    }


    public static function actionToListCiudad($pais=0){
        $pais=$_POST['pais'];
        if($pais)
        {
             $model= Ciudad::find()->where(['=','status',1])->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
                //return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
                return  json_encode(\yii\helpers\ArrayHelper::map($model, 'id', 
                 function($model, $defaultValue) {
                    return $model['nombre'];
                }));
        }
        /* else
        {
           return $model= array();
        }*/
   }
    
     public function safeModel($model){
        try {
            $model->save();
            return 1;
         } catch (\Exception $e) {
             var_dump($e);exit();
             return $e;
         }   
    }

    /**
     * Updates an existing Eventos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->fecha_hora_inicio = $this->FormatoFechas($model->fecha_hora_inicio);
            $model->fecha_hora_fin = $this->FormatoFechas($model->fecha_hora_fin);
            if($model->validate()){
                if($this->safeModel($model))
                {
                    return [['respuesta'=>1,'id' => $model->id]];
                }   
            }else{
                 return [['respuesta'=>0,'error'=>$model->getErrors()]];
            }
            return $this->redirect(['index']);
        } else {
            $model->fecha_hora_inicio=Yii::$app->formatter->asDate($model->fecha_hora_inicio, 'php:d/m/Y');
             $model->fecha_hora_fin=Yii::$app->formatter->asDate($model->fecha_hora_fin, 'php:d/m/Y');
            return $this->render('update', [
                 'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas()
                    ,'listTipoEventos' => $this->toListTipoEventos(),'listPais'=>Pais::toList(),'listCiudad'=> Ciudad::toList($model->pais)
                    ,'listEventos'=>Eventos::toList()
            ]);
        }
    }
    
      function FormatoFechas($fecha){
        $dia = substr($fecha, 0, 2);
        $mes  = substr($fecha, 3, 2);
        $ano = substr($fecha, -4);
        // fechal final realizada el cambio de formato a las fechas europeas5
        $fecha = $ano.'-'.$mes.'-'.$dia;
   
    return $fecha;
    }

    /**
     * Deletes an existing Eventos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionDeleteAjax($id,$accion)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $where = "f.id_evento=".$id;
         $detalleRecibos = (new \yii\db\Query())
                ->select("df.id")
                ->from('detalle_recibos df')
                ->leftJoin("facturas f","f.id=df.id_factura")
                ->where($where)->all();
        foreach ($detalleRecibos as $detalleRecibo) {
            \app\models\DetalleRecibos::findOne($detalleRecibo['id'])->delete();
        }
        $detalleFacturas = (new \yii\db\Query())
                ->select("df.id")
                ->from('detalle_factura df')
                ->leftJoin("facturas f","f.id=df.id_factura")
                ->where($where)->all();
        foreach ($detalleFacturas as $detalleFactura) {
            \app\models\DetalleFactura::findOne($detalleFactura['id'])->delete();
        }
        
        $facturas = Facturas::deleteAll(['=','id_evento',$id]);
        //$inscripciones = Inscripciones::deleteAll(['=','id_evento',$even]);
        //$inscripciones = Inscripciones::find()->where(['=','deleted',0])->all();
         $where = "p.id_evento=".$id;
         $inscripciones = (new \yii\db\Query())
                ->select("i.id")
                ->from('inscripciones i')
                ->leftJoin("productos p","p.id=i.id_producto")
                ->where($where)->all();
        foreach ($inscripciones as $inscripcion) {
            Inscripciones::findOne($inscripcion['id'])->delete();
        }
        $where = "p.id_evento=".$id;
         $contactos = (new \yii\db\Query())
                ->select("c.id")
                ->from('contactos c')
                ->leftJoin("empresas p","p.id=c.id_empresa")
                ->where($where)->all();
        foreach ($contactos as $contacto) {
            \app\models\Contactos::findOne($inscripcion['id'])->delete();
        }
        $personas = Personas::deleteAll(['=','id_evento',$id]);
        $empresas = Empresas::deleteAll(['=','id_evento',$id]);
        $productos = \app\models\Productos::deleteAll(['=','id_evento',$id]);
        try {
            $this->findModel($id)->delete();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#evento_grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>2,'reload'=>'evento/index']];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>$e]];
         }   
    }
    /**
     * Finds the Eventos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Eventos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Eventos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public static function toListSectoresEmpresas()
    {
        $model= SectoresEmpresas::find()->where(['=','deleted',0])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    
     public static function toListTipoEventos()
    {
        $model= \app\models\TipoEvento::find()->where(['=','deleted',0])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    
}
