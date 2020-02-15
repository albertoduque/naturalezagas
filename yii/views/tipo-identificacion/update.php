<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoIdentificacion */
if (!Yii::$app->request->isAjax) {
$this->title = 'Actualizar Tipo Identificación: ' . $model->significado;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Identificación', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->significado]];
$this->params['breadcrumbs'][] = 'Actualizar';
}
?>
<div class="tipo-identificacion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
