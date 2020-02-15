<?php

namespace app\controllers;

use Yii;
use app\models\DetalleRecibos;
use app\models\DetalleRecibosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;

/**
 * DetalleReciboController implements the CRUD actions for DetalleRecibos model.
 */
class DetalleReciboController extends Controller
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
     * Lists all DetalleRecibos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DetalleRecibosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DetalleRecibos model.
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
     * Creates a new DetalleRecibos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=0,$idInscripcion=0)
    {
        $model = new DetalleRecibos();
        $sql = "SELECT id,fecha_pago,valor FROM detalle_recibos WHERE id_factura =".$id;
        $where = "id_factura = ".$id;
        if($idInscripcion>0)
        {
            $sql .= " and id_inscripcion=".$idInscripcion;
            $where .= " and id_inscripcion=".$idInscripcion;
        }
        $modelFactura=Yii::$app->db->createCommand($sql)->queryAll();
        $modelFactura = new ActiveDataProvider([
            'query' => DetalleRecibos::find()->where($where),
        ]);
         if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->id_factura=  $id ? intval($id) : NULL;
                $model->id_inscripcion=  $idInscripcion ? intval($idInscripcion) : 0;
                $model->fecha_pago = $this->FormatoFechas($model->fecha_pago);
                if($model->validate())
                 {
                     $transaction = Yii::$app->db->beginTransaction();
                    try{
                        if($model->save(false))
                        {
                            $idRecibo=$model->getPrimaryKey();
                            if($id)
                            {
                                $facturas = \app\models\Facturas::findOne($id);
                                $facturas->id_estado_factura=$model->tipo_pago;
                                $facturas->save(false);
                                if($idInscripcion>0){
                                    $facturas = \app\models\DetalleFactura::find()
                                        ->where(['detalle_factura.id_inscripcion' => $idInscripcion])
                                        ->andWhere(['detalle_factura.id_factura' => $id])->one();
                                    $facturas->id_estado_factura=$model->tipo_pago;
                                    $facturas->save(false);
                                }
                                else{
                                    $facturas = \app\models\DetalleFactura::find()
                                        ->where(['detalle_factura.id_factura' => $id])->one();
                                    $facturas->id_estado_factura=$model->tipo_pago;
                                    $facturas->save(false);
                                }
                            }
                            $transaction->commit();
                            return [['respuesta'=>1,'id'=>$idRecibo]];
                        }
                        else
                           return [['respuesta'=>0]];
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return [['respuesta'=>0,'data'=> ActiveForm::validate($model)]];
                    }
                        
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            }else {
                return $this->renderAjax('create', [
                    'model' => $model,'listFormaPago'=>  \app\models\FormasPago::toList(),'vpagos'=>$modelFactura
                ]);
            }
        }
        else{
                return $this->render('create', [
                    'model' => $model
                ]);
            
        } 
    }
    
    
    public function actionDeleteAjax($id,$accion)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $this->findModel($id)->delete();
            if($accion)
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>1,'reload'=>'#detalle-grid']];
            else  
                return [['respuesta'=>1,'msgError'=>'','msgSuccess'=>'Se elimino con exito','action'=>3]];
                // return $this->redirect(['index']);
         } catch (\Exception $e) {
             return [['respuesta'=>0,'msgError'=>'Error al Eliminar Registro','msgSuccess'=>$e]];
         }   
    }
    
    

    /**
     * Updates an existing DetalleRecibos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_detalle_factura]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DetalleRecibos model.
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
     * Finds the DetalleRecibos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DetalleRecibos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DetalleRecibos::findOne($id)) !== null) {
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
    
    function FormatoFechas($fecha){
        $dia = substr($fecha, 0, 2);
        $mes  = substr($fecha, 3, 2);
        $ano = substr($fecha, -4);
        // fechal final realizada el cambio de formato a las fechas europeas5
        $fecha = $ano.'-'.$mes.'-'.$dia;
   
    return $fecha;
    }
}
