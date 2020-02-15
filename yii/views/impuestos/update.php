<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Impuestos */
if (!Yii::$app->request->isAjax) {
$this->title = 'Actualizar Impuesto: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Impuesto', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->nombre]];
$this->params['breadcrumbs'][] = 'Actualizar';
}
?>
<div class="impuesto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
