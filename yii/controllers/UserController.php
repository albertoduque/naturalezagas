<?php

namespace app\controllers;

use app\models\Eventos;
use Yii;
use app\models\User;
use app\models\Rol;
use app\models\UsuarioEvento;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\Funciones;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /*public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }*/
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'about','user-index'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'error','user-index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['about', 'logout', 'index','user-index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    
    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $eventos=$model->checkListEvents();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password_hash);
            $model->generateAuthKey();
            $cedula=$model->cedula;
            $model->save();
            if($model->eventos) {
                $id = $model->getPrimaryKey();
                foreach ($model->eventos as $event) {
                    $user_events = new UsuarioEvento();
                    $user_events->evento = $event;
                    $user_events->usuario = $id;
                    $user_events->save();
                }
            }
            $auth = Yii::$app->authManager;
            $modelRol = $this->findModelRol($model->rol_id);
            $rol = $auth->getRole($modelRol->nombre);
            if($rol==NULL)
            {
                $rol = $auth->createRole($modelRol->nombre);
                $auth->add($rol);
            }
            $user= $this->findModel(0,$cedula);
            $auth->assign($rol, $user[0]['id']);
           
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,'listEventos'=>$eventos
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $eventos=$model->checkListEvents();
        $modelUserEvent = UsuarioEvento::findAll(["usuario"=>$id]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->is_all= $model->id_evento==1000 ? 1:0;
            $model->save();
            if($modelUserEvent)
                 foreach ($modelUserEvent as $event)
                     $event->delete();
             if($model->eventos) {
                 foreach ($model->eventos as $event) {
                     $user_events = new UsuarioEvento();
                     $user_events->evento = $event;
                     $user_events->usuario = $id;
                     $user_events->save();
                 }
             }

            $auth = Yii::$app->authManager;
            $auth->revokeAll($id);
            $modelRol = $this->findModelRol($model->rol_id);
            $rol = $auth->getRole($modelRol->nombre);
            $auth->assign($rol, $id);
         
            return $this->redirect(['index']);
        } else {
            $dataPermiso= array();
             if($modelUserEvent)
             {
                 foreach ($modelUserEvent as $events)
                 {
                     $dataPermiso[] = $events->evento;
                 }
             }
             $model->eventos = $dataPermiso;
            return $this->render('update', [
                'model' => $model,'listEventos'=>$eventos
            ]);
        }
    }
    
    /**
     * Actualiza contraseña de manera interna
     * */
    public function actionUpdatep()
    {
        if(isset($_POST['id']))
            $id = $_POST['id'];
        
        $model = $this->findModel($id);
        $model->setPassword($_POST['pass']);
        if ($model->validate()) {
            $model->save();
            $jsondata[0]['respuesta'] = 'Cambio de contraseña exitoso';
            $jsondata[0]['error'] = 0;
        }
        else{
            $jsondata[0]['respuesta'] = 'Error al cambiar contraseña';
            $jsondata[0]['error'] = \yii\widgets\ActiveForm::validate($model);
        }
        
        echo json_encode($jsondata); 
        
    }
    
   

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status=0;
        $auth = Yii::$app->authManager;
        $auth->revokeAll($id);
        if($model->validate()) {
            $model->save();
            return $this->redirect(['index']);
        }
        else {
            return 0;
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id,$buscar=0)
    {
        if($buscar)
        {
            $model = User::find()->where(['=','cedula',$buscar])->asArray()->all();
            return $model;
        }
        else {
            if (($model = User::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
      protected function findModelRol($id)
    {
        if (($model = Rol::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
     /**
     * Envia correo electronico a un usuario
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    
    public static function sendEmail($orden_de_compra_idorden_de_compra)
    {
        
        $estado=  OrdenDeCompraController::verificarEstado($orden_de_compra_idorden_de_compra);
        $correos= \app\models\ParametrosSistemaCorreo::find()->all();
       
        $i=0;
        foreach ($correos as $correo)
        {
            $mail[$i]=$correo->correo;
            $i++;
        }
      
        return \Yii::$app->mailer->compose(['html' => 'notificacion-html', 'text' => 'notificacion-html'], ['estado' => $estado])
             ->setFrom([\Yii::$app->params['adminEmail'] => 'Quimicos Integrales SAS'])
             ->setTo($mail)
             ->setSubject('Cambio de Estado : ' . $orden_de_compra_idorden_de_compra)
             ->send();

        return false;
    }
    
    
}
