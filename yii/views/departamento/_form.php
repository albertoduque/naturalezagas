<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Departamento */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="departamento-form">

    
     <?php $form = ActiveForm::begin(['id'=>'departamento-form','options' => ['data' => ['id' => $tipociudad]]]);  ?>
    
    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
     <?= $form->field($model, 'types')->hiddenInput()->label(false); ?>
    <?= $form->field($model,'id_pais')->dropDownList(app\models\Pais::toList(),['prompt'=>'Selecione el Pais'])->label('Pais'); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
