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
    <?php $form = ActiveForm::begin(['id'=>'update-presence-form']); ?>    
        <div class="row">
            <div class="col-sm-4">
                <div class="col-sm-12">
                    <div class="form-group label-floating">
                            <?= $form->field($model, 'is_presence')->dropDownList(['1'=>'ASISTIO','0'=>'NO ASISTIO']);  ?>
                    </div>
                     <div class="form-group">
                    <?= Html::submitButton( 'Guardar', ['class' => 'btn btn-success']) ?>
                </div>
                </div>
               
            </div> 
        </div>
        
    <?php ActiveForm::end(); ?>  
</div>

