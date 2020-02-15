<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\TipoAsistentes;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InscripcionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'INSCRIPCIONES';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inscripciones-index">

       <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nueva Inscripciones', ['index-menu'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'inscripciones-personas-grid'])?>
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
                        if($model->id_empresa)
                        {
                            $url = Url::toRoute(['persona/create','id_empresa'=>$model->id_empresa]);
                            return Html::button('<i class="material-icons">people</i>',['value'=>$url,'class' => 'btn btn-success btn-round btn-just-icon','id'=>'modal-personas-inscripciones','rel'=>"tooltip",'title'=>" Nueva Persona",'data-title'=>"Empresa : ".$model->idEmpresa->nombre]);
                        }
                        return false;
                    }
                ],
                    'headerOptions' => ['style' => 'width:2%'],
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
                        $htmls=Html::a('<i class="material-icons" style="font-size: 15px">create</i>', ['empresa/update','id'=>$model->id_empresa], ['class' => '']).$salida;
                    }
                    return $htmls;
                },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
                'filterInputOptions' => ['placeholder' => 'Filtro Empresa','class' => 'form-control',],
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
                    $htmls=Html::a('<i class="material-icons" style="font-size: 15px">create</i>', ['persona/update','id'=>$model->id_persona], ['class' => '']).$salida;
                    
                    return $htmls;
                },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
                'filterInputOptions' => ['placeholder' => 'Filtro Participante','class' => 'form-control',],
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
                     $estado = 'Activo';
                    if($model->estado===1)
                    {
                        $estado = 'Activo';
                    }
                   // elseif($model->estado==2)
                   // {
                    //    $estado = 'Facturado';
                   // }
                    elseif($model->estado===0)
                    {
                        $estado = 'InActivo';
                    }
                    
                    return $estado;
                },
                        'headerOptions' => ['style' => 'text-align: center;'],
                        'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],
            [
               'header' => 'Estado Factura',
                'attribute' => 'estado_pago',
                'format' => 'raw',
                'value' => function($model, $key, $index, $widget) {     // render your custom button    
                        $htmls="";

                    $htmls= $model['id_factura'] ? 'FACTURADO' : 'NO FACTURADO' ;
                           // if($model['estadoInscripcion']<2 && $model['is_facturado'] == 0 )
                                //$htmls=Html::button('<i class="material-icons" style="font-size:15px;" onclick="javascript:openModal('.$model['id'].')">create</i>',['class' => 'buttonEdit','style'=>'margin:0px !important;','rel'=>"tooltip",'title'=>"Cambiar Estado"]).$salida;
                           // else
                             //   $htmls=$salida;
                        
                        return $htmls;
                    },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
            ],
            [
               'header' => 'Asistencia',
                'attribute' => 'is_presence',
                'format' => 'raw',
                'value' => function($model, $key, $index, $widget) {     // render your custom button    
                        $htmls="";
                       
                            $salida= $model['is_presence'] == 0 ? 'NO ASISTIO' : 'ASISTIO' ;
                           // if($model['estadoInscripcion']<2 && $model['is_facturado'] == 0 )
                                $htmls=Html::button('<i class="material-icons" style="font-size:15px;" onclick="javascript:openModalPresence('.$model['id'].')">create</i>',['class' => 'buttonEdit','style'=>'margin:0px !important;','rel'=>"tooltip",'title'=>"Cambiar Estado"]).$salida;
                           // else
                             //   $htmls=$salida;
                        
                        return $htmls;
                    },
                'headerOptions' => ['style' => 'width:40%;text-align: center;'],
            ],                  
              'observaciones',
            // 'modified_at',
            // 'deleted',

                ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:60px;'],'header'=>'Acciones','template' => '{estado} {note} {delete}',
                    'buttons' => [

                           'estado' => function ($url, $model) {
                                //if($model->estado>0)
                                //{
                                        $url = Url::toRoute(['inscripcion/cambiar-inscrito','idInscrito'=>$model->id]);
                                        //$url = Url::toRoute(['orden-de-compra/view','id'=>$model->idorden_de_compra]);
                                         return Html::a('<span class=" glyphicon glyphicon-refresh"></span>', null,
                                        [
                                            'id' => 'modal-modalht',
                                            'data' => [
                                                'title'=>'Datos del cambio',
                                                'pjax' => '0',
                                                'value' =>  $url,
                                            ],
                                            'title' => Yii::t('app', 'Cambiar Participante')
                                        ]);
                                //}
                            },
                            'note' => function ($url, $model) {
                                //if($model->estado>0)
                                //{
                                        $url = Url::toRoute(['inscripcion/notas','idInscrito'=>$model->id]);
                                        //$url = Url::toRoute(['orden-de-compra/view','id'=>$model->idorden_de_compra]);
                                         return Html::a('<span class="glyphicon glyphicon-edit"></span>', null,
                                        [
                                            'id' => 'modal-modalht',
                                            'data' => [
                                                'title'=>'Notas',
                                                'pjax' => '0',
                                                'value' =>  $url,
                                            ],
                                            'title' => Yii::t('app', 'Notas')
                                        ]);
                                //}
                            },
                        ]
                ], 
        ],
    ]); ?>
    <?php  Pjax::end() ?>
</div>
