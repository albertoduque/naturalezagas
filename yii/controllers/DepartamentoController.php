<?php

namespace app\controllers;

use Yii;
use app\models\Departamento;
use app\models\DepartamentoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/**
 * DepartamentoController implements the CRUD actions for Departamento model.
 */
class DepartamentoController extends Controller
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
     * Lists all Departamento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartamentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Departamento model.
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
     * Creates a new Departamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Departamento();
//
//        if ($model->load(Yii::$app->request->post())) {
//            $model->status=1;
//            $model->save();
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
    
     public function actionCreate($tipociudad=0,$name=null)
    {   
        $model = new Departamento();
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->status=1;
                $model->id_padre=0;
                if($model->validate())
                 {
                    $pais=$model->id_pais;
                    if($this->safeModel($model))
                    {
                        $id=$model->getPrimaryKey();
                        return [['respuesta'=>1,'pais'=>$pais,'types'=>$name,'id'=> $id,'lista'=>  $this->actionDropdownCodigo($pais),'url'=>Url::toRoute(['departamento/index'])]];
                    }
                    else
                       return [['respuesta'=>0]];
                        
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            }else {
                return $this->renderAjax('create', [
                    'model' => $model,'tipociudad'=>$tipociudad
                ]);
            }
        }
        else{
                return $this->render('create', [
                    'model' => $model,'tipociudad'=>$tipociudad
                ]);
            
        } 
    }

    /**
     * Updates an existing Departamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id,$tipociudad=0)
    {
        $model = $this->findModel($id);
        
         if (Yii::$app->request->isAjax) {
             if ($model->load(Yii::$app->request->post())) {
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 if($model->validate())
                 {
                   if($this->safeModel($model))
                   {
                       return [['respuesta'=>2,'url'=>Url::toRoute(['departamento/index'])]];
                   }
                   else
                       return [['respuesta'=>0]];
                 }
                else {
                   
                    return ActiveForm::validate($model);
                }
             }else
             {
                 return $this->renderAjax('update', [
                    'model' => $model,'tipociudad'=>$tipociudad
                ]);
             }
        
        } else {
            return $this->render('update', [
                'model' => $model,'tipociudad'=>$tipociudad
            ]);
        }
    }
    
    

    /**
     * Deletes an existing Departamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
         $departamento= \app\models\Empresas::find()->where(['=','id_ciudad',$id])->all();
         if(empty($departamento))
             $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Departamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Departamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Departamento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
     public function safeModel($model){
        try {
            $model->save();
            return 1;
         } catch (\Exception $e) {
             return $e;
         }   
    }
    
    public static function actionDropdownCodigo($pais){
      
       $model= Departamento::find()->where("id != 0 and id_padre = 0 and status = 1")->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
      return  \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
   }
   
    public static function actionToListDepartamento($pais=0){
        $pais=$_POST['pais'];
        if($pais)
        {
             $model= Departamento::find()->where("id != 0 and id_padre = 0 and status = 1")->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
                return  json_encode(\yii\helpers\ArrayHelper::map($model, 'id', 
                 function($model, $defaultValue) {
                    return $model['nombre'];
                }));
        }
   }
}
