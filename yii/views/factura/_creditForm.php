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
    <?php $form = ActiveForm::begin(['id'=>'nc-form-id']); ?>    
        <div class="row">
            <div class="col-sm-12">
                <h4 class="info-text"> Ingrese Información básica de la Nota de Crédito.</h4>
            </div>
            <div class="col-sm-12">
                <h4 class="info-text"> <?= "Factura :".$title?></h4>
            </div>
            <div class="col-sm-12">
            
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating">
							 <?= $form->field($model, 'numero')->textInput(['maxlength' => 11,'onkeypress'=>"javascript:runScript(event)",'readOnly'=>true])->label('# Nota Crédito') ?>
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
				<?php if($model->id_moneda <> 1) { ?>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="form-group label-floating trm-hidden">
							 <?= $form->field($model, 'trm')->textInput(['maxlength' => 11,'readOnly'=>true]) ?>
						</div>
					</div>
				</div>
				<?php } ?>
            </div>
            <div class="col-sm-12">
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
			</div>
            <div class="col-sm-12">
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
			</div>
            <div class="col-sm-12">
				<div class="col-sm-6">
					<div class="form-group label-floating">
						 <?= $form->field($model, 'id_tipo_nota')->dropDownList(
								$listTNCredito
								,['prompt'=>'N/A','autofocus' => 'autofocus', 'tabindex' => '1']
							)->label('Tipo Nota Credito')
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

            <div class="col-sm-12">
                <div class="row"> 
                    <div class="">
                        <table id="tableNC" class="table-striped table-bordered table-condensed order-list">
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
                            <tbody>
							<?=$form->field($model, "id_contacto")->hiddenInput(['value'=> $model['id_contacto']])->label(false);?>	
							<?php foreach($detalle_factura as $i=>$df) {?>
								<tr data-ids="[<?=$i?>]">
									<td>
                                        <?php
                                            $nombre = $df['id_inscripcion'] ? "-".$df->idInscripcion->idPersona->nombre." ".$df->idInscripcion->idPersona->apellido : '' ;
                                            echo $df->idProducto->nombre.$nombre ?>
                                        <?= $form->field($df, "[$i]id")->hiddenInput(['value'=> $df['id']])->label(false);?>
								        <?= $form->field($df, "[$i]id_producto")->hiddenInput(['value'=> $df['id_producto']])->label(false);?>
								        <?= $form->field($df, "[$i]producto")->hiddenInput(['value'=> $df->idProducto->nombre])->label(false);?>
									    <?= $form->field($df, "[$i]id_inscripcion")->hiddenInput(['value'=> $df['id_inscripcion']])->label(false);?></td>
									<td><?= $form->field($df, "[$i]observacion")->textInput(['class'=>'form-control'])->label(false) ?></td>
									<td><?= $form->field($df, "[$i]cantidad")->textInput(['class'=>'form-control'])->label(false) ?></td>
									<td><?= $form->field($df, "[$i]valor")->textInput(['class'=>'form-control','onkeypress'=>'javascript:subtotalNC(this)','value'=> Yii::$app->formatter->asDecimal($df['valor'],0)])->label(false) ?></td>
									<td><?= $form->field($df, "[$i]iva")->textInput(['class'=>'form-control','onkeypress'=>'javascript:ivaNC(this)'])->label(false) ?></td>
									<td><?= $form->field($df, "[$i]valorTotal")->textInput(['class'=>'form-control','value'=> Yii::$app->formatter->asDecimal($df['valorTotal'],0)])->label(false) ?></td>
									<td><button type="button" class="btn btn-danger btn-round btn-just-icon ibtnDelNC" value="delete" title="" rel="tooltip" data-title="Empresa : mafirma FINAL" data-original-title=" Borrar Item"><i class="material-icons">delete</i><div class="ripple-container"></div></button></td>
								</tr>
							<?php  $i++;} ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1" style="text-align: left;">
                                    </td>
                                    <td colspan="8"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" rowspan="4">
                                    </td>
                                </tr>
                                <tr>
                                    <td >Subtotal</td>
                                    <td colspan="4"><?= $form->field($model, 'subtotal')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;','value'=> Yii::$app->formatter->asDecimal($model['subtotal'],0)])->label(false) ?></td>
                                </tr>
                                <tr>
                                    <td >Iva</td>
                                    <td colspan="4"><?= $form->field($model, 'iva')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;','value'=> Yii::$app->formatter->asDecimal($model['iva'],0)])->label(false) ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td colspan="4"><?= $form->field($model, 'total')->textInput(['class'=>'form-control','readonly' => true,'style'=>'text-align: right;border: none;','value'=> Yii::$app->formatter->asDecimal($model['total'],0)])->label(false) ?></td>
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
            <?= Html::submitButton($model->isNewRecord ? 'Crear Nota Crédito' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'nc-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>  
</div>
<?php
$this->registerJs(
    "$('document').ready(function(){ 
		$('#tableNC tbody tr').each(function (index) {
			$('#detallefactura-'+index+'-valortotal').numeric(false);	
			$('#detallefactura-'+index+'-valortotal').maskMoney({thousands:',', precision:'0'});
			$('#detallefactura-'+index+'-valor').numeric(false);	
			$('#detallefactura-'+index+'-valor').maskMoney({thousands:',', precision:'0'});
			$('#detallefactura-'+index+'-valor').css('text-align', 'right');
			$('#detallefactura-'+index+'-valortotal').css('text-align', 'right');
			$('#detallefactura-'+index+'-cantidad').css('text-align', 'right');
			$('#detallefactura-'+index+'-iva').css('text-align', 'right');
		});
		$('#facturas-subtotal').numeric(false);	
		$('#facturas-subtotal').maskMoney({thousands:',', precision:'0'});
		$( '#nc-button' ).click(function() {
		    if(!$( '#facturas-id_tipo_nota' ).val())
                $( '#facturas-id_tipo_nota' ).focus();
        });
    });",
    View::POS_READY,
    'my-button-handler'
);
?> 
