<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormasPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formas-pago-form">

     <?php $form = ActiveForm::begin(['id'=>'forma-pago-form']); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
