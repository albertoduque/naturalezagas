<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InscripcionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'GESTIÓN DE FACTURACIÓN';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facturas-index">

    <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?php echo $this->title ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('FACTURAR PRODUCTOS',null, ['class' => 'btn btn-success','onclick'=>'javascript:setUrlInscription()']) ?>
        <?= Html::a('NC MULTIPLE', ['factura/create-note-mult'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('TRANSMISIÓN DE FACTURAS', ['factura/transmision'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(['id' => 'recibos-grid',]); ?>
        
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-bordered table-hover toggle-circle'],
        'options'=>['style' => 'white-space:nowrap;'],//overflow-y: hidden;overflow: auto;table-layout:fixed;
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{myButton}{noShow}' , 
                'buttons' => [
                    'myButton' => function($url, $model, $key) {  
                            //var_dump($dataProvider);die;
                            // render your custom button        
                            $url = Url::to(['factura/create','id_inscripcion'=>$model['id_inscripcion'] ? $model['id_inscripcion'] : 0],true);
                            return $model['id_inscripcion'] && $model['estadoInscripcion'] == 1 && $model['tipo_factura'] != "NC" && $model['is_presence'] == 1 && Yii::$app->user->can('facturacion') ? Html::button('<i class="material-icons">description</i>',['class' => 'btn btn-success btn-round btn-just-icon','onclick'=>'javascript:setUrlInscription('.$model['id_inscripcion'].')','rel'=>"tooltip",'title'=>" Nueva Factura"]) : '';
                    },
                    'noShow' => function($url, $model, $key) {  
                            //var_dump($dataProvider);die;
                            // render your custom button        
                            $url = Url::to(['factura/create','id_inscripcion'=>$model['id_inscripcion'] ? $model['id_inscripcion'] : 0],true);
                            return $model['id_inscripcion'] && $model['estadoInscripcion'] == 1 && $model['tipo_factura'] != "NC" && Yii::$app->user->can('facturacion') ? Html::button('<i class="material-icons">delete_forever</i>',['class' => 'btn btn-danger btn-round btn-just-icon','onclick'=>'javascript:setNoShow('.$model['id_inscripcion'].')','rel'=>"tooltip",'title'=>" No Asistio"]) : '';
                    }
                ],
                    'headerOptions' => ['style' => 'width:2%;text-align: center;'],
            ],
            [
                'label' => 'Empresa',
                'attribute' => 'empresa_nombre',
                'format' => 'raw',
                'value' => function ($model) {
                    return yii\bootstrap\Html::a(yii\helpers\BaseStringHelper::truncate($model['empresa'],40 ),null,['rel'=>"tooltip",'title'=>$model['empresa']]);
                },          
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control'],

            ],
             [
                'label' => 'Participante',
                'attribute' => 'persona_nombre',
                'format' => 'raw',
                'value' => function ($model) {
                    return yii\bootstrap\Html::a(yii\helpers\BaseStringHelper::truncate($model['persona'].' '.$model['apellido'],40 ),null,['rel'=>"tooltip",'title'=>$model['persona'].' '.$model['apellido']]);
                },        
                 'visible'=> Yii::$app->session->get('event_id') <> 109,       
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'contentOptions' => ['style' => 'max-width:450px;'], 
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],
            [
                'label' => 'Producto',
                'attribute' => 'producto',
                'format' => 'raw',
                'value' => function ($model) {
                    return yii\bootstrap\Html::a(yii\helpers\BaseStringHelper::truncate($model['producto'],30 ),null,['rel'=>"tooltip",'title'=>$model['producto'],'style' => 'max-width:150px; ']);
                },
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'contentOptions' => ['style' => 'width:10; '],
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],
            [
                'header' => 'Estado Factura',
                'attribute' => 'estado_pago',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:4%;text-align:center'],
                'filter' => Html::activeDropDownList($searchModel,'estado_pago',  \app\models\EstadosFactura::tolistFacturas(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $key, $index, $widget) {     // render your custom button
                        $htmls="";
                        //if($model['estadoInscripcion'])
                        //{
                            //$salida=$model['estadoInscripcion']<2 && $model['is_facturado'] == 0 ? 'NO FACTURADO' : 'FACTURADO' ;
                            $salida = !$model['idFactura'] ? 'NO FACTURADO' : 'FACTURADO' ;
                           // if($model['estadoInscripcion']<2 && $model['is_facturado'] == 0 )
                           /* if(!$model['idFactura'])
                                $htmls=Html::button('<i class="material-icons" style="font-size:15px;" onclick="javascript:openModal('.$model['id_inscripcion'].')">create</i>',['class' => 'buttonEdit','style'=>'margin:0px !important;','rel'=>"tooltip",'title'=>"Cambiar Estado"]).$salida;
                            else*/
                                $htmls=$salida;
                        //}
                        return $htmls;
                    },
                'headerOptions' => ['style' => 'width:4%;text-align: center;'],          
            ],
            [
                'header' => 'Tipo Doc',
                'attribute' => 'tipo_factura',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:4%;text-align:center'],
                'filter' => Html::activeDropDownList($searchModel,'tipo_factura',  \app\models\Facturas::toListTipoFacturas(),['class'=>'form-control','prompt' => 'Todos']),
                'headerOptions' => ['style' => 'width:4%;text-align: center;'],
            ],
            [
                'header' => 'Estado Pago',
                'attribute' => 'estado_factura',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:4%;text-align:center'],
                'filter' => Html::activeDropDownList($searchModel,'estado_factura',  \app\models\EstadosFactura::toList(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $key, $index, $widget) {
                        $class = "label ";
                        
                            if($model['id_estado_factura']==1)
                            {
                                $class .= "label-warning";
                            }
                            else if($model['id_estado_factura']==2)
                            {
                                $class .= "label-info";
                            }
                            else if($model['id_estado_factura']==3)
                            {
                                $class .= "label-success";
                            }
                            else if($model['id_estado_factura']==4)
                            {
                                $class .= "label-danger";
                            }
                        
                    $roleDropdown = \app\models\EstadosFactura::toList();
                    $salida =  $model['id_estado_factura'] ? "<span class='$class'>".$roleDropdown[$model['id_estado_factura']]."</span>" : '';
                    
                    /*if(!$model['idFactura'])
                    {
                        //return '<button type="button" class="btn btn-success btn-round btn-just-icon" title="" rel="tooltip" style="margin:0px !important;" data-original-title="Cambiar Estado" aria-describedby="tooltip975843"><i class="material-icons" style="font-size:15px;" onclick="javascript:openModal(43)">createm</i><div class="ripple-container"></div></button>';
                        return $htmls= $salida ? Html::button('<i class="material-icons" style="font-size:15px;" onclick="javascript:openModalEstado('.$model['id_inscripcion'].')">create</i>',['class' => 'buttonEdit','style'=>'margin:0px !important;','rel'=>"tooltip",'title'=>"Cambiar Estado"]).$salida : '';
                       // return  $htmls='<span onclick="javascript:openModalEstado()"><i class="material-icons" style="font-size:15px;">create</i></span>';                   
                    }*/
                    return $salida;
                    //return $roleDropdown[$model['id_estado_factura']];
                },
            ],
            [   'label' => '# Doc.',
                'format' => 'raw',
                'attribute' => 'numero','headerOptions' => ['style' => 'text-align: center; word-wrap: break-word;'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                    $url = Url::toRoute(['factura/generar-pdf','id'=>$model['idFactura']]);
                     return  $model['numero'] > 0 ? Html::a('', $url, ['class' => 'fa fa-file-pdf-o'])." ".$model['serie'].$model['numero'] : '';
                },
                 'filterOptions' => [ 'title' => 'Debe ingresar solo el número del documento']
            ],
            [  
                'label' => 'Doc NC/ND',
                'format' => 'raw',
                'attribute' => 'numero_doc',
                'headerOptions' => ['style' => 'text-align: center; word-wrap: break-word;'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                    $url = Url::toRoute(['factura/generar-pdf','id'=>$model['id_nc']]);
                    return  $model['relacion_nc'] > 0 ? Html::a('', $url, ['class' => 'fa fa-file-pdf-o'])." ".$model['serie_nc'].$model['relacion_nc'] : '';
                }],
            [
                'header' => 'Fecha <br> Factura',
                'label' => 'Fecha <br> Factura',
                'attribute' => 'fecha',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'contentOptions' => ['style' => 'width:10; '],
                'format' =>  ['date', 'php:d/m/Y'],
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],             
            [   'label' => 'Subtotal',
                'attribute' => 'subtotal', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                   return  $model['subtotal'] ? Yii::$app->formatter->asDecimal($model['subtotal'],0) : 0;
            }],
            ['label' => 'Iva',
                'attribute' => 'iva', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                   return  $model['iva'] ? Yii::$app->formatter->asDecimal($model['iva'],0) : 0;
                }
            ],
            ['label' => 'Total',
            'attribute' => 'total', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
            'value'=>function($model){
                   return  $model['total'] ? Yii::$app->formatter->asDecimal($model['total'],0) : 0;
                }
            ],          
            [
                'label' => 'Días Mora',
                'attribute' => 'diasTrans',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['diasTrans']." Dias";
                },
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'contentOptions' => ['style' => 'width:10; '],
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],         
            ['label' => 'Pagos',
            'attribute' => 'pagos', 'headerOptions' => ['style' => 'text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
            'value'=>function($model){
                   return  Yii::$app->formatter->asDecimal($model['pagos'] ? $model['pagos'] : 0,0);
                }
            ],  
          
                    
            [
                'header' => 'Fecha Ult <br> Pago',
                'label' => 'Fecha Ult <br> Pago',
                'attribute' => 'fechaPago',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'contentOptions' => ['style' => 'width:10; '],
                'value'=>function($model){
                   return  $model['fecha_pago'] ? $model['fecha_pago']: '';
                }  , 
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],        
                    
             ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:70px;'],'header'=>'Acciones','template' => '{estado} {print} {credito} {debito}',
                    'buttons' => [
                            'estado' => function ($url, $model) {
                                if($model['id_estado_factura']<>4 && $model['tipo_factura'] == "FA" && Yii::$app->user->can('facturacion'))
                                {
                                        $url = Url::toRoute(['detalle-recibo/create','id'=>$model['idFactura'],'idInscripcion'=>$model['id_inscripcion']]);
                                        //$url = Url::toRoute(['orden-de-compra/view','id'=>$model->idorden_de_compra]);
                                         return Html::a('<span class=" glyphicon glyphicon-credit-card"></span>', null,
                                        [
                                            'id' => 'modal-modalht',
                                            'data' => [
                                                'title'=>'Datos del Pago',
                                                'pjax' => '0',
                                                'value' =>  $url,
                                            ],
                                            'title' => Yii::t('app', 'Cargar Pago')
                                        ]);
                                }
                            },
                            'print' =>  function ($url, $model) {
                                $url = Url::toRoute(['factura/generar-pdf','id'=>$model['idFactura']]);
                                return $model['idFactura'] ? Html::a('<span class="glyphicon glyphicon-print" ></span>', FALSE, [
                                'id' => 'modal-modalht',
                                'title' => Yii::t('app', 'Imprimir'),
                                'data' => [
                                        'title'=>'Factura Generada',
                                        'pjax' => '0',
                                        'value' =>  $url,
                                    ],
                                ]) : '';
                            },                
                            'delete' => function ($url, $model) {
                                if($model['id_estado_factura']<>4)
                                {
                                    return Html::a('<span class=" glyphicon glyphicon-trash "></span>', null, [
                                                'class' => 'deleteCrud',
                                                'id' => 'modaldelete',
                                                'data' => [
                                                    'content' => 'Esta seguro que desea eliminar la factura?',
                                                    'ids'=>'contacto-delete',
                                                    'pjax' => '0',
                                                    'url' =>  Url::toRoute(['factura/delete-ajax','id'=>$model['idFactura'],'accion'=>'0']),
                                                ],
                                                'title' => Yii::t('app', 'Eliminar Factura')
                                            ]);
                                }
                            },
                            'credito' => function ($url, $model) {
                                    return $model['tipo_factura'] == "FA" ? Html::a('<span class="glyphicon glyphicon-list-alt"></span>', Url::toRoute(['factura/create-note','id'=>$model['idFactura']]), [
                                                'class' => 'deleteCrud2',
                                                'onclick' =>'myFunction()',
                                                'id' => 'modaldelete52',
                                                'data' => [
                                                    'content' => 'Esta seguro que desea crear Nota de Credito ?',
                                                    'ids'=>'contacto-anular',
                                                    'pjax' => '0',
                                                    'url' =>  Url::toRoute(['factura/create-note','id'=>$model['idFactura']]),
                                                ],
                                                'title' => Yii::t('app', 'Nota de Credito')
                                            ]) : '';
                            },
                            'debito' => function ($url, $model) {
                                return $model['tipo_factura'] == "FA" ? Html::a('<span class="glyphicon glyphicon-duplicate"></span>', Url::toRoute(['factura/create-debit','id'=>$model['idFactura']]), [
                                    'class' => 'deleteCrud2',
                                    'onclick' =>'myFunction()',
                                    'id' => 'modaldelete52',
                                    'data' => [
                                        'content' => 'Esta seguro que desea crear Nota de Dedito ?',
                                        'ids'=>'contacto-anular',
                                        'pjax' => '0',
                                        'url' =>  Url::toRoute(['factura/create-debit','id'=>$model['idFactura']]),
                                    ],
                                    'title' => Yii::t('app', 'Nota de Dedito')
                                ]) : '';
                            },
                        ]
                ], 
        ],
    ]); ?>
     <?php  Pjax::end() ?>
</div>
