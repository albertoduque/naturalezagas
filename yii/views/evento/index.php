<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EventosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eventos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eventos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Evento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'evento_grid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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

            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class=" glyphicon glyphicon-trash"></span>', null,
                            [
                                'class' => 'deleteCrud',
                                'id' => 'modaldelete5',
                                'data' => [
                                    'content' => 'Esta seguro que desea eliminar el evento?',
                                    'ids'=>'sectorempresa-delete',
                                    'pjax' => '0',
                                    'url' =>  Url::toRoute(['evento/delete-ajax','id'=>$model->id,'accion'=>'1']),
                                ],
                            ]);
                    },        
                ]
            ],
        ],
    ]); ?>
     <?php Pjax::end(); ?>
</div>
