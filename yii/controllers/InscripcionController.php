<?php

namespace app\controllers;

use app\models\ProveedorTecnologico;
use Yii;
use app\models\Inscripciones;
use app\models\InscripcionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SectoresEmpresas;
use app\models\Pais;
use app\models\Departamento;
use app\models\Cargos;
use app\models\Ciudad;
use app\models\Empresas;
use app\models\Personas;
use app\models\EmpresaSearch;
use app\models\Contactos;
use app\models\DetalleContacto;
use app\models\TipoIdentificacion;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

/**
 * InscripcionController implements the CRUD actions for Inscripciones model.
 */
class InscripcionController extends Controller
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
     * Lists all Inscripciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InscripcionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
         /*$dataProvider = new ActiveDataProvider([
            'query' => Inscripciones::find()->orderBy('id_empresa'),
        ]);*/

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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

    public function actionIndexMenuEmpresa()
    {
        return $this->render('index-menu-empresa');
    }
    
    public function actionChangePresence($id){
        $model = \app\models\Inscripciones::findOne($id);
        
            if ($model->load(Yii::$app->request->post())) {
                if($model->validate())
                {
                    if($this->safeModel($model))
                    {
                          return $this->redirect(['/inscripcion']);
                    }
                    else
                    {
                        return [['respuesta'=>0,'error'=>$model->getErrors()]];
                    }
                }
            } else {
               return $this->renderPartial('_update_presence', [
                    'model' => $model,     
                ]);
            }
     
    }
    
    public function actionInscripcionEmpresa($pais=1,$idEmpresa=0, $id_padre="")
    {
        $model = new Inscripciones();
        $empresa = new Empresas();
        $contacto = new Contactos();
        $persona = new Personas();
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $empresa->id_evento = $event_id;
        if($idEmpresa){
            $dataEmpresa = $this->findModelEmpresa($idEmpresa);
            $contacto->direccion = $dataEmpresa->direccion;
            $contacto->pais = $dataEmpresa->ciudad->id_pais;
            $pais = $dataEmpresa->ciudad->id_pais;
            $contacto->id_padre = $dataEmpresa->ciudad->id_padre;
			      $id_padre = $dataEmpresa->ciudad->id_padre;
            $contacto->id_ciudad = $dataEmpresa->id_ciudad;
            $contacto->telefono = $dataEmpresa->telefono;
            $contacto->telefono_extension = $dataEmpresa->telefono_extension;
            $contacto->movil = $dataEmpresa->movil;
            $contacto->inscripcion=1;
        }
        
        $empresa->estado='1';
        
        if (Yii::$app->request->isAjax) {
            if($model->load(Yii::$app->request->post()))
            {
                $model->id_empresa=$idEmpresa;
                if($model->guardar==1){
                     if ($empresa->load(Yii::$app->request->post())) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $transaction = Yii::$app->db->beginTransaction();
                        try  {
                            if($empresa->validate() && !$this->findModelEmpresaByIdentificacion($empresa->identificacion)){
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
                                $idEmpresa= $empresa->identificacion ? $this->findModelEmpresaByIdentificacion($empresa->identificacion) : 0;
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
                      'listPais'=>  Pais::toList(),
					            'listDepartamento'=> Departamento::toList($pais),
					            'listCiudad'=> Ciudad::toList($id_padre),
                      'contacto'=>$contacto,'listCargos'=>Cargos::toList(),
                      'persona'=>$persona,
                      'empresa'=>$empresa,
                      'personas_data'=>  $this->findModelPersonas($idEmpresa),
                      'listPt'=>ProveedorTecnologico::toList(),
                      'listTI'=>TipoIdentificacion::toList()
                      ]);
            }
        }
        else{
            if (!$empresa->load(Yii::$app->request->post())) {
                return $this->render('inscripcion-empresa', [
                    'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                    'listPais'=>  Pais::toList(),
					          'listDepartamento'=> Departamento::toList($pais),
					          'listCiudad'=> Ciudad::toList($id_padre),
                    'contacto'=>$contacto,'listCargos'=> Cargos::toList(),
                    'persona'=>$persona,'empresa'=>$empresa,
                    'personas_data'=>  $this->findModelPersonas($idEmpresa),
                    'listPt'=>ProveedorTecnologico::toList(),
                    'listTI'=>TipoIdentificacion::toList()
                ]);
            }
        } 
    }
    
    public function actionInscripcionEmpresaPersona($idEmpresa=0){
        $model = new Inscripciones();
        $model->id_empresa=$idEmpresa;
        
        return $this->render('inscripcion-empresa-persona', [
                    'model' => $model,
                    'personas_data'=>  $this->findModelPersonas($model->id_empresa),
                ]);
    }
    
    public function actionValidarnit(){
        $id = Yii::$app->request->post('id');
        $eventosId = Yii::$app->request->post('eventosId');
        Yii::$app->response->format = Response::FORMAT_JSON;            
        $idEmpresa= $id ? $this->findModelEmpresaByIdentificacion($id,$eventosId) : 0;
        $repuesta= $idEmpresa ? 0 : 1;
        return [['respuesta'=>$repuesta,'idEmpresa'=>$idEmpresa]];          
    }
    
    public function actionNoshow(){
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $model->is_presence = 0;
        $repuesta= $model->save();
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [['respuesta'=>$repuesta]];          
    }
    
    protected function findModelPersona($id)
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        if (($model = Personas::find()->where(['=','identificacion',$id])->andWhere(['=','id_evento',$event_id])->one()) !== null) {
            return $model;
        } else {
           return 0;
        }
    }
    
    
    public function actionValidarpersona(){
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
                   
        $idEmpresa= $id ? $this->findModelPersona($id) : 0;
        $repuesta= $idEmpresa ? 0 : 1;
        return [['respuesta'=>$repuesta,'idEmpresa'=>$idEmpresa]];          
    }
    
    
    public function actionCambiarInscrito($idInscrito=0){
        if (Yii::$app->request->isAjax) {
            if($va=Yii::$app->request->post()){
                Yii::$app->response->format = Response::FORMAT_JSON;
                if(isset($va['Inscripciones']['id_persona'])){
                    $model = Inscripciones::findOne($va['Inscripciones']['id_cambio']);
                    $modelnew = Inscripciones::findOne($va['Inscripciones']['id_persona']);
                    $detalleFactura = \app\models\DetalleFactura::findOne(["id_inscripcion"=>$va['Inscripciones']['id_cambio']]);
                    $detalleFactura->id_inscripcion=$modelnew->id;

                    $model->estado=0;
                    $modelnew->estado=2;
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        $model->save(false);
                        $modelnew->save(false);
                        $detalleFactura->save(false);
                        $transaction->commit();
                        return [['respuesta'=>1,'id'=>$modelnew->id]];
                    } catch (Exception $ex) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> ActiveForm::validate($model)]];
                    }
                }
                else if(isset($va['Inscripciones']['id_cambio'])){
                    $model = Inscripciones::findOne($va['Inscripciones']['id_cambio']);
                    $model->estado=$va['Inscripciones']['estado'];
                    $model->save();
                    return [['respuesta'=>1,'id'=>$model->id]];
                }
                //$va['Inscripciones']['estado'];
            }
            else {
                $inscrito = Inscripciones::findOne($idInscrito);
                $inscrito->id_cambio=$idInscrito;
                $idEmpresa = $inscrito->id_empresa;
                return $this->renderAjax('cambiar-inscrito', [
                            'model' => $inscrito,
                            'listInscrito'=>  $this->toListInscritos($idEmpresa,1)
                        ]);
            }
        }
    }
    
    
    public function actionNotas($idInscrito=0){
      
        if (Yii::$app->request->isAjax) {
            if($va=Yii::$app->request->post())
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if(isset($va['Inscripciones']['id_cambio']))
                {
                       $model = Inscripciones::findOne($va['Inscripciones']['id_cambio']);
                       $model->observaciones = $va['Inscripciones']['observaciones'];
                       $transaction = Yii::$app->db->beginTransaction();
                       try{
                           $model->save(false);
                           $transaction->commit();
                           return [['respuesta'=>1,'id'=>$model->id]];
                       } catch (Exception $ex) {
                           $transaction->rollBack();
                           return [['respuesta'=>1,'data'=> ActiveForm::validate($model)]];
                       }
                }
                //$va['Inscripciones']['estado'];
            }
            else
            {
                $inscrito = Inscripciones::findOne($idInscrito);
                $inscrito->id_cambio=$idInscrito;
                return $this->renderAjax('notas', [
                            'model' => $inscrito,
                        ]);
            }
        }
    }
    
    
    public static function toListInscritos($idEmpresa=0,$estado=1){
            
        $sql = "SELECT i.id,CONCAT(p.nombre,' ',p.apellido) AS nombre
                FROM
                  inscripciones i
                LEFT JOIN
                  personas p ON(i.id_persona = p.id)
                WHERE
                  i.id_empresa = '$idEmpresa' 
                AND i.estado=".$estado;

        $lista=Yii::$app->db->createCommand($sql)->queryAll();
       // $lista = $lista ? $lista : array("id"=>"","nombre"=>"");
        return \yii\helpers\ArrayHelper::map($lista, 'id', 'nombre');
    } 
    
     public static function toListSectoresEmpresas()
    {
        $model= SectoresEmpresas::find()->where(['=','deleted',0])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
   

    public function safeModel($model){
        try {
            return $model->save();
         } catch (\Exception $e) {
             return $e;
         }   
    }
    
    /**
     * Displays a single Inscripciones model.
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
     * Creates a new Inscripciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){  
        $model = new Inscripciones();
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                 $model->status='1';
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate())
                 {                  
                    if($this->safeModel($model))
                    {
                        $id=$model->getPrimaryKey();
                        return [['respuesta'=>1,'id'=>$id,'url'=>Url::toRoute(['pais/view','id' => $id])]];
                    }
                    else
                       return [['respuesta'=>0]];      
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            } else {
                return $this->renderAjax('create', [
                    'model' => $model,'drop'=>$drop
                ]);
            }
        }
        else{
                return $this->render('create', [
                    'model' => $model,'drop'=>$drop
                ]);
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
    
    /**
     * Updates an existing Inscripciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
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

    /**
     * Deletes an existing Inscripciones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Inscripciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inscripciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inscripciones::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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
    
    protected function findModelEmpresaByIdentificacion($identificacion,$eventosId=0)
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
}
