<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\models\Facturas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="facturas-form">
    <?php $form = ActiveForm::begin(['id'=>'factura-form-id']); ?>    
        <div class="row">
            <div class="col-sm-12">
                <h4 class="info-text" style="margin: 10px;"> Ingrese Información básica de la Factura<?php if($model->serie==2){ ?> de Contigencia <?php } ?>.</h4>
                <?php if($model->serie==2){ ?>
                <div class="alert alert-danger" role="alert">Tenga en cuenta que la fecha de la factura de contingencia debe ser la fecha que esta en la factura fisica</div>
            <?php } ?>
            </div>
            <?php if(isset($title) && $title !=''){ ?>
                <div class="col-sm-12">
                    <h4 class="info-text"> <?= "Factura :".$title?></h4>
                </div>
            <?php } ?>
            <div class="col-sm-12">
            
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating">
							 <?= $form->field($model, 'numero')->textInput(['maxlength' => 11,'onkeypress'=>"javascript:runScript(event)",'readOnly'=>true])->label('# Factura') ?>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating">
							 <?= $form->field($model, 'fecha')->textInput(['maxlength' => 11,'class'=>"form-control id_Desde"])->label('Fecha') ?>
						</div>
					</div>
				</div>  
				<div class="col-sm-3">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'id_moneda')->dropDownList($listMoneda,['options'=>['1'=>['Selected'=>true]]])->label('Moneda') ?>
					</div>
				</div>
				 <div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating trm-hidden">
							 <?= $form->field($model, 'trm')->textInput(['maxlength' => 11]) ?>
						</div>
					</div>
				</div>				
						
				<div class="col-sm-6">
					<div class="form-group label-floating">
						<?php if($model->is_patrocinios){ ?>
							<?=  $form  ->field($model, 'clientes')->dropDownList($listClientes,['prompt' => 'Empresas'])->label('Empresas') ?>
						<?php }else{ ?>
							<?=  $form->field($model, $model->id_empresa ? 'id_empresa' : 'id_persona')->dropDownList($listClientes,['disabled' => 'disabled'])->label('Empresas') ?>
							<?= $form->field($model, $model->id_empresa ? 'id_empresa' : 'id_persona')->hiddenInput()->label('');?>
						<?php }?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group label-floating">
						<?= $form->field($model, 'id_contacto')->dropDownList($listContacto,['options'=>['1'=>['Selected'=>true]]])->label('Contacto Facturación') ?>
					</div>
				</div>
				 <div class="col-sm-6">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'direccion')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Dirección de Facturación") ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'telefonoContacto')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Teléfono de Contacto") ?>
					</div>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-3">
					<div class="form-group label-floating">
						<?=  $form->field($model, 'tipo_compra')->dropDownList([1=>'CONTADO',2=>'CRÉDITO']); ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group label-floating">
					   <? $form->field($model, 'periodo_pago')->textInput(['maxlength' => 6,'disabled'=>true])->label('Periodo Pago') ?>
					   <?=  $form->field($model, 'periodo_pago')->dropDownList([''=>'',30=>30, 60=>60,90=>90],['disabled' => true]); ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating">
							<?= $form->field($model, 'fecha_vencimiento')->textInput(['maxlength' => 11,'class'=>"form-control id_Desde",'disabled'=>true])->label('Fecha Vencimiento') ?>
						</div>
					</div>
				</div>
			</div>    
			 <div class="col-sm-12">
				<!--
				<div class="col-sm-3">
					<div class="form-group label-floating">
					<? $form->field($model, 'id_impuesto')->dropDownList(
							$listImpuestos
							,['options'=>[''=>['Selected'=>true]]]
						)->label('Impuesto Retención')
					?>
					</div>
				</div>
				-->
				<div class="col-sm-3">
					<div class="form-group label-floating">
						<?= $form->field($model, 'id_medio_pago')->dropDownList(
								$listMedioPago
								,['options'=>[''=>['Selected'=>true]]]
							)->label('Medio Pago')
						?>
					</div>
				</div>
			</div>
			<!--
			<div class="col-sm-12">
				<div class="col-sm-3">
					<div class="form-group label-floating">
						<? $form->field($model, 'orden_compra')->textInput(['maxlength' => 6])->label('Orden de Compra') ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating">
							<? $form->field($model, 'fechaemisionordencompra')->textInput(['maxlength' => 11,'class'=>"form-control id_Desde"])->label('Fecha Orden de Compra') ?>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group label-floating">
						<? $form->field($model, 'numeroaceptacioninterno')->textInput(['maxlength' => 6])->label('Número Aceptación Interno') ?>
					</div>
				</div>
			</div>
			-->
		<div class="col-sm-12">
			<div class="col-sm-12">
                <div class="row"> 
                    <div class="">
                        <table id="myTable" class="table-striped table-bordered table-condensed order-list">
                            <thead>
                                <tr>
                                    <th class="col-sm-2" style="text-align: center;">Producto</th>
                                    <th class="col-sm-3" style="text-align: center;">Descripción</th>
                                    <th class="col-sm-1" style="text-align: center;">Cant</th>
                                    <th class="col-sm-2" style="text-align: center;">Valor/Unt</th>
                                    <th class="col-sm-1" style="text-align: center;">% IVA</th>
                                    <th class="col-sm-2" style="text-align: center;">Total</th>
                                    <th class="col-sm-1"></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="1" style="text-align: left;">
                                        <input type="button" class="btn btn-lg btn-block " onclick="javascript:loadSelect(<?=$detalle_factura->id_inscripcion? $detalle_factura->id_inscripcion : 0?>,<?=$detalle_factura->id_inscripcion? 1 : 0 ?>)" id="addrow" value="+ Ingreso Nuevo Producto" />
                                    </td>
                                    <td colspan="6"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" rowspan="4">
                                    </td>
                                </tr>
                                <tr>
                                    <td >Subtotal</td>
                                    <td colspan="4"><?= $form->field($model, 'subtotal')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;'])->label(false) ?></td>
                                </tr>
                                <tr>
                                    <td >Iva</td>
                                    <td colspan="4"><?= $form->field($model, 'iva')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;'])->label(false) ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td colspan="4"><?= $form->field($model, 'total')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;'])->label(false) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>            
                </div>
            </div>
        </div>
        <div class="form-group pull-right">
            	<?= $form->field($model, 'tipoidentificacion')->hiddenInput()->label('') ?>				
				<?= $form->field($model, 'departamento')->hiddenInput()->label('') ?>				
				<?= $form->field($model, 'ciudad')->hiddenInput()->label('') ?>	
				<?= $form->field($model, 'tipoDocumento')->hiddenInput()->label(false) ?>
				<?= $form->field($model, 'fecha_factura')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'is_patrocinios')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'serie')->hiddenInput()->label(false); ?>
            <?= $form->field($model, "facturaNC")->hiddenInput(['value'=> $model->facturaNC])->label(false) ?>
            <?= Html::a('Cancelar', ['/factura/facturados'], ['class'=>'btn btn-danger']) ?>
            <?= Html::submitButton(isset($title) && $title !='' ?  'Crear Nota Debito' : 'Crear Factura', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>  
</div>