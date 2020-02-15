<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Recibos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recibos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fecha_pago')->textInput() ?>

    <?= $form->field($model, 'valor_descuento')->textInput() ?>

    <?= $form->field($model, 'valor_retencion')->textInput() ?>

    <?= $form->field($model, 'valor_pagado')->textInput() ?>

    <?= $form->field($model, 'tipo_pago')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'valor_subtotal')->textInput() ?>

    <?= $form->field($model, 'valor_iva')->textInput() ?>

    <?= $form->field($model, 'id_forma_pago')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'modified_at')->textInput() ?>

    <?= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
