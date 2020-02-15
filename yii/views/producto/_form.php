<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Productos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="productos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_evento')->dropdownList($listEventos)->label("Eventos"); ?>

    <?= $form->field($model, 'descripcion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'valor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'iva')->textInput(['maxlength' => true]) ?>

    <?=  $form->field($model, 'inscripciones')->dropDownList(['N'=>'NO','S'=>'SI']); ?>
	
    <?=  $form->field($model, 'tipo_impuesto')->dropDownList([''=>'', 1=>'GRAVADO',2=>'EXCLUIDO',3=>'EXCENTO']); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
