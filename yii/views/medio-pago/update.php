<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MedioPago */
if (!Yii::$app->request->isAjax) {
$this->title = 'Actualizar Medio Pago: ' . $model->medio_pago;
$this->params['breadcrumbs'][] = ['label' => 'Medio Pago', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->medio_pago]];
$this->params['breadcrumbs'][] = 'Actualizar';
}
?>
<div class="medio-pago-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
