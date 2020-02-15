<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\TipoAsistentes;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InscripcionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'FACTURAR INSCRITOS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facturas-index">

     <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table-striped table-bordered table-condensed'],
        'options'=>['style' => 'white-space:nowrap;'],
        'columns' => [
             [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{myButton}' , 
                'buttons' => [
                    'myButton' => function($url, $model, $key) {     // render your custom button        
                            $url = ['factura/create','id_inscripcion'=>$model->id ? $model->id : 0];
                            return Html::a('<i class="material-icons">description</i>',$url,['class' => 'btn btn-success btn-round btn-just-icon','rel'=>"tooltip",'title'=>" Nueva Factura"]);
                    }
                ],
                    'headerOptions' => ['style' => 'width:2%;text-align: center;'],
            ],
            [
                'label' => 'Empresa',
                'attribute' => 'empresa_nombre',
                'format' => 'html',
                'value' => function($model, $key, $index, $widget) {
                    $htmls="";
                    if($model->idEmpresa['nombre'])
                    {
                        $salida=$model->idEmpresa['nombre'];
                        $htmls=$salida.Html::a('<i class="material-icons" style="font-size: 15px">create</i>', ['empresa/update','id'=>$model->id_empresa], ['class' => '']);
                    }
                    return $htmls;
                },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
            ],
            [
                'label' => 'Identificación',
                'attribute' => 'idPersona.identificacion',
                 'headerOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'label' => 'Participante',
                'attribute' => 'persona_nombre',
                'format' => 'html',
                'value' => function($model, $key, $index, $widget) {
                    $salida=$model->idPersona['apellido']." ".$model->idPersona['nombre'];
                    $htmls=$salida.Html::a('<i class="material-icons" style="font-size: 15px">create</i>', ['persona/update','id'=>$model->id_persona], ['class' => '']);
                    
                    return $htmls;
                },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
            ],
            [
                'label' => 'Asistente',
                'attribute' => 'asistente',
                'filter' => Html::activeDropDownList($searchModel,'asistente',TipoAsistentes::toList(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $index, $dataColumn) {
                    $roleDropdown = TipoAsistentes::toList();
                    return $roleDropdown[$model->idPersona->idTipoAsistente->id];
                },
                 'headerOptions' => ['style' => 'text-align: center;'],   
            ],
            [
                'label' => 'Estado',
                'attribute' => 'estado',
                 'value' => function ($model) {
                    
                    return $model->estado<2? 'NO FACTURADO' : 'FACTURADO' ;
                },
               'headerOptions' => ['style' => 'text-align: center;'],         
            ],
            // 'created_at',
            // 'modified_at',
            // 'deleted',

                ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:60px;'],'header'=>'','template' => ' {deletes}',
                    'buttons' => [

                            'update' => function ($url, $model) {
                                        $url = Url::toRoute(['persona/update','id'=>$model->id_persona]);
                                        return  $url ? Html::a('<span class="glyphicon glyphicon-pencil"></span>',$url, ['title' => Yii::t('app', 'Actualizar'),]) : '';
                            },
                            'view' => function ($url, $model) {
                                        $url = Url::toRoute(['persona/view','id'=>$model->id_persona]);
                                        //$url = Url::toRoute(['orden-de-compra/view','id'=>$model->idorden_de_compra]);
                                        return Html::a('<span class="glyphicon glyphicon-eye-open" ></span>', $url, [
                                        'title' => Yii::t('app', 'Ver'),
                                ]);
                            },
                        ]
                ], 
        ],
    ]); ?>
</div>
