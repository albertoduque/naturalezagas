<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\DetalleRecibos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalle-recibos-form">
    <?php if(empty($listInscrito)){ ?>
      <?= Html::label("No hay participantes inscritos de esta empresa para realizar el cambio.") ?>
      <br>
      <?= Html::submitButton('Aceptar', ['class' => 'btn btn-success','onclick'=>'js:exitInscritos();']) ?>
    <?php } else { ?>
      <?php  $form = ActiveForm::begin(['id'=>'cambiar-participante-form-id']);?>
      <?php if($model->estado==2){ ?>
          <?= Html::label("Participante Actual : ".$model->idPersona->nombre." ".$model->idPersona->apellido) ?>
          <?= $form->field($model, 'id_persona')->dropDownList($listInscrito)->label('Participante Cambiar : ') ; ?>
      <?php } ?>
      <?= $form->field($model, 'id_cambio')->hiddenInput()->label(false) ; ?>
      <?= $form->field($model, 'estado')->dropDownList(['1'=>'ACTIVO','0'=>'INACTIVO']); ?>
      <div class="form-group">
          <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
      </div>
      <?php ActiveForm::end(); ?>
      <?php } ?>

</div>
