<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MonedasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monedas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monedas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Monedas', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
                'label' => 'País',
                'attribute' => 'id_pais',
                'value' => function($model, $index, $dataColumn) {
                    return $model->idPais->nombre;
                },
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            'codigo',
            'numero',
            'divisa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
