<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MedioPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipo-nota-form">

    <?php $form = ActiveForm::begin(['id'=>'tipo-nota-form','options' => ['data' => ['id' => $drop]]]);  ?>
	<?= $form->field($model,'tipo')->dropDownList(['NA'=>'N/A','NC'=>'Nota Crédito','ND'=>'Nota Débito']); ?>
    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
  

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
