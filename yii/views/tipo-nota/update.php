<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MedioPago */
if (!Yii::$app->request->isAjax) {
$this->title = 'Actualizar Tipo Nota: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Nota', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->nombre]];
$this->params['breadcrumbs'][] = 'Actualizar';
}
?>
<div class="medio-pago-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
