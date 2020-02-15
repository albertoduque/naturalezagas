<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Monedas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="monedas-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<?= $form->field($model,'id_pais')->dropDownList(app\models\Pais::toList(),['prompt'=>'Selecione el Pais'])->label('Pais'); ?>

    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numero')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'divisa')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
