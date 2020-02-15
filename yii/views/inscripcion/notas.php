<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\DetalleRecibos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalle-recibos-form">

    <?php  $form = ActiveForm::begin(['id'=>'notas-form-id']);?>
    <?= $form->field($model, 'observaciones')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'id_cambio')->hiddenInput()->label(false) ; ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
