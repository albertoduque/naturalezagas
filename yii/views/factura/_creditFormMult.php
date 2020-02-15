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
    <?php $form = ActiveForm::begin(['id'=>'creditmult-form-id']); ?>
    <div class="row">
        <div class="col-sm-12">
            <h4 class="info-text"> Ingrese Información básica de la Nota de Credito.</h4>
        </div>

        <div class="col-sm-12">

            <div class="col-sm-4">
                <div class="input-group">
                    <div class="form-group label-floating">
                        <?= $form->field($model, 'numero')->textInput(['maxlength' => 11,'onkeypress'=>"javascript:runScript(event)",'readOnly'=>true])->label('# Factura') ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <div class="form-group label-floating">
                        <?= $form->field($model, 'fecha')->textInput(['maxlength' => 11,'class'=>"form-control id_Desde"])->label('Fecha') ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group label-floating">
                    <?= $form->field($model, 'id_moneda')->dropDownList($listMoneda,['options'=>['1'=>['Selected'=>true]]])->label('Moneda') ?>
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
            <div class="col-sm-6">
                <div class="form-group label-floating">
                    <?= $form->field($model, 'telefonoContacto')->textarea(['rows' => '3'],['class'=>'form-control'])->label("Teléfono de Contacto") ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group label-floating">
                    <?= $form->field($model, 'facturas[]')->dropDownList($listContacto,['options'=>['1'=>['Selected'=>true]],'multiple'=>'multiple' ])->label('Facturas') ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group label-floating">
                     <?= Html::a('Cargar Facturas', null, ['id'=>'buttonLoadFacturas','class' => 'btn btn-success']) ?>
                </div>
            </div>

        </div>
		<?= $form->field($model, 'tipo_compra')->hiddenInput()->label(false) ?>
		 <?= $form->field($model, 'periodo_pago')->hiddenInput()->label(false) ?>
		 <?= $form->field($model, 'fecha_vencimiento')->hiddenInput()->label(false) ?>
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
        <?= Html::submitButton(isset($title) && $title !='' ?  'Crear Nota Crédito' : 'Crear Nota Crédito', ['class' => 'btn btn-success']) ?>
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
		});
    });",
    View::POS_READY,
    'my-button-handler'
);
?> 
