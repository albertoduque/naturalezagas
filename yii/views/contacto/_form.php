<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;

use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\Contactos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contactos-form">

    <?php $form = ActiveForm::begin(['id'=>'contact-form-id']); ?>

    <div class="row">
        <h4 class="info-text">Información de Contacto de Facturación.</h4>
        <div class="col-sm-12">
                <div class="input-group">
                        <span class="input-group-addon">
                                <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group label-floating">
                           <?= $form->field($model, 'nombre')->textInput(['maxlength' => true,'class'=>'form-control upperClass']) ?>
                        </div>
                </div>
        </div>
        <div class="col-sm-12">
            <div class="input-group">
                    <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                    </span>
                    <div class="form-group label-floating">
                       <?= $form->field($model, 'direccion')->textarea(['rows' => 6,'class'=>'form-control upperClass'])->label('Dirección') ?>
                    </div>
            </div>
        </div>
        <div class="col-sm-6">
                 <div class="input-group">
                     <span class="input-group-addon">
                         <i class="material-icons">phone</i>
                     </span>
                     <div class="form-group label-floating">
                          <?= $form->field($model, 'telefono')->textInput(['maxlength' => 15])->label('Teléfono') ?>
                     </div>
                 </div>
             </div>
             <div class="col-sm-6">
                 <div class="form-group label-floating">
                     <?= $form->field($model, 'telefono_extension')->textInput(['maxlength' => 5])->label('Teléfono Extensión') ?>   
                 </div>
             </div>
            <div class="col-sm-12">
                <div class="input-group">
                        <span class="input-group-addon">
                                <i class="material-icons">phonelink_ring</i>
                        </span>
                        <div class="form-group label-floating">
                           <?= $form->field($model, 'movil')->textInput(['maxlength' => true]) ?>
                        </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="material-icons">email</i>
                    </span>
                    <div class="form-group label-floating">
                         <?= $form->field($model, 'correo')->textInput(['type' => 'email'],['maxlength' => true]) ?>
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
										<?php Pjax::begin(['id' => 'contactos-pais-dropDownList']);   ?>
										<?= $form->field($model, 'pais')->dropDownList($listPais
											,['options'=>['0'=>['Selected'=>true]]
												,'prompt'=>'Pais'
												,'class'=>'dropDownList form-control'
												,'data-preview'=>'contactos-id_padre'
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
									<?php Pjax::begin(['id' => 'contactos-departamento-dropDownList']);   ?>
										<?= $form->field($model, 'id_padre')->dropDownList($listDepartamento
											,['prompt'=>'Departamento'
												,'class'=>'dropDownList form-control'
												,'data-preview'=>'contactos-id_ciudad'
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
                             <?php Pjax::begin(['id' => 'ciudad-dropDownList']);   ?>
                                <?= $form->field($model, 'id_ciudad')->dropDownList($listCiudad,['options'=>['1'=>['Selected'=>true]],'prompt'=>'Ciudad'])->label('Ciudad') ?>
                            <?php Pjax::end(); ?>
                            </div>
                             <?= Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['ciudad/create','name'=>'contacto']),'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalAlterno']) ?>
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
                    <?php Pjax::begin(['id' => 'cargo-dropDownList']);   ?>
                        <?= $form->field($model, 'id_cargo')->dropdownList($listCargos)->label("Cargo"); ?>
                    <?php Pjax::end(); ?>
                    </div>
                      <? // Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['cargo/create','name'=>'contactos']),'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalAlterno']) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
             <div class="input-group">
                <span class="input-group-addon">
                        <i class="material-icons">lock_outline</i>
                </span>
                <div class="form-group label-floating">
                   <?= $form->field($model, 'diamax_facturacion')->textInput(['type' => 'number', 'min' => 0, 'max' => 31],['maxlength' => 2]) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="input-group">
                    <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                    </span>
                    <div class="form-group label-floating">
                       <?= $form->field($model, 'observaciones')->textarea(['rows' => 6]) ?>
                    </div>
            </div>
        </div>
    </div>
    
    <?php if ((Yii::$app->request->isAjax && !$model->inscripcion) || $model->active=2) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
        </div>
    <?php } ?>  
    
    <?php ActiveForm::end(); ?>
    

</div>
