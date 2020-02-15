<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RecibosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recibos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recibos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Recibos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fecha_pago',
            'valor_descuento',
            'valor_retencion',
            'valor_pagado',
            // 'tipo_pago',
            // 'valor_subtotal',
            // 'valor_iva',
            // 'id_forma_pago',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
