<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\DetalleRecibos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalle-recibos-form">
    <?php Pjax::begin(['id' => 'detalle-grid']) ?>
    <?= GridView::widget([
    'dataProvider' => $vpagos,
    'columns' => [
        'fecha_pago',
         ['label' => 'Valor',
                'attribute' => 'valor', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                   return  Yii::$app->formatter->asDecimal($model['valor'],0);
                }
            ],
    
            ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:60px;'],'header'=>'','template' => ' {delete}',
                'buttons' => [
                        'delete' => function ($url, $model) {
                                
                               return Html::a('<span class=" glyphicon glyphicon-trash "></span>', null, [
                                                'class' => 'deleteCrud',
                                                'id' => 'detalle-recibo-delete',
                                                'data' => [
                                                    'content' => 'Esta seguro que desea eliminar ?',
                                                    'ids'=>'detalle-recibo-delete',
                                                    'pjax' => '0',
                                                    'url' =>  Url::toRoute(['detalle-recibo/delete-ajax','id'=>$model->id,'accion'=>'1']),
                                                ],
                                            ]);
                               
                            },    
                    ]
            ],
    ],
    ]); ?>
    
    <?php Pjax::end();?>
    <h4 class="info-text" style=" font-weight: bold;text-align: center;"> INGRESE NUEVO PAGO</h4>
    <?php  $form = ActiveForm::begin(['id'=>'recibo-form-id']);?>
    <?= $form->field($model, 'fecha_pago')->textInput(['maxlength' => 11,'class'=>"form-control id_Desde"]) ?>
    <?= $form->field($model, 'valor')->textInput() ?>
    <?= $form->field($model, 'id_forma_pago')->dropDownList($listFormaPago,['options'=>['1'=>['Selected'=>true]]])->label('Forma de Pago') ?>
    <?= $form->field($model, 'tipo_pago')->dropDownList(['2'=>'PAGO PARCIAL','3'=>'PAGO TOTAL']); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
