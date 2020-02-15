<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MedioPagoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Medio de Pago';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medio-pago-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Nuevo Medio Pago', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codigo',
            'medio_pago',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
