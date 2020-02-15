<?php

namespace app\controllers;

use Yii;
use app\models\TipoAsistentes;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use app\models\Personas;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use yii\bootstrap\ActiveForm;
use  yii\web\Session;
use yii\web\Response;

/**
 * TipoAsistenteController implements the CRUD actions for TipoAsistentes model.
 */
class TipoAsistenteController extends Controller
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
     * Lists all TipoAsistentes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $dataProvider = new ActiveDataProvider([
            'query' => TipoAsistentes::find()->where(['=','id_evento',$event_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TipoAsistentes model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TipoAsistentes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TipoAsistentes();

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
                        return $this->redirect(['index']);
                    }
                    else
                       return [['respuesta'=>0]];
                        
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            }else {
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
     * Updates an existing TipoAsistentes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
     * Deletes an existing TipoAsistentes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
         $personas= Personas::find()->where(['=','id_tipo_asistente',$id])->all();
         if(empty($personas))
             $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TipoAsistentes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TipoAsistentes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TipoAsistentes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
}
