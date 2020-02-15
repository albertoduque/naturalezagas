<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CiudadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ciudades';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ciudad-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nueva Ciudad', ['create'], ['class' => 'btn btn-success']) ?>
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
			[
                'label' => 'Departamento',
                'attribute' => 'id_padre',
                'value' => function($model, $index, $dataColumn) {
                    return $model->idDepartamento->nombre;
                },
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            'codigo',
            'nombre',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
