<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DetalleRecibosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Detalle Recibos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detalle-recibos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Detalle Recibos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_detalle_factura',
            'fecha_pago',
            'valor',
            'id_forma_pago',
            'tipo_pago',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
