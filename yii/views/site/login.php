<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';

?>

        <div id="loginbox" style="margin-top:100px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-success" >
                    <div class="panel-heading">
                        <div class="panel-title" style="padding: 10px;"> <?= $this->title?></div>
                        <div style="float:right; font-size: 80%; position: relative; top:-15px"><?= Html::a('Olvidaste la contraseña?', ['site/request-password-reset']) ?></div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >
                        
                         <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                        
                            <div style="margin-bottom: 25px" class="input-group col-lg-12 col-md-12 col-xs-12" >
                                 <?= $form->field($model, 'username', ['template' => " <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>{input}{hint}"])->textInput(['placeholder' => "usuario"]);  ?>
                                 
                            </div>
                            
                            <div style="margin-bottom: 25px" class="input-group col-lg-12 col-md-12 col-xs-12">
                              
                                
                                <?= $form->field($model, 'password', ['template' => " <span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>{input}{hint}"])->passwordInput(['placeholder' => "Contraseña"]);  ?>
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                     <?= Html::submitButton('Ingresar', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
                            </div>
                          <?php echo $form->errorSummary($model, ['class' => 'alert alert-danger']); ?>
                            <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                           Ingrese los datos para acceder al sistema
                                        </div>
                                    </div>
                             </div>    
                        <?php ActiveForm::end(); ?>  
                    </div>                     
            </div>  
        </div>




