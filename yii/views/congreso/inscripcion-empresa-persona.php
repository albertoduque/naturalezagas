<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use  yii\bootstrap\Modal;
use kartik\switchinput\SwitchInput;
use yii\grid\GridView;

?>

<div class="empresas-form">

       <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= "Inscribir Participante Empresa: ".strtoupper($model->idEmpresa->nombre) ?></h4>
    <div class="row"> 
            <?= Html::button('CREAR PARTICIPANTE',['value'=>Url::toRoute(['congreso/congreso-inscrito','id_empresa'=>$model->id_empresa]),'rel'=>"tooltip",'title'=>" Crear Participante",'class' => 'btn btn-success','id'=>'modal-personas-inscripciones']) ?>
            <?php Pjax::begin(['id' => 'personas-data-grid']); ?>
            <?php Pjax::end(); ?>
    </div>
</div>