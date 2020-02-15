<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
$this->title = 'Historial de eventos';
?>
<div class="site-index">
        <h1><?= Html::encode($this->title) ?></h1>

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
      
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             [
                'attribute' => 'nombre',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            [
                'label' => 'Descripción',
                'attribute' => 'descripcion',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            
            ['attribute' => 'fecha_hora_inicio',
             'value' => 'fecha_hora_inicio',
             'format' =>  ['date', 'php:d/m/Y'], 
             'label' => 'Fecha Inicio', 
                'headerOptions' => ['style' => 'text-align: center;'],
             ],
            ['attribute' => 'fecha_hora_fin',
             'value' => 'fecha_hora_fin',
             'format' =>  ['date', 'php:d/m/Y'], 
             'label' => 'Fecha Fin', 
                'headerOptions' => ['style' => 'text-align: center;'],
             ],
            // 'id_ciudad',
            // 'direccion',
            // 'descripcion_sitio:ntext',
            // 'tipo',
            // 'sector',
            // 'encabezado:ntext',
            // 'piedepagina:ntext',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:70px;'],'header'=>'','template' => '{print}',
                    'buttons' => [
                            'print' => function ($url, $model) {
                                $url = Url::toRoute(['site/set-session','event'=>$model['id']]);
                                return Html::a('<span class="btn btn-xs btn-success" >INGRESAR</span>', $url
                                );
                            },                
                            
                             
                        ]
                ], 
        ],
    ]); ?>


</div>
