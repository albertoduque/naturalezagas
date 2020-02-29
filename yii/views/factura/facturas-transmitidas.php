<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FacturasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Facturas por transmitir';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facturas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Transmitir Facturas', ['transmitir-factura'], ['class' => 'btn btn-success']) 
        ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => 'Cliente',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:14%;text-align:center'],
                //'filter' => Html::activeDropDownList($searchModel,'estado_pago',  \app\models\EstadosFactura::tolistFacturas(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $key, $index, $widget) {     // render your custom button
                    $result = '';
                    if($model->tipo_factura=='FA')
                        $result = $model->idEmpresa ? $model->idEmpresa->nombre  : $model->idPersona->nombre.' '.$model->idPersona->apellido;
                    return $result;
                },
                'headerOptions' => ['style' => 'width:14%;text-align: center;'],
            ],
            'tipo_factura',
            'numero',
            [   
                'label' => 'Total',
                'attribute' => 'total', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                    return  $model['total'] ? Yii::$app->formatter->asDecimal($model['total'],0) : 0;
                }
            ],
            [
              'label' => 'Respuesta',
              'attribute' => 'respuesta', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
              'format' => 'raw',
              'headerOptions' => ['style' => 'width:4%;text-align:center'],
              'value'=>function($model){
                $decode = json_decode($model['respuesta'], true);
                return $decode['descripcionProceso'].'<br>'.json_encode($decode['listaMensajesProceso']);
              }
            ],
            [
                'header' => 'Estado Factura',
                'attribute' => 'cufe',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:4%;text-align:center'],
                //'filter' => Html::activeDropDownList($searchModel,'estado_pago',  \app\models\EstadosFactura::tolistFacturas(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $key, $index, $widget) {     // render your custom button
                    return $model['cufe'] ? 'TRANSMITIDA' : 'NO TRANSMITIDA' ;
                },
                'headerOptions' => ['style' => 'width:4%;text-align: center;'],
            ],

            // 'id_estado_factura',
            // 'observaciones:ntext',
            // 'id_moneda',
            // 'descuento',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:70px;'],'header'=>'Acciones','template' => '{update}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        if(!$model['cufe'])
                        {
                            return Html::a('<span class=" glyphicon glyphicon-trash "></span>', null, [
                                'class' => 'deleteCrud',
                                'id' => 'modaldelete',
                                'data' => [
                                    'content' => 'Esta seguro que desea eliminar la factura?',
                                    'ids'=>'contacto-delete',
                                    'pjax' => '0',
                                    'url' =>  Url::toRoute(['factura/delete-ajax','id'=>$model['id'],'accion'=>'0']),
                                ],
                                'title' => Yii::t('app', 'Eliminar Factura')
                            ]);
                        }
                    },
                    'update' =>  function ($url, $model) {
                        $url = Url::toRoute(['factura/retransmitir-factura','id'=>$model['id']]);
                        return Html::a('<span class=" glyphicon glyphicon-pencil "></span>', [$url], ['class' => 'profile-link','title' => Yii::t('app', 'Actualizar')]);
                    },
                ],]
        ],
    ]); ?>
</div>
