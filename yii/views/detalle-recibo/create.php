<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DetalleRecibos */
if (!Yii::$app->request->isAjax) {
$this->title = 'Create Detalle Recibos';
$this->params['breadcrumbs'][] = ['label' => 'Detalle Recibos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="detalle-recibos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'listFormaPago'=>$listFormaPago,'vpagos'=>$vpagos
    ]) ?>

</div>
