<?php

namespace app\controllers;

use Yii;
use app\models\ProveedorTecnologico;
use app\models\ProveedorTecnologicoSearch;
use yii\web\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProveedorTecnologicoController implements the CRUD actions for ProveedorTecnologico model.
 */
class ProveedorTecnologicoController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all ProveedorTecnologico models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProveedorTecnologicoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProveedorTecnologico model.
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
     * Creates a new ProveedorTecnologico model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProveedorTecnologico();

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
                return $this->render('create', [
                    'model' => $model
                ]);
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
     * Updates an existing ProveedorTecnologico model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProveedorTecnologico model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProveedorTecnologico model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProveedorTecnologico the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProveedorTecnologico::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
