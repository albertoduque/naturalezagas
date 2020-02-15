<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="productos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Productos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
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
                'label' => 'Evento',
                'attribute' => 'idEvento.nombre',
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'descripcion',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            ['label' => 'Valor',
                'attribute' => 'valor', 'headerOptions' => ['style' => 'width:10%;text-align: center'],'contentOptions' => ['style' => 'text-align: right'],
                'value'=>function($model){
                   return  Yii::$app->formatter->asDecimal($model->valor,0);
                }
            ],
            'iva',
            // 'imagen',
            // 'activo',
            // 'inscripciones',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
