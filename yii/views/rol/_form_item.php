<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\Rol */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="rol-form">

    <?php $form = ActiveForm::begin(['id'=>'rol-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            
    <div class="form-group">    
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>
        <?php ActiveForm::end(); ?>

</div>
