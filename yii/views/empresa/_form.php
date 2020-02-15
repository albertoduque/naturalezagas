<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;

use kartik\switchinput\SwitchInput;
/* @var $this yii\web\View */
/* @var $model app\models\Empresas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="empresas-form">
    <?php $form = ActiveForm::begin(['id'=>'empresa-form-id']); ?>    
        <div class="row">
                <div class="col-sm-12">
                        <h4 class="info-text"> INGRESE INFORMACIÓN BÁSICA DE LA EMPRESA</h4>
                </div>
                <div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon">
								<i class="material-icons">lock_outline</i>
						</span>
						<div class="form-group label-floating">
								<?= $form->field($model, 'id_tipo_identificacion')->dropDownList($listTI,['options'=>['1'=>['Selected'=>true]],'prompt'=>'Seleccione','class'=>'form-control'])  ?>
						</div>
					</div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group label-floating">
                             <?= $form->field($model, 'identificacion')->textInput(['class'=>'form-control upperClass','maxlength' => 20,'onkeypress'=>$model->isNewRecord ? "javascript:runScript(event)" : "fock()",'readOnly'=>$model->isNewRecord ? false : true]) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group label-floating">
                         <?= $form->field($model, 'verificacion')->textInput(['class'=>'form-control','id'=>'escenario-manejo','readOnly'=>true]) ?>
                    </div>
                </div>
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
                        <?= $form->field($model, 'telefono_extension')->textInput(['maxlength' => 5])->label('Extensión') ?>   
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-group">
                            <span class="input-group-addon">
                                    <i class="material-icons">phonelink_ring</i>
                            </span>
                            <div class="form-group label-floating">
                               <?= $form->field($model, 'movil')->textInput(['maxlength' => true])->label('Móvil') ?> 
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
									<?php Pjax::begin(['id' => 'empresas-pais-dropDownList']);   ?>
										<?= $form->field($model, 'pais')->dropDownList($listPais
											,['options'=>['0'=>['Selected'=>true]]
												,'prompt'=>'Pais'
												,'class'=>'dropDownList form-control'
												,'data-preview'=>'empresas-id_padre'
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
									<?php Pjax::begin(['id' => 'empresas-departamento-dropDownList']);   ?>
										<?= $form->field($model, 'id_padre')->dropDownList($listDepartamento
											,['prompt'=>'Departamento'
												,'class'=>'dropDownList form-control'
												,'data-preview'=>'empresas-id_ciudad'
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
                             <?php //Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['ciudad/create','name'=>'empresa']),'data'=>['title'=>'Datos de la ciudad'],'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalButton']) 
                             ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-group">
                            <span class="input-group-addon">
                                    <i class="material-icons">lock_outline</i>
                            </span>
                            <div class="form-group label-floating">
                                <?= $form->field($model, 'afiliado_gremio')->widget(SwitchInput::classname(), 
                                    [ 'type' => SwitchInput::CHECKBOX,
                                      'pluginOptions' => ['onText' => 'NO','offText' => 'SI']
                                    ]); ?>
                            </div>
                    </div>
                </div>
            <div class="col-sm-12" style="display: none">
                        <div class="input-group">
                                <span class="input-group-addon">
                                        <i class="material-icons">lock_outline</i>
                                </span>
                                <div class="form-group label-floating">
                            <div class="form-group2">
                            <?php Pjax::begin(['id' => 'sector-dropDownList']);   ?>
                                <?= $form->field($model, 'id_sector_empresa')->dropdownList($listSectoresEmpresas)->label("Sector"); ?>
                            <?php Pjax::end(); ?>
                            </div>
                             <?= Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['/sector-empresa/create']),'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalButton']) ?>
                        </div>
                    </div>
            </div>
            <div class="col-sm-12">
                <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;">DATOS  DE FACTURACIÓN ELECTRÓNICA</h4>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                                <span class="input-group-addon">
                                        <i class="material-icons">lock_outline</i>
                                </span>
                    <div class="form-group label-floating">
                            <?= $form->field($model, 'id_proveedor_tecnologico')->dropDownList($listPt,['options'=>['1'=>['Selected'=>true]],'prompt'=>'Seleccione','class'=>'form-control'])  ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                                <span class="input-group-addon">
                                        <i class="material-icons">lock_outline</i>
                                </span>
                    <div class="form-group label-floating">
                            <?= $form->field($model, 'correo_facturacion_electronica')->textInput(['maxlength' => true]); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php if(!$model->isNewRecord){
        echo $this->render('//contacto/index', array('dataProvider'=>$contacto,'searchModel'=>$contacto,'url'=>Url::toRoute(['contacto/create','id'=>$model->id]))); 
    }
        if(!$model->isNewRecord || $visible){
    ?>
    <div class="form-group">
        <?= $form->field($model, 'redirectEmpresa')->hiddenInput()->label(false); ?>
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>
    <?php } ?>  
    <?php ActiveForm::end(); ?>  
</div>
