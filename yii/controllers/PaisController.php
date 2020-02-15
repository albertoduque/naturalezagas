<?php

namespace app\controllers;

use Yii;
use app\models\Pais;
use app\models\PaisSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/**
 * PaisController implements the CRUD actions for Pais model.
 */
class PaisController extends Controller
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
     * Lists all Pais models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaisSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pais model.
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
     * Creates a new Pais model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreates()
    {
        $model = new Pais();

        if ($model->load(Yii::$app->request->post())) {
            $model->status=1;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionCreate($drop=0,$name=null)
    {  
        $model = new Pais();
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                 $model->status='1';
                Yii::$app->response->format = Response::FORMAT_JSON;
                if($model->validate())
                 {                  
                    if($this->safeModel($model))
                    {
                        $id=$model->getPrimaryKey();
                        return [['respuesta'=>1,'id'=>$id,'types'=>$name,'url'=>Url::toRoute(['pais/view','id' => $id])]];
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

    /**
     * Updates an existing Pais model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdates($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }
    
     public function actionUpdate($id,$drop=0)
    {
        $model = $this->findModel($id);
        
         if (Yii::$app->request->isAjax) {
             if ($model->load(Yii::$app->request->post())) {
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 if($model->validate())
                 {
                   if($this->safeModel($model))
                   {
                       return [['respuesta'=>2,'url'=>Url::toRoute(['pais/index'])]];
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
                           'model' => $model,'drop'=>$drop
               ]);
             }
        
        } else {
            return $this->render('update', [
                'model' => $model,'drop'=>$drop
            ]);
        }
    }

    /**
     * Deletes an existing Pais model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $ciudad= \app\models\Ciudad::find()->where(['=','id_pais',$id])->all();
         if(empty($ciudad))
             $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Pais model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pais the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pais::findOne($id)) !== null) {
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
    
    public function actionDeleteget($id,$accion)
    {
        try {
            $this->findModel($id)->delete();
            if($accion)
                echo 1;
            else
                 return $this->redirect(['index']);
         } catch (\Exception $e) {
             echo 0;
         }   
    }
    
    public static function toListPais()
    {
        $model= Pais::find()->where(['=','status',1])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    
    
}
