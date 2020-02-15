<?php

namespace app\controllers;

use Yii;
use app\models\AuthItem;
use app\models\AuthItemChild;
use app\models\AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends Controller
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
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->can('configuracion'))
        {
            $model = new AuthItem();
      
            if ($model->load(Yii::$app->request->post())) {
                $auth = Yii::$app->authManager;
                $parent=$auth->getPermission($model->padre);
                $pieces = explode("/", $parent->name);
                $num_tags = count($pieces);
                $model->name = $pieces[$num_tags-1]."/".strtoupper($model->name);
           
                $permiso = $auth->createPermission($model->name);
                $permiso->description=$model->description;
                $permiso->is_module=1;
                
                $auth->add($permiso);
                $auth->addChild($parent, $permiso);
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                return $this->render('create', [
                    'model' => $model,'list' => $this->toListModules()
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->user->can('configuracion'))
        {
            $model = $this->findModel($id);
    
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                return $this->render('update', [
                    'model' => $model,'list' => $this->toListModules()
                ]);
            }
        }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    public static function toListModules()
    {
        $model= AuthItem::find()->where(['=','type',2])->andWhere(['=','is_module',1])->orderBy('name')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'name', 'name');
    }
    
   
    
    public  function actionTreeJson(){
        return json_encode($this->Tree());
    }
 
    public function Tree($name="SISTEMA",$node=array(),$tree=array()){
        $model= AuthItemChild::find()->where(['=','parent',$name])->asArray()->all();
        if($model)
        {
            foreach($model as $row){
                $child = explode("/", $row['child']);
                $num_tags = count($child);
                
                if($row['have_children']==1)
                {
                    array_push($node,array($this->Tree($row['child'])));
                }
                else{
                   array_push($node,array(
                       "text"=>$child[$num_tags-1],"tags"=>$row['child']));
                }
            }
            $parent = explode("/", $name);
            $num_tags = count($parent);
            array_push($tree,array(
                           "text"=>$parent[$num_tags-1],
                           "nodes"=>$node,
                           "tags"=>$name));   
        }
        return  $tree;
    }
   
}
