<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\SectoresEmpresas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sectores-empresas-form">

    <?php $form = ActiveForm::begin(['id'=>'sector-empresa-form']);?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'deleted')->widget(SwitchInput::classname(), 
            [ 'type' => SwitchInput::CHECKBOX,
              'pluginOptions' => ['onText' => 'Inactivo','offText' => 'Activo']
            ]); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
  
  <span class="slider round"></span>

    <?php ActiveForm::end(); ?>

</div>
