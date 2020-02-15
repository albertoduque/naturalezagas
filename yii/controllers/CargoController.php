<?php

namespace app\controllers;

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
use SoapClient;
use SOAPHeader;
//require_once 'ClientDispapelesApi.class.php';
/**
 * CargoController implements the CRUD actions for Cargos model.
 */
class CargoController extends Controller
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
}
