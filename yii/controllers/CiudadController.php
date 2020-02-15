<?php

namespace app\controllers;

use Yii;
use app\models\Pais;
use app\models\Departamento;
use app\models\Ciudad;
use app\models\CiudadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
/**
 * CiudadController implements the CRUD actions for Ciudad model.
 */
class CiudadController extends Controller
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
     * Lists all Ciudad models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CiudadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ciudad model.
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
     * Creates a new Ciudad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Ciudad();
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
        $model = new Ciudad();
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->status=1;
                if($model->validate())
                 {
                    $pais=$model->id_pais;
                    $departamento=$model->id_padre;
                    if($this->safeModel($model))
                    {
                        $id=$model->getPrimaryKey();
                        return [[
							'respuesta'=>1
							,'pais'=>$pais
							,'departamento'=>$departamento
							,'types'=>$name
							,'id'=> $id
							,'lista'=>$this->actionDropdownCodigo($pais,$departamento)
							,'url'=>Url::toRoute(['ciudad/index'])]];
                    }
                    else
                       return [['respuesta'=>0]];
                        
                 }
                 else {
                    return ActiveForm::validate($model);
                }
            }else {
                return $this->renderAjax('create', [
                    'model' => $model,'tipociudad'=>$tipociudad,
					'listPais'=>  Pais::toList(),'listDepartamento'=> Departamento::toList($pais),
                ]);
            }
        }
        else{
                return $this->render('create', [
                    'model' => $model,'tipociudad'=>$tipociudad,
					'listPais'=>  Pais::toList(),'listDepartamento'=> Departamento::toList($pais),
                ]);
            
        } 
    }

    /**
     * Updates an existing Ciudad model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id,$tipociudad=0)
    {
        $model = $this->findModel($id);
        $model->idDepartamento->id_padre;
         if (Yii::$app->request->isAjax) {
             if ($model->load(Yii::$app->request->post())) {
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 if($model->validate())
                 {
                   if($this->safeModel($model))
                   {
                       return [['respuesta'=>2,'url'=>Url::toRoute(['ciudad/index'])]];
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
                    'model' => $model,'tipociudad'=>$tipociudad,
					'listPais'=>  Pais::toList(),'listDepartamento'=> Departamento::toList($pais),
                ]);
             }
        
        } else {
            return $this->render('update', [
                'model' => $model,'tipociudad'=>$tipociudad,
				'listPais'=>  Pais::toList(),'listDepartamento'=> Departamento::toList($pais),
            ]);
        }
    }
    
    

    /**
     * Deletes an existing Ciudad model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
         $ciudad= \app\models\Empresas::find()->where(['=','id_ciudad',$id])->all();
         if(empty($ciudad))
             $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ciudad model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ciudad the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ciudad::findOne($id)) !== null) {
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
    
    public static function actionDropdownCodigo($pais,$departamento){
   		//$model= Ciudad::find()->where(['=','status',1])->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
		$model= Ciudad::find()->where("id != 0 and id_padre = 0 and status = 1")->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
		return  \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
   }
   
    //public static function actionToListCiudad($pais=0){
    public static function actionToListCiudad($id_padre=0){
        $pais=$_POST['pais'];
        $id_padre=$_POST['id_padre'];
        //if($pais)
        if($pais || $id_padre)
        {
             //$model= Ciudad::find()->where(['=','status',1])->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
			 if($pais){
				$model= Ciudad::find()->where("id != 0 and status = 1")->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
			 }
			 if($id_padre){
				$model= Ciudad::find()->where("id != 0 and status = 1")->andWhere(['id_padre'=>$id_padre])->orderBy('nombre')->asArray()->all();
			 }
                return  json_encode(\yii\helpers\ArrayHelper::map($model, 'id', 
                 function($model, $defaultValue) {
                    return $model['nombre'];
                }));
        }
   }
}
