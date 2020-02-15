<?php

namespace app\controllers;

use Yii;
use app\models\Rol;
use app\models\RolSearch;
use app\models\AuthItem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * RolController implements the CRUD actions for Rol model.
 */
class RolController extends Controller
{
    /**
     * @inheritdoc
     */
   /*public function behaviors()
    {
       $behaviors['access']=[
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $post=Yii::$app->request->post();
                            $action=Yii::$app->controller->action->id;
                            $controller=Yii::$app->controller->id;
                            $route="$controller/$action";
                           // print_r(\Yii::$app->user->getIdentity());exit();
                            if(\Yii::$app->user->can($route))
                                return true;
                        }
                    ],
                ],
        ];
        return $behaviors;     
    }*/

    /**
     * Lists all Rol models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->can('configuracion'))
        {
            $searchModel = new RolSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Displays a single Rol model.
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
     * Creates a new Rol model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->can('configuracion'))
        {
            $model = new Rol();
            $permisos=$model->checkListPermision();
            if ($model->load(Yii::$app->request->post())) {
                if($model->nombre)
                {
                    $auth = Yii::$app->authManager;
                    $model->nombre = strtoupper($model->nombre);
                    $author = $auth->createRole($model->nombre);
                    $auth->add($author);
                    if($model->permiso)
                    {
                        foreach($model->permiso as $id){
                            $permision=$auth->getPermission($id);
                            $auth->addChild($author, $permision);
                        }
                    }
                    $model->save();
                }
                return $this->redirect(['index']);
            }else{
                return $this->render('create', [
                    'model' => $model,'permisos'=>$permisos
                ]);
            }
        }
        else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Updates an existing Rol model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id,$file=0)
    {
        if(Yii::$app->user->can('configuracion'))
        {
            $model = $this->findModel($id);
            $rol = $model->nombre;
            $auth = Yii::$app->authManager;
            $permisos=$model->checkListPermision();
            $getPermiso = $auth->getPermissionsByRole($rol);
            $dataPermiso= array();
            if(!empty($getPermiso))
            {
                foreach ($getPermiso as $permiso)
                {
                    $dataPermiso[] = $permiso->name;
                }
            }
            $model->permiso = $dataPermiso;
            if ($model->load(Yii::$app->request->post())) {
                $model->nombre=  strtoupper($model->nombre);
                if($auth->getRole($model->nombre))
                {
                    if($model->permiso)
                    {
                        $author = $auth->getRole($model->nombre);
                        foreach ($getPermiso as $permiso)
                        {
                            $auth->removeChildren($author);
                        }
                        foreach($model->permiso as $id){
                            $permision=$auth->getPermission($id);
                            $auth->addChild($author, $permision);
                        }
                    }
                }
                else
                {
                    $rolupdate = $auth->createRole($model->nombre);
                    $users=Yii::$app->user->identity->getRoleUsers($rol);
                    foreach ($users as $user)
                    {
                        $item = $auth->getRole($rol);
                        $item = $item ? : $auth->getPermission($rol);
                        $auth->revoke($item,$user['user_id']);
                        $auth->assign($rolupdate, $user['user_id']);
                    }
                    $author = $auth->getRole($rol);
                    foreach ($getPermiso as $permiso)
                    {
                        $auth->removeChildren($author);
                    }
                    $auth->update($rol,$rolupdate);

                    foreach($model->permiso as $id)
                    {
                        $permision=$auth->getPermission($id);
                        $auth->addChild($rolupdate, $permision);
                    }
                }

                $model->save();

                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,'permisos'=>$permisos
                ]);
            }
        }
        else{
            throw new ForbiddenHttpException;
        }
    }
    
    public function actionCreateItem()
    {
      
      if(Yii::$app->user->can('configuracion'))
      {
        $model = new AuthItem();
        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $permiso = $auth->createPermission($model->name);
            $auth->add($permiso);
            return $this->redirect(['view', 'id' => $model->name]);
          }else{
            return $this->render('_form_item', [
                'model' => $model
            ]);
        }
      }
      else{
        throw new ForbiddenHttpException;
      }
    }

    /**
     * Deletes an existing Rol model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->can('configuracion')){
            $auth = Yii::$app->authManager;

            $model=$this->findModel($id);
            $rol = $auth->getRole($model->nombre);
            $users=Yii::$app->user->identity->getRoleUsers($model->nombre);
            if(!$users)
            {
                $auth->remove($rol);
                $model->delete();
            }

            return $this->redirect(['index']);
        }
        else{
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Finds the Rol model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rol the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rol::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
