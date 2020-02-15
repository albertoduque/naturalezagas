<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $model app\models\Facturas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="facturas-form">
    <?php $form = ActiveForm::begin(['id'=>'debit-form-id']); ?>
        <div class="row">
            <div class="col-sm-12">
                <h4 class="info-text"> Ingrese Información básica de la Factura.</h4>
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
						 <?= $form->field($model, 'id_moneda')->dropDownList($listMoneda,['disabled' =>true])->label('Moneda') ?>
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
						<?= $form->field($model, 'clientes')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Cliente") ?>
						<?= $form->field($model, 'id_empresa')->hiddenInput()->label(false) ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group label-floating">
						<?= $form->field($model, 'identificacion')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Identificación") ?>
					</div>
				</div>
				 <div class="col-sm-6">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'direccion')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Dirección de Facturación") ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'telefonoContacto')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Teléfono de Contacto") ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'id_tipo_nota')->dropDownList(
								$listTNDebito
								,['prompt'=>'N/A']
							)->label('Tipo Nota Debito')
						?>
					</div>
				</div>
				
            </div>
			
			<?= $form->field($model, 'tipo_compra')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'periodo_pago')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'fecha_vencimiento')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'id_impuesto')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'id_medio_pago')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'orden_compra')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'fechaemisionordencompra')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'numeroaceptacioninterno')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'facturaNumero')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'id_serie')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'trm')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'id_moneda')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'fecha_factura')->hiddenInput()->label(false) ?>
			<?= $form->field($model, 'tipoDocumento')->hiddenInput()->label(false) ?>

            </div>    
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
                                        <?=$form->field($model, "id_contacto")->hiddenInput(['value'=> $model['id_contacto']])->label(false);?>
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
            <?= $form->field($model, 'is_patrocinios')->hiddenInput()->label(false); ?>
            <?= $form->field($model, "facturaNC")->hiddenInput(['value'=> $model->facturaNC])->label(false) ?>
            <?= Html::a('Cancelar', ['/factura/facturados'], ['class'=>'btn btn-danger']) ?>
            <?= Html::submitButton(isset($title) && $title !='' ?  'Crear Nota Debito' : 'Crear Factura', ['class' => 'btn btn-success','id'=>'db-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>  
</div>
<?php
$this->registerJs(
    "$('document').ready(function(){ 
		$('#facturas-trm').number( true, 2 );
		$( '#db-button' ).click(function() {
		    if(!$( '#facturas-id_tipo_nota' ).val())
                $( '#facturas-id_tipo_nota' ).focus();
        });
    });",
    View::POS_READY,
    'handler-trm'
);
?> 