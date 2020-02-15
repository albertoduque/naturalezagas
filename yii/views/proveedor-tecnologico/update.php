<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProveedorTecnologico */

$this->title = 'Actualizar Proveedor Tecnologico: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Proveedor Tecnologicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="proveedor-tecnologico-update">

    <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
