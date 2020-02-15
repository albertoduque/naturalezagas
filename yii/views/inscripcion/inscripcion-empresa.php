<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;
use kartik\switchinput\SwitchInput;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $model app\models\Empresas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="empresas-form">

  <div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="wizard-container">
                <div class="card wizard-card" data-color="green" id="wizard">
                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#empresa" data-toggle="tab">Empresas</a></li>
                            <li><a href="#contacto" data-toggle="tab">Facturación</a></li>
                            <li><a href="#description" data-toggle="tab">Participantes</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane" id="empresa">
                            <div class="row">
                                 <?php $form = ActiveForm::begin(['id'=>'empresa-inscripcion-form-id']); ?>
                                    <?= $form->field($model,'id_empresa')->hiddenInput()->label(false); ?> 
                                    <?=  $form->field($model, 'guardar')->hiddenInput(['value'=> 1])->label(false)  ?>
                                        <?php echo $this->render(
											'//empresa/_form'
											, array('model'=>$empresa
												,'listSectoresEmpresas'=>$listSectoresEmpresas
												,'listPais'=>$listPais
												,'listDepartamento'=>$listDepartamento
												,'listCiudad'=>$listCiudad
												, 'listPt'=>$listPt
												, 'listTI'=>$listTI
											)); ?>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="contacto">
                            <div class="row">
                                <?php Pjax::begin(['id' => 'contactos-data-grid']); ?>
                                 <?php $form = ActiveForm::begin(['id'=>'contacto-inscripcion-form-id']); ?>  
                                    <?=  $form->field($model, 'guardar')->hiddenInput(['value'=> 2])->label(false)  ?>
                                    <?php echo $this->render('//contacto/_form',
										array('model'=>$contacto
											,'listCargos'=>$listCargos
											,'listPais'=>$listPais
											,'listDepartamento'=>$listDepartamento
											,'listCiudad'=>$listCiudad)
										); ?>
                                <?php ActiveForm::end(); ?>
                                <?php Pjax::end(); ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="description">
                            <div class="row"> 
                                    <?= Html::button('CREAR PARTICIPANTE',['value'=>Url::toRoute(['persona/create','id_empresa'=>$model->id_empresa]),'rel'=>"tooltip",'title'=>" Crear Participante",'class' => 'btn btn-success','id'=>'modal-personas-inscripciones']) ?>
                                    <?php Pjax::begin(['id' => 'personas-data-grid']); ?>    
                                        <?= GridView::widget([
                                             'dataProvider' => $personas_data,
                                             'columns' => [
                                                 ['class' => 'yii\grid\SerialColumn'],
                                                 [
                                                        'label' => 'Indentificación',
                                                        'attribute' => 'idPersona.identificacion',
                                                    ],
                                                 [
                                                        'label' => 'Nombre',
                                                        'attribute' => 'idPersona.nombre',
                                                    ],
                                                 [
                                                        'label' => 'Apellido',
                                                        'attribute' => 'idPersona.apellido',
                                                ],
                                              //   ['class' => 'yii\grid\ActionColumn']
                                                    ['class' => 'yii\grid\ActionColumn','template'=>'{delete}' ]
                                                ],]); 
                                        ?>
                                    <?php Pjax::end(); ?>
                            </div>
                        </div>
                    </div> 
                    
                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Siguiente' style="visibility: hidden"/>
                            <input type='button' class='btn btn-fill btn-danger btn-wd' name='cancel' value='Cancelar' onclick="javascript:salir()" />
                            <input type='button' class='btn btn-empresa btn-fill btn-success btn-wd' name='next' value='Siguiente' onclick="javascript:verificarnit(this,1)" />
                            <input type='button' class='btn btn-contacto btn-fill btn-success btn-wd' name='next' value='Siguiente' onclick="javascript:verificarnit(this,2)" style="display: none;"/>
                            <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Guardar' />
                        </div>
                        <div class="pull-left">
                            <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Anterior' style="display: none;"/>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div> <!-- wizard container -->
        </div>
    </div> <!-- row -->
</div> <!--  big container -->   
  
</div>

