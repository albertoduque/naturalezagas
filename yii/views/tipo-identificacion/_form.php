<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TipoIdentificacion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipo-identificacion-form">

    <?php $form = ActiveForm::begin(['id'=>'tipo-identificacion-form','options' => ['data' => ['id' => $drop]]]);  ?>
    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'significado')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_check_digit')->dropDownList([0=>'No',1=>'Si'], ['prompt' => 'Seleccione Uno' ]); ?>

  

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
