<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InformacionEmpresa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="informacion-empresa-form">

    <?php $form = ActiveForm::begin(['id'=>'info-empresa-form-id']); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true,'class'=>'form-control upperClass']) ?>

    <?= $form->field($model, 'direccion')->textInput(['maxlength' => true,'class'=>'form-control upperClass']) ?>

    <?= $form->field($model, 'telefono')->textInput(['maxlength' => true,'class'=>'form-control upperClass']) ?>

    <?= $form->field($model, 'pagina_web')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ciudad')->textInput(['maxlength' => true]) ?>
	
    <?= $form->field($model, 'version_manual')->textInput(['maxlength' => true]) ?>
    <div class="col-sm-12">
        <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;">Datos de Factura Electrónica</h4>
    </div>

    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'numero_autorizacion_factura')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'fecha_factura')->textInput(['maxlength' => true,'class'=>"form-control id_Desde"]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'desde_factura')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'hasta_factura')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'periodo_renovacion')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;">Datos de Factura Contingencia Electrónica</h4>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'numero_autorizacion_contingencia')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'fecha_contingencia')->textInput(['maxlength' => true,'class'=>"form-control id_Desde"]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'desde_contingencia')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                    <i class="material-icons">lock_outline</i>
                </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'hasta_contingencia')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
