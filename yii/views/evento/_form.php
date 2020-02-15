<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Eventos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eventos-form">

    <?php 
     $form = ActiveForm::begin(['id'=>'event-form-id']); ?> 
    
    <div class="row">
        <div class="col-sm-12">
                <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"> EVENTOS</h4>
    
        </div>
        <div class="col-sm-12">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
         <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?= $form->field($model, 'fecha_hora_inicio')->textInput(['maxlength' => true,'class'=>"form-control id_Desde"])->label('Fecha Inicio') ?>
                </div>
            </div>
        </div>
         <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?= $form->field($model, 'fecha_hora_fin')->textInput(['maxlength' => true,'class'=>"form-control id_Desde"])->label('Fecha Fin') ?>
                </div>
            </div>
        </div>
         <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                      <?= $form->field($model, 'tipo')->dropdownList($listTipoEventos); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                      <?= $form->field($model, 'id_sector')->dropdownList($listSectoresEmpresas); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?=  $form->field($model, 'copyEvent')->dropDownList(['0'=>'Crear Evento Nuevo -- Tablas del Sistema','1'=>'Crear Evento Nuevo --- Tablas Sistema,Empresas,Inscritos']); ?>
                </div>
            </div>
        </div>
         <div class="col-sm-6">
             <div class="input-group copyEventIdClass" style="display: none">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                     <?=  $form->field($model, 'copyEventId')->dropDownList($listEventos); ?>
                </div>
            </div>
        </div>
        
    </div> 
    <br>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-danger']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
