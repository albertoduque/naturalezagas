<?php

namespace app\controllers;

use Yii;
use app\models\Contactos;
use app\models\Pais;
use app\models\Departamento;
use app\models\Cargos;
use app\models\Ciudad;
use app\models\Empresas;
use app\models\ContactoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
/**
 * ContactoController implements the CRUD actions for Contactos model.
 */
class ContactoController extends Controller
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
     * Lists all Contactos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contactos model.
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
     * Creates a new Contactos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=0,$pais=1,$id_padre = "")
    {
        $model = new Contactos();
        $model->id_empresa=$id;
        
        if($id){
            $dataEmpresa = $this->findModelEmpresa($id);
            $model->direccion = $dataEmpresa->direccion;
            $model->pais = $dataEmpresa->ciudad->id_pais;
			$model->id_padre = $dataEmpresa->ciudad->id_padre;
			
            $pais = $dataEmpresa->ciudad->id_pais;
            $id_padre = $dataEmpresa->ciudad->id_padre;
            $model->id_ciudad = $dataEmpresa->id_ciudad;
            $model->telefono = $dataEmpresa->telefono;
            $model->movil = $dataEmpresa->movil;
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate())
                {
                    if($this->safeModel($model))
                    {
                        return [['respuesta'=>1,'id' => $id]];
                    }
                    else
                    {
                        return [['respuesta'=>0,'error'=>$inscripcion->getErrors(),'empresa'=>$id_empresa]];
                    }
                }
            } else {
                return $this->renderAjax('create', [
                    'model' => $model
					,'listPais'=>  Pais::toList()
					,'listDepartamento'=> Departamento::toList($pais)
					,'listCiudad'=> Ciudad::toList($id_padre),
                    'listCargos'=> Cargos::toList(),
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model
				,'listPais'=>  Pais::toList()
				,'listDepartamento'=> Departamento::toList($pais)
				,'listCiudad'=> Ciudad::toList($id_padre)
				,'listCargos'=> Cargos::toList(),
            ]);
        }
    }

    /**
     * Updates an existing Contactos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$model->pais = $model->ciudad->id_pais;
		$model->id_padre = $model->ciudad->id_padre;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newUrl = str_replace("contacto", "empresa", Yii::$app->request->url);
            $newUrl = str_replace($model->id, $model->id_empresa, $newUrl);
           return $this->redirect($newUrl);
        } else {
            $model->active=2;
            return $this->render('update', [
                'model' => $model
				,'listPais'=>  Pais::toList()
				,'listDepartamento'=> Departamento::toList($model->pais)
				,'listCiudad'=> Ciudad::toList($model->id_padre)
				,'listCargos'=> Cargos::toList(),
            ]);
        }
    }

    /**
     * Deletes an existing Contactos model.
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
        try {
            $this->findModel($id)->delete();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#evento_grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>3]];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>$e]];
         }   
    }

    /**
     * Finds the Contactos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contactos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contactos::findOne($id)) !== null) {
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
    protected function findModelEmpresa($id)
    {
        if (($model = Empresas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
