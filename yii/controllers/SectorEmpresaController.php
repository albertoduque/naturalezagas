<?php

namespace app\controllers;

use Yii;
use app\models\SectoresEmpresas;
use app\models\SectoresEmpresasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SectorEmpresaController implements the CRUD actions for SectoresEmpresas model.
 */
class SectorEmpresaController extends Controller
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
     * Lists all SectoresEmpresas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SectoresEmpresasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SectoresEmpresas model.
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
     * Creates a new SectoresEmpresas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SectoresEmpresas();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SectoresEmpresas model.
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
     * Deletes an existing SectoresEmpresas model.
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
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#sectorEmpresa-grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>2,'reload'=>'sector-empresa/index']];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>'']];
         }   
    }

    /**
     * Finds the SectoresEmpresas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SectoresEmpresas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SectoresEmpresas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
