<?php

namespace app\controllers;

use app\models\ProveedorTecnologico;
use Yii;
use app\models\SectoresEmpresas;
use app\models\Pais;
use app\models\Departamento;
use app\models\Cargos;
use app\models\Ciudad;
use app\models\Empresas;
use app\models\Personas;
use app\models\EmpresaSearch;
use app\models\Contactos;
use app\models\TipoIdentificacion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use  yii\web\Session;
//use  yii\db\QueryBuilder;
/**
 * EmpresaController implements the CRUD actions for Empresas model.
 */
class EmpresaController extends Controller
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
     * Lists all Empresas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmpresaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Empresas model.
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
     * Creates a new Empresas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pais=1,$id_padre=""){
        $visible = Yii::$app->request->get() ? Yii::$app->request->get('visible') : false;
        $model = new Empresas();
        $contacto = new Contactos();
        $persona = new Personas();
        $model->estado='1';
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model->id_evento = $event_id;
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $transaction = Yii::$app->db->beginTransaction();
                
                
                if($visible)
                {
                    try  {
                        if($model->validate()  && !$this->findModelEmpresa($model->identificacion)){
                            if ($model->save(false)) {
                                $transaction->commit();
                                $id=$model->getPrimaryKey();
                                return [['respuesta'=>1,'url'=>Url::toRoute(['empresa'])]];
                            } else {
                                $transaction->rollBack();
                                return [['respuesta'=>0,'id'=> ActiveForm::validate($model)]];
                            }
                        }
                        else
                        {        
                            return [['respuesta'=>0,'id'=> $this->findModelEmpresa($model->identificacion)]];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
                
                
                $model->id_sector_empresa=200;
                
                try  {
                    if($model->validate() && !$this->findModelEmpresa($model->identificacion)){
                        if ($model->save(false) && $contacto->save(false)) {
                            $transaction->commit();
                            $id=$model->getPrimaryKey();
                            return [['respuesta'=>1,'id'=>$id,'url'=>Url::toRoute(['pais/view','id' => $id])]];
                        } else {
                            $transaction->rollBack();
                            return [['respuesta'=>10,'id'=> $contacto->errors]];
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
            } else {
                return $this->render('create', [
                    'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                    'listPais'=>  Pais::toList()
					,'listDepartamento'=> Departamento::toList($pais)
					,'listCiudad'=> Ciudad::toList($id_padre),
                    'contacto'=>$contacto,'listCargos'=>Cargos::toList(),
                    'persona'=>$persona,
                    'visible'=>$visible,
                    'listPt'=>ProveedorTecnologico::toList(),
                    'listTI'=>TipoIdentificacion::toList()
                ]);
            }
        }
        else{
            if ($model->load(Yii::$app->request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                Yii::$app->response->format = Response::FORMAT_JSON;
                try  {
                    if($model->validate() && $contacto->validate() && !$this->findModelEmpresa($model->identificacion)){
                        if ($model->save(false) && $contacto->save(false)) {
                            $transaction->commit();
                            $id=$model->getPrimaryKey();
                            return [['respuesta'=>2,'id'=>$id,'url'=>Url::toRoute(['pais/view','id' => $id])]];
                        } else {
                            $transaction->rollBack();
                            return [['respuesta'=>0,'id'=> $model->errors]];
                        }
                    }
                    else
                    {        
                        return [['respuesta'=>0,'id'=> ActiveForm::validate($contacto)]];
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
              
            }
            else{
                return $this->render('create', [
                    'model' => $model,'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                    'listPais'=>  Pais::toList()
					,'listDepartamento'=> Departamento::toList($pais)
					,'listCiudad'=> Ciudad::toList($id_padre),
                    'contacto'=>$contacto,'listCargos'=> Cargos::toList(),
                    'persona'=>$persona,
                    'visible'=>$visible,
                    'listPt'=>ProveedorTecnologico::toList(),
                    'listTI'=>TipoIdentificacion::toList()
                ]);
            }
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
     * Updates an existing Empresas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($pais=1,$id,$redirectUrl='/inscripcion/index-menu')
    {
        $model = $this->findModel($id);
        $model->pais = $model->ciudad->id_pais;
        $model->id_padre = $model->ciudad->id_padre;
        $model->redirectEmpresa = $redirectUrl;
        //$model->redirectEmpresa
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $model->redirectEmpresa == "empresa" ? $this->redirect(['/empresa']) : $this->redirect(['/inscripcion']);
            //return $this->redirect(['/inscripcion']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'listSectoresEmpresas' => $this->toListSectoresEmpresas(),
                'listPais'=>  Pais::toList()
				,'listDepartamento'=> Departamento::toList($model->pais)
                ,'listCiudad'=> Ciudad::toList($model->id_padre),
                'contacto'=>$this->findModelContacto($id),
                'redirectUrl'=>$model->redirectEmpresa,
                'listPt'=>ProveedorTecnologico::toList(),
                'listTI'=>TipoIdentificacion::toList()
            ]);
        }
    }
    
     protected function findModelContacto($id=0)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Contactos::find()->where(['=','id_empresa',$id]),
        ]);

        return $dataProvider;
    }
    
     public static function toListSectoresEmpresas()
    {
        $model= SectoresEmpresas::find()->where(['=','deleted',0])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    /**
     * Deletes an existing Empresas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $cou=Contactos::find()
        ->where(['id_empresa' => $id])
        ->count();
        if($cou>0)
            Contactos::deleteAll('id_empresa IN ('.$id.')');
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Empresas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Empresas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Empresas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findModelEmpresa($identificacion)
    {
        if (($model = Empresas::findOne(['=','identificacion',$identificacion])) !== NULL) {
            return $model;
        } else {
            return 0;
        }
    }
    
}
