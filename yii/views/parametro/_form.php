<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\Parametros */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parametros-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre', ['enableAjaxValidation' => true])->textInput(['disabled' => true]) ?>

    <?= $form->field($model, 'descripcion')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'deleted')->widget(SwitchInput::classname(), 
            [ 'type' => SwitchInput::CHECKBOX,
              'pluginOptions' => ['onText' => 'Inactivo','offText' => 'Activo']
    ]); ?>

     <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
