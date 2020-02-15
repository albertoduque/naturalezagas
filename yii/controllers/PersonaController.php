<?php

namespace app\controllers;

use Yii;
use app\models\TipoAsistentes;
use app\models\Inscripciones;
use app\models\Personas;
use app\models\Pais;
use app\models\Departamento;
use app\models\Cargos;
use app\models\Ciudad;
use app\models\Empresas;
use app\models\Productos;
use app\models\PersonaSearch;
use app\models\TipoIdentificacion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use  yii\web\Session;


/**
 * PersonaController implements the CRUD actions for Personas model.
 */
class PersonaController extends Controller
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
     * Lists all Personas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PersonaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Personas model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function getProducto($id_evento){
        $producto = Productos::find()->where(['=','id_evento',$id_evento])->andWhere(['=','inscripciones','S'])->one();
        return $producto['id'];
    }
    /**
     * Creates a new Personas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pais=1,$id_empresa=NULL, $id_padre="")
    {
        $model = new Personas();
        $inscripcion = new Inscripciones();
        $empresa = new Empresas();
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
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
                //$idTemp->id_tipo_identificacion = $model->id_tipo_identificacion;
                
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate())
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
                     $idTemp=$this->findModelPersonaIdentificacion($model->identificacion);
                     if($this->findModelPersonaIdentificacion($model->identificacion))
                     {
                        $idTemp->load($model);
                        $idTemp->id_tipo_identificacion = $model->id_tipo_identificacion;
                        $idTemp->id_ciudad = $model->id_ciudad;
                        $idTemp->id_tipo_asistente= $model->id_tipo_asistente;
                        $idTemp->id_cargo = $model->id_cargo;
                        $idTemp->estado='1';
                        $transaction = Yii::$app->db->beginTransaction();
                        $id=$idTemp->id;
                        $inscripcion->id_empresa=$id_empresa;
                        $inscripcion->id_producto=$this->getProducto($event_id);
                        $inscripcion->id_persona=$id;
                        if($idTemp->validate() && $inscripcion->validate())
                        {
                            $idTemp->save();
                            $inscripcion->save(false);
                            $transaction->commit();
                            return [['respuesta'=>1,'id' => $id_empresa]];
                        }
                        else
                        {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'error'=>$inscripcion->getErrors(),'empresa'=>ActiveForm::validate($idTemp)]];
                        }
                     }
                     else
                        return [['respuesta'=>0,'data2'=>2,'data'=> ActiveForm::validate($model)]];
                }
            } else {
                return $this->renderAjax('create', [
					'model' => $model
					,'listPais'=>  Pais::toList()
					,'listDepartamento'=> Departamento::toList($pais)
					,'listCiudad'=> Ciudad::toList($id_padre),
                    'listCargos'=> Cargos::toList(),'listAsistente'=> TipoAsistentes::toList(),
					'listTI'=>TipoIdentificacion::toList()
            ]);
            }
        }
        else{
            return $this->render('create', [
                'model' => $model,
				'listPais'=>  Pais::toList()
				,'listDepartamento'=>  Departamento::toList($pais)
				,'listCiudad'=> Ciudad::toList($id_padre),
                'listCargos'=> Cargos::toList(),'listAsistente'=> TipoAsistentes::toList(),
				'listTI'=>TipoIdentificacion::toList()
            ]);
        }     
    }
    
    protected function findModelPersona($id)
    {
        $session = Yii::$app->session;
                $event_id = $session->get('event_id');
        if (($model = Personas::find()->where(['=','id',$id])->andWhere(['=','id_evento',$event_id])->one()) !== null) {
            return $model;
        } else {
            return 0;
        }
    }
    
     
    protected function findModelPersonaIdentificacion($identificacion)
    {
        $session = Yii::$app->session;
                $event_id = $session->get('event_id');
        if (($model = Personas::find()->where(['=','identificacion',$identificacion])->andWhere(['=','id_evento',$event_id])->one()) !== null) {
            return $model;
        } else {
            return 0;
        }
    }
    
    public function actionValidacion($id = null){
        //$model = new Personas();
        $model = $id===null ? new Personas : $this->findModelPersona($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //$model->scenario="create";
           
            Yii::$app->response->format = 'json';
             if(!$model->id)
            {
                return  [['respuesta'=>1]];
            }
            return ActiveForm::validate($model);
        }
    }
    
      public function safeModel($model){
        try {
            return $model->save();
         } catch (\Exception $e) {
             return $e;
         }   
    }
 
    /**
     * Updates an existing Personas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($pais=1,$id)
    {
        $model = $this->findModel($id);
        $model->pais = $model->ciudad->id_pais;
        $model->id_padre = $model->ciudad->id_padre;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/inscripcion']);
        } else {
            return $this->render('update', [
                'model' => $model
				,'listPais'=>  Pais::toList()
				,'listDepartamento'=> Departamento::toList($model->pais)
				,'listCiudad'=> Ciudad::toList($model->id_padre),
                'listCargos'=> Cargos::toList(),'listAsistente'=> TipoAsistentes::toList(),
				'listTI'=>TipoIdentificacion::toList()
            ]);
        }
    }

    /**
     * Deletes an existing Personas model.
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
     * Finds the Personas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Personas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Personas::findOne($id)) !== null) {
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
}
