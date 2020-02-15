<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProveedorTecnologico */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proveedor-tecnologico-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
    <div class="col-sm-6">
        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'nit')->textInput(['maxlength' => 11,'onkeypress'=>"javascript:runScriptNit(event)"]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group label-floating">
            <?= $form->field($model, 'verificacion')->textInput(['class'=>'form-control','id'=>'verificacion-manejo','readOnly'=>true]) ?>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
            <div class="form-group label-floating">
                <?= $form->field($model, 'nombre')->textInput(['maxlength' => true,'class'=>'form-control upperClass']) ?>
            </div>
        </div>
    </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
