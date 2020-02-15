<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SectoresEmpresasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sectores Empresas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sectores-empresas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Sectore Empresa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'sectorEmpresa-grid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'nombre',
            [
                'attribute' => 'Estado',
                'value' => function ($data) {
                    return $data->deleted ? 'Inactivo' : 'Activo'; 
                },
            ],
            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class=" glyphicon glyphicon-trash"></span>', null,
                            [
                                'class' => 'deleteCrud',
                                'id' => 'modaldelete5',
                                'data' => [
                                    'content' => 'Esta seguro que desea eliminar el sector empresa?',
                                    'ids'=>'sectorempresa-delete',
                                    'pjax' => '0',
                                    'url' =>  Url::toRoute(['sector-empresa/delete-ajax','id'=>$model->id,'accion'=>'1']),
                                ],
                            ]);
                    },        
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
