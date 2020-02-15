<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\DescripcionProductos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="descripcion-productos-form">

    <?php  $form = ActiveForm::begin(['id'=>'descripcion-form-id']); ?>

    <?= $form->field($model, 'nombre')->textarea(['rows' => 6]) ?>

    <div class="col-sm-12">
        <div class="input-group">
            <div class="form-group label-floating">
                <div class="form-group2">
                    <?php Pjax::begin(['id' => 'sector-dropDownList']);   ?>
                    <?= $form->field($model, 'producto_id')->dropdownList($listProductos)->label("Producto"); ?>
                    <?php Pjax::end(); ?>
                </div>
                <?php // Html::button('<i class="material-icons">add</i>',['value'=>Url::toRoute(['/sector-empresa/create']),'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-modalButton']) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
