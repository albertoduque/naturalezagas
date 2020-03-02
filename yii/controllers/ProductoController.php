<?php

namespace app\controllers;

use Yii;
use app\models\Productos;
use app\models\ProductoSearch;
use app\models\Inscripciones;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\Session;

/**
 * ProductoController implements the CRUD actions for Productos model.
 */
class ProductoController extends Controller
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
     * Lists all Productos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Productos model.
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
     * Creates a new Productos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Productos();
        $model->activo='S';
        $model->tipo_codigo_producto='999';

        if ($model->load(Yii::$app->request->post())) {
            $model->valor=str_replace(",","",$model->valor);
            if($model->save())
                return $this->redirect(['index']);
            else
                var_dump ($model->getErrors ());die;
            return $this->render('create', [
                'model' => $model,'listEventos'=>  \app\models\Eventos::toList()
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,'listEventos'=>  \app\models\Eventos::toList()
            ]);
        }
    }

    /**
     * Updates an existing Productos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
          $model->valor=str_replace(",","",$model->valor);
          if ($model->save())
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,'listEventos'=>  \app\models\Eventos::toList()
            ]);
        }
    }

    /**
     * Deletes an existing Productos model.
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
     * Finds the Productos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Productos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Productos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetProducto(){

        $id = Yii::$app->request->post('id');
        $tipo = Yii::$app->request->post('tipo');
        if($tipo=='i')
        {
            $inscripciones = Inscripciones::findOne($id);
            $respuesta= $inscripciones ? 1 : 0;
            $cantidad = $respuesta ? 1 : 0;
            $valor = $respuesta ? $inscripciones->idProducto->valor : 0;
            $iva = $respuesta ? $inscripciones->idProducto->iva : 0;
            $description = '';
            $id_producto=$inscripciones->idProducto->id;
            $nombre = $inscripciones->idProducto->nombre."-".$inscripciones->idPersona->nombre." ".$inscripciones->idPersona->apellido;
        }
        else{
            $model = $this->findModel($id);
            $respuesta= $model ? 1 : 0;
            $cantidad = 1;
            $valor = $model ? $model->valor : 0;
            $iva = $model ? $model->iva : 0;
            $description = $this->getProductDescription($id);
            $id_producto=$id;
        }

        $subTotal = $cantidad * $valor;
        $totalIva = ($iva*$subTotal)/100;
        $total = $subTotal;
        Yii::$app->response->format = Response::FORMAT_JSON;
        
    return [['respuesta'=>$respuesta,
             'valor'=>$valor,
             'description'=>$description,
             'cantidad'=>$cantidad,
             'iva'=>$iva,
             'total'=>$total,
             'id_producto'=>$id_producto,
             'nombre'=> $tipo=='i' ? $nombre : $this->getProductName($id)
           ]];          
    }

    public function getProductDescription($producto){
        $sql = "SELECT d.id,SUBSTRING(d.nombre,1,100) as nombre "
            . "FROM productos_descripciones pd INNER JOIN `descripcion_productos` d ON(pd.descripcion_id=d.id) WHERE  pd.producto_id =".$producto;
        $lista=Yii::$app->db->createCommand($sql)->queryAll();
        if(!empty($lista)) {
            return \yii\helpers\ArrayHelper::map($lista, 'id', 'nombre');
        }
        return array("1"=>"");
    }

    /**
     * @param $producto
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function getProductName($producto){
        $sql = "SELECT nombre from productos where id =".$producto;
        $lista=Yii::$app->db->createCommand($sql)->queryAll();
       return $lista[0]['nombre'];
    }
}
