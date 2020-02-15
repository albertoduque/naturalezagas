<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CargoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CARGOS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cargos-index">

    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Cargo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nombre',

            ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:70px;'],'header'=>'','template' => '{view} {update}{delete}',
                    'buttons' => [  
            
                            'delete' => function ($url, $model) {
                                
                                    return Html::a('<span class=" glyphicon glyphicon-trash "></span>', null, [
                                                'class' => 'deleteCrud',
                                                'id' => 'modaldelete',
                                                'data' => [
                                                    'content' => 'Esta seguro que desea eliminar el Cargo?',
                                                    'ids'=>'cargo-delete',
                                                    'pjax' => '0',
                                                    'url' =>  Url::toRoute(['cargo/delete-ajax','id'=>$model['id'],'accion'=>'0']),
                                                ],
                                                'title' => Yii::t('app', 'Eliminar Cargo')
                                            ]);
                                
                            },
                        ]
                ], 
        ],
    ]); ?>
</div>
