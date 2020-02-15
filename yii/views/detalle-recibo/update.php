<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DetalleRecibos */

$this->title = 'Update Detalle Recibos: ' . $model->id_detalle_factura;
$this->params['breadcrumbs'][] = ['label' => 'Detalle Recibos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_detalle_factura, 'url' => ['view', 'id' => $model->id_detalle_factura]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="detalle-recibos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
