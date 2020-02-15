<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RecibosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recibos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fecha_pago') ?>

    <?= $form->field($model, 'valor_descuento') ?>

    <?= $form->field($model, 'valor_retencion') ?>

    <?= $form->field($model, 'valor_pagado') ?>

    <?php // echo $form->field($model, 'tipo_pago') ?>

    <?php // echo $form->field($model, 'valor_subtotal') ?>

    <?php // echo $form->field($model, 'valor_iva') ?>

    <?php // echo $form->field($model, 'id_forma_pago') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'modified_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
