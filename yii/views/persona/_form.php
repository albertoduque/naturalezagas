<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\Personas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="personas-form">

   <?php 
   $validationUrl = ['persona/validacion'];
    if (!$model->isNewRecord)
        $validationUrl['id'] = $model->id;
   
   $form = ActiveForm::begin(['id'=>'person-form-id','enableClientValidation' => true,
    'enableAjaxValidation' => false,'validationUrl'=>$validationUrl]); ?>
    
   <div class="row">
    
    <div class="col-sm-6">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="material-icons">email</i>
                    </span>
					<div class="form-group label-floating">
						<?= $form->field($model, 'id_tipo_identificacion')->dropDownList($listTI,['options'=>['1'=>['Selected'=>true]],'prompt'=>'Seleccione','class'=>'form-control'])  ?>
					</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                <div class="form-group label-floating">
                    <?= $form->field($model, 'identificacion')->textInput(['class'=>'form-control upperClass'],['maxlength' => 20])->label('Identificación') ?>   
                </div>
                    </div>
            </div>
    <div class="col-sm-12">
                <div class="input-group">
                        <span class="input-group-addon">
                                <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group label-floating">
                           <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
                        </div>
                </div>
            </div>
     <div class="col-sm-12">
                <div class="input-group">
                        <span class="input-group-addon">
                                <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group label-floating">
                           <?= $form->field($model, 'apellido')->textInput(['maxlength' => true]) ?>
                        </div>
                </div>
            </div>
    
    <div class="col-sm-6">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="material-icons">email</i>
            </span>
            <div class="form-group label-floating">
                 <?= $form->field($model, 'telefono')->textInput(['maxlength' => 11])->label('Teléfono') ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group label-floating">
            <?= $form->field($model, 'movil')->textInput(['maxlength' => true])->label('Móvil') ?>   
        </div>
    </div>


    <div class="col-sm-12">
		<div class="input-group">
				<span class="input-group-addon">
						<i class="material-icons">lock_outline</i>
				</span>
				<div class="form-group label-floating">
				   <?= $form->field($model, 'direccion')->textarea(['rows' => 6])->label('Dirección') ?>
				</div>
		</div>
	</div>

    <div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                        <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                    <div class="form-group2">
                        <?php Pjax::begin(['id' => 'personas-pais-dropDownList']);   ?>
                            <?= $form->field($model, 'pais')->dropDownList($listPais
								,['options'=>['0'=>['Selected'=>true]]
									,'prompt'=>'Pais'
									,'class'=>'dropDownList form-control'
									,'data-preview'=>'personas-id_padre'
									,'data-set'=>'pais'
									,'data-url'=>Url::toRoute(['departamento/to-list-departamento'])
								])->label('País') ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
        </div>
	</div>
	<div class="col-sm-6">
        <div class="input-group">
                <span class="input-group-addon">
                        <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                    <div class="form-group2">
                        <?php Pjax::begin(['id' => 'personas-departamento-dropDownList']);   ?>
                            <?= $form->field($model, 'id_padre')->dropDownList($listDepartamento
								,['prompt'=>'Departamento'
									,'class'=>'dropDownList form-control'
									,'data-preview'=>'personas-id_ciudad'
									,'data-set'=>'id_padre'
									,'data-url'=>Url::toRoute(['ciudad/to-list-ciudad'])
								])->label('Departamento') ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
        </div>
	</div>
	<div class="col-sm-6">
		<div class="input-group">
			<span class="input-group-addon">
					<i class="material-icons">lock_outline</i>
			</span>
			<div class="form-group label-floating">
				<div class="form-group2">
				 <?php Pjax::begin(['id' => 'persona-ciudad-dropDownList']);   ?>
					<?= $form->field($model, 'id_ciudad')->dropDownList($listCiudad,['prompt'=>'Ciudad'])->label('Ciudad') ?>
				<?php Pjax::end(); ?>
				</div>
				 <? //Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['ciudad/create','tipociudad'=>1,'name'=>'personas']),'data'=>['title'=>'Datos de la ciudad'],'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalAlterno']) ?>
			</div>
		</div>
	</div>

    <div class="col-sm-12">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="material-icons">email</i>
                    </span>
                    <div class="form-group label-floating">
                         <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

       
       
       <div class="col-sm-12">
                <div class="input-group">
                        <span class="input-group-addon">
                                <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group label-floating">
                    <div class="form-group2">
                    <?php Pjax::begin(['id' => 'persona-dropDownList']);   ?>
                        <?= $form->field($model, 'id_cargo')->dropdownList($listCargos)->label("Cargo"); ?>
                    <?php Pjax::end(); ?>
                    </div>
                      <?= Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['cargo/create','name'=>'personas']),'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalAlterno']) ?>
                </div>
            </div>
        </div>
      
       
       
       <div class="col-sm-12">
                    <div class="input-group">
                            <span class="input-group-addon">
                                    <i class="material-icons">lock_outline</i>
                            </span>
                            <div class="form-group label-floating">
                                 <?= !Yii::$app->request->isAjax ? $form->field($model, 'estado')->widget(SwitchInput::classname(), 
                                    [ 'type' => SwitchInput::CHECKBOX,
                                      'pluginOptions' => ['onText' => 'INACTIVO','offText' => 'ACTIVOs']
                                    ]) : $form->field($model, 'estado')->dropDownList(['1'=>'ACTIVO','0'=>'INACTIVOs']); ?>
                            </div>
                    </div>
                </div>
       
         <div class="col-sm-12">
                <div class="input-group">
                     <span class="input-group-addon">
                         <i class="material-icons">lock_outline</i>
                     </span>
                     <div class="form-group label-floating">
                             <?= $form->field($model, 'id_tipo_asistente')->dropdownList($listAsistente)->label("Asistente"); ?>
                     </div>
                </div>
            </div>
</div>
    <?= $form->field($model, 'idEmpresa')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false);  ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::button('Nuevo Inscrito', ['class' => 'btn btn-info','onclick'=>'js:clearFormInscritos();']) ?>
        <?= Html::button('Salir', ['class' => 'btn btn-danger','onclick'=>'js:exitInscritos();']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
