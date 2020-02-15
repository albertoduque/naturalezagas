<?php

namespace app\controllers;

use app\models\Productos;
use app\models\TipoAsistentes;
use Yii;
use app\models\Cargos;
use app\models\CargoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use yii\base\ErrorException;
use yii\bootstrap\ActiveForm;
use app\models\SectoresEmpresas;
use app\models\Pais;
use app\models\Ciudad;
use app\models\Empresas;
use app\models\Personas;
use app\models\Inscripciones;
use app\models\ProveedorTecnologico;
use app\models\Contactos;
use app\controllers\InscripcionController;
use SoapClient;
use SOAPHeader;
use yii\data\ActiveDataProvider;
//require_once 'ClientDispapelesApi.class.php';
/**
 * CargoController implements the CRUD actions for Cargos model.
 */
class CongresoController extends Controller
{
    public $eventId=110;
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
     * Lists all Cargos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CargoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionInscripcionEmpresa($pais=1,$idEmpresa=0)
    {
        $model = new Inscripciones();
        $empresa = new Empresas();
        $contacto = new Contactos();
        $persona = new Personas();
        $session = Yii::$app->session;
        $event_id = $this->eventId;
        $empresa->id_evento = $event_id;
        if($idEmpresa)
        {
            $dataEmpresa = $this->findModelEmpresa($idEmpresa);
            $contacto->direccion = $dataEmpresa->direccion;
            $contacto->pais = $dataEmpresa->ciudad->id_pais;
            $pais = $dataEmpresa->ciudad->id_pais;
            $contacto->id_ciudad = $dataEmpresa->id_ciudad;
            $contacto->telefono = $dataEmpresa->telefono;
            $contacto->telefono_extension = $dataEmpresa->telefono_extension;
            $contacto->movil = $dataEmpresa->movil;

            $contacto->inscripcion=0;
        }

        $empresa->estado='1';

        if (Yii::$app->request->isAjax) {
            if($model->load(Yii::$app->request->post()))
            {
                $model->id_empresa=$idEmpresa;
                if($model->guardar==1){
                    if ($empresa->load(Yii::$app->request->post())) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $empresa->id_evento=$this->eventId;
                        $transaction = Yii::$app->db->beginTransaction();
                        try  {
                            if($empresa->validate() && !$this->getEmpresaByIdentificacion($empresa->identificacion)){
                                if ($empresa->save(false)) {
                                    $transaction->commit();
                                    $empresa_id=$empresa->getPrimaryKey();
                                    return [['respuesta'=>1,'empresa_id'=>$empresa_id]];
                                } else {
                                    $transaction->rollBack();
                                    return [['respuesta'=>0,'data'=> ActiveForm::validate($empresa)]];
                                }
                            }
                            else
                            {
                                $idEmpresa= $empresa->identificacion ? $this->getEmpresaByIdentificacion($empresa->identificacion) : 0;
                                $transaction->rollBack();
                                return [['respuesta'=>0,'data'=> ActiveForm::validate($empresa),'idEmpresa'=>$idEmpresa]];
                            }
                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
                if($model->guardar==2){
                    if ($contacto->load(Yii::$app->request->post())) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $transaction = Yii::$app->db->beginTransaction();
                        if($contacto->validate()){
                            if ($contacto->save()) {
                                $contacto_id=$contacto->getPrimaryKey();
                                $transaction->commit();
                                return [['respuesta'=>1,'contacto_id'=>$contacto_id]];
                            } else {
                                $transaction->rollBack();
                                return [['respuesta'=>0,'data'=> ActiveForm::validate($contacto)]];
                            }
                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data'=> ActiveForm::validate($contacto)]];
                        }
                    }
                }
            }
            else {
                return $this->render('inscripcion-empresa', [
                    'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                    'listPais'=>  Pais::toList(),'listCiudad'=> Ciudad::toList($pais),
                    'contacto'=>$contacto,'listCargos'=>Cargos::toList(),
                    'persona'=>$persona,
                    'empresa'=>$empresa,
                    'personas_data'=>  $this->findModelPersonas($idEmpresa),
                    'listPt'=>ProveedorTecnologico::toList()
                ]);
            }
        }
        else{
            if ($empresa->load(Yii::$app->request->post())) {
                var_dump($empresa);die();
                /*$transaction = Yii::$app->db->beginTransaction();

                try  {
                    if($empresa->validate() && $contacto->validate()){
                        if ($empresa->save(false) && $contacto->save(false)) {
                            $transaction->commit();
                            $id=$empresa->getPrimaryKey();
                            return [['respuesta'=>2,'id'=>$id,'url'=>Url::toRoute(['pais/view','id' => $id])]];
                        } else {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'id'=> $empresa->errors]];
                        }
                    }
                    else
                    {
                        return [['respuesta'=>0,'id'=> $empresa->errors]];
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
              */
            }
            else{
                return $this->render('inscripcion-empresa', [
                    'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                    'listPais'=>  Pais::toList(),'listCiudad'=> Ciudad::toList($pais),
                    'contacto'=>$contacto,'listCargos'=> $this->toListCargos(),
                    'persona'=>$persona,'empresa'=>$empresa,
                    'personas_data'=>  $this->findModelPersonas($idEmpresa),
                    'listPt'=>ProveedorTecnologico::toList()
                ]);
            }
        }
    }

    public function toListCargos(){
        $model= Cargos::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$this->eventId])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    public function toListTipoAsistentes(){
        $model= TipoAsistentes::find()->where(['=','id',48])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    public static function toListSectoresEmpresas()
    {
        $model= SectoresEmpresas::find()->where(['=','deleted',0])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    /**
     * Displays a single Cargos model.
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
     * Creates a new Cargos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($name=null)
    {
        
        $model = new Cargos();
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model->id_evento = $event_id;

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate())
                 {
                    if($this->safeModel($model))
                    {
                        $id=$model->getPrimaryKey();
                        if($name)
                            return [['respuesta'=>1,'types'=>$name,'id'=>$id]];
                        else
                            return $this->redirect(['index']);
                    }
                    else
                       return [['respuesta'=>0]];
                        
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            }else {
                  $model->types = $name;
                return $this->renderAjax('create', [
                    'model' => $model
                ]);
            }
        }
        else{
                return $this->render('create', [
                    'model' => $model
                ]);
            
        } 
    }

    /**
     * Updates an existing Cargos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
             Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->validate())
            {
                if($this->safeModel($model))
                {
                    $id=$model->getPrimaryKey();
                    return $this->redirect(['index']);
                }
                else
                   return [['respuesta'=>0]];

             }
             else {
                return ActiveForm::validate($model);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cargos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            if($this->findModel($id)->delete()){
               return 1;
            }
        }catch (\Exception $e) {
             throw new \yii\web\HttpException(500,"YOUR MESSAGE", 405);
        }
    }
    
    public function actionDeleteAjax($id,$accion)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $this->actionDelete($id);
            $transaction->commit();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#evento_grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>3]];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
              $transaction->rollBack();
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro, el Cargo está siendo utilizado','msgSuccess'=>$e]];
         }   
    }
    
    public function actionWebservice()
    {
        $facturaWsdl = new FacturaWsdl();
        //$xmlInvoice = $facturaWsdl->sendFile('e2556e2f2dc65b60653fab4fc380996647363a01');
        //$xmlInvoice = $facturaWsdl->createFactura('e2556e2f2dc65b60653fab4fc380996647363a01');
        $xmlInvoice = $facturaWsdl->createNC('e2556e2f2dc65b60653fab4fc380996647363a01');
        
        //$objClientDispapelesApis = new ClientDispapelesApi();
        //print_r($objClientDispapelesApis->findRegistroDocumentosEmpresa($xmlInvoice) );
        //var_dump((new \DateTime())->format('Y-m-d H:i:s'));die;
        //print_r($objClientDispapelesApis->enviarFactura($xmlInvoice) );
        //print_r($objClientDispapelesApis->enviarFactura($xmlInvoice) );
        
    }

    /**
     * Finds the Cargos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cargos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cargos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function safeModel($model){
        try {
            return $model->save();
         } catch (\Exception $e) {
             return $e;
         }   
    }

    protected function findModelPersonas($id)
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Inscripciones::find()->with('idPersona')->where(['=','id_empresa',$id]),
            //'query' => Personas::find()->where(['>','id',$id]),
        ]);

        return $dataProvider;
    }

    protected function getEmpresaByIdentificacion($identificacion,$eventosId=0)
    {
        if($eventosId) {
            $event_id=$eventosId;
        }
        if($eventosId==0) {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
        }
        $identificacion = trim($identificacion);
        $model = Empresas::findOne(['identificacion'=>$identificacion,'id_evento'=>$event_id]);
        if ($model !== null) {
            return $model->id;
        } else {
            return 0;
        }
    }

    protected function findModelEmpresa($id)
    {
        if (($model = Empresas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCongresoInscrito($pais=1,$id_empresa=NULL)
    {
        $model = new Personas();
        $inscripcion = new Inscripciones();
        $empresa = new Empresas();
        $event_id = $this->eventId;
        $model->idEmpresa=$id_empresa;
        $model->id_evento = $event_id;

        if($id_empresa){
            $dataEmpresa = $this->findModelEmpresa($id_empresa);
            $model->direccion = $dataEmpresa->direccion;
            $model->pais = $dataEmpresa->ciudad->id_pais;
            $model->id_ciudad = $dataEmpresa->id_ciudad;
            $model->telefono = $dataEmpresa->telefono;
            $model->movil = $dataEmpresa->movil;
        }


        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate() && !$this->findModelPersona($model->identificacion))
                {
                    $model->nombre = strtoupper($model->nombre);
                    $model->apellido = strtoupper($model->apellido);
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        if($this->safeModel($model))
                        {
                            $id=$model->getPrimaryKey();
                            $inscripcion->id_empresa=$id_empresa;
                            $inscripcion->id_producto=$this->getProducto($event_id);
                            $inscripcion->id_persona=$id;
                            if($inscripcion->validate())
                            {
                                $inscripcion->save(false);
                                $transaction->commit();
                                return [['respuesta'=>1,'id' => $id_empresa]];
                            }
                            else
                            {
                                $transaction->rollBack();
                                return [['respuesta'=>0,'error'=>$inscripcion->getErrors(),'empresa'=>$id_empresa]];
                            }
                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'data'=> ActiveForm::validate($model)]];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> ActiveForm::validate($model)]];
                    }
                }
                else {
                    if($this->findModelPersona($model->identificacion))
                        return [['respuesta'=>3,'data'=> ActiveForm::validate($model)]];
                    else
                        return [['respuesta'=>0,'data'=> ActiveForm::validate($model)]];
                }
            } else {
                return $this->renderAjax('_createpersona', [
                    'model' => $model,'listPais'=>  Pais::toList(),'listCiudad'=> Ciudad::toList($pais),
                    'listCargos'=> $this->toListCargos(),'listAsistente'=> $this->toListTipoAsistentes()
                ]);
            }
        }
        else{
            return $this->render('_createpersona', [
                'model' => $model,'listPais'=>  Pais::toList(),'listCiudad'=> Ciudad::toList($pais),
                'listCargos'=> $this->toListCargos(),'listAsistente'=> $this->toListTipoAsistentes()
            ]);
        }
    }

    protected function findModelPersona($id)
    {
        $event_id = $this->eventId;
        if (($model = Personas::find()->where(['=','identificacion',$id])->andWhere(['=','id_evento',$event_id])->one()) !== null) {
            return $model;
        } else {
            return 0;
        }
    }

    public function getProducto($id_evento){
        $producto = Productos::find()->where(['=','id_evento',$id_evento])->andWhere(['=','inscripciones','S'])->one();
        return $producto['id'];
    }

    public function actionInscripcionEmpresaPersona($idEmpresa=0){
        $model = new Inscripciones();
        $model->id_empresa=$idEmpresa;

        return $this->render('inscripcion-empresa-persona', [
            'model' => $model,
            'personas_data'=>  $this->findModelPersonas($model->id_empresa),
        ]);
    }
}
