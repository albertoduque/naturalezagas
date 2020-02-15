<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;

use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\Ciudad */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ciudad-form">

    
    <?php $form = ActiveForm::begin(['id'=>'ciudad-form','options' => ['data' => ['id' => $tipociudad]]]);  ?>
    <? //$form->field($model,'id_pais')->dropDownList(app\models\Pais::toList(),['prompt'=>'Selecione el Pais'])->label('Pais'); ?>
	<?php Pjax::begin(['id' => 'ciudad-pais-dropDownList']);   ?>
		<?= $form->field($model, 'id_pais')->dropDownList(
			$listPais
			,['options'=>['0'=>['Selected'=>true]]
				,'prompt'=>'Pais'
				,'class'=>'dropDownList form-control'
				,'data-preview'=>'ciudad-id_padre'
				,'data-set'=>'pais'
				,'data-url'=>Url::toRoute(['departamento/to-list-departamento'])
			]) ->label('País') ?>
	<?php Pjax::end(); ?>
	<?php Pjax::begin(['id' => 'departamento-dropDownList']);   ?>
		<?= $form->field($model, 'id_padre')->dropDownList($listDepartamento,['options'=>['1'=>['Selected'=>true]],'prompt'=>'Departamento'])->label('Departamento') ?>
	<?php Pjax::end(); ?>
    
    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
     <?= $form->field($model, 'types')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
