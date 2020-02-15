<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
      <?= $form->field($model, 'nombre')->label('Nombre'); ?>
      <?= $form->field($model, 'cedula')->label('Cedula'); ?>
      <?= $form->field($model, 'telefono')->label('Telefono'); ?>
      <?= $form->field($model, 'email') ?>
      <?= $form->field($model, 'username')->label('Usuario'); ?>
      
     <?php if(empty($model->id)){  ?>
                <?= $form->field($model, 'password_hash')->passwordInput()->label('Contraseña',['class'=>'label-class'])?>
    <?php } ?>
    <?= $form->field($model,'rol_id')->dropDownList(User::toList(),['prompt'=>'Selecione Rol'])->label('Rol'); ?>
    <?= $form->field($model,'status')->dropDownList(['10'=>'Activo','0'=>'Inactivo']); ?>
    <div class="form-group">
        <?= $form->field($model, 'eventos')->checkboxList($listEventos  , ['separator'=>'<br/>'])->label("EVENTOS PERMITIDOS") ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <button class='btn btn-danger' type='button' id='btUserReiniciarPassword' >Reiniciar contraseña</button>
    </div> 
     <?php ActiveForm::end(); ?>
     <div class="col-lg-4 reiniciarpassword">
        <div class="input-group">
             <input type="text" id="user-password_hash" min="1" max="15"  class="form-control" placeholder="Contraseña">
             <span class="input-group-btn">
                <button class="btn btn-success" type="button" data-id=<?=  $model->id ?> data-url="<?php echo Url::toRoute('user/updatep') ?>" data-home="<?php echo Url::toRoute('user/index') ?>" id='BtReiniciarpass'>Reiniciar</button>
            </span>
         </div>
     </div><!-- /.col-lg-6 -->
</div>
