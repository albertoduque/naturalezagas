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
    <?php $form = ActiveForm::begin(['id'=>'update-status-form']); ?>    
        <div class="row">
            <div class="col-sm-4">
                <div class="col-sm-12">
                    <div class="form-group label-floating">
                            <?= $form->field($model, 'is_facturado')->dropDownList(['1'=>'FACTURADO','0'=>'NO FACTURADO']);  ?>
                    </div>
                     <div class="form-group">
                    <?= Html::submitButton( 'Guardar', ['class' => 'btn btn-success']) ?>
                </div>
                </div>
               
            </div> 
        </div>
        
    <?php ActiveForm::end(); ?>  
</div>

