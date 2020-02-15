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
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model,'id_empresa')->hiddenInput()->label(false); ?>
            <input type="hidden" id="inscripciones-visibles" class="form-control" name="Inscripciones[visibles]" value="1">
        <?php ActiveForm::end(); ?>
            <?= Html::button('CREAR PARTICIPANTE',['value'=>Url::toRoute(['persona/create','id_empresa'=>$model->id_empresa]),'rel'=>"tooltip",'title'=>" Crear Participante",'class' => 'btn btn-success','id'=>'modal-personas-inscripciones']) ?>
            <?php Pjax::begin(['id' => 'personas-data-grid']); ?>    
                <?= GridView::widget([
                     'dataProvider' => $personas_data,
                     'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Identificación',
                            'attribute' => 'idPersona.identificacion',
                            'headerOptions' => ['style' => 'text-align: center;'],
                        ],
                        [
                            'label' => 'Nombre',
                            'attribute' => 'idPersona.nombre',
                            'headerOptions' => ['style' => 'text-align: center;'],
                        ],
                        [
                            'label' => 'Apellido',
                            'attribute' => 'idPersona.apellido',
                            'headerOptions' => ['style' => 'text-align: center;'],
                        ],
                            ['class' => 'yii\grid\ActionColumn','template'=>'{delete}' ]
                        ],]); 
                ?>
            <?php Pjax::end(); ?>
    </div>
</div>