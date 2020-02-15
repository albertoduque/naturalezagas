<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ImpuestosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Impuestos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="impuestos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Impuesto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'identificador',
            'nombre',
            'descripcion',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
