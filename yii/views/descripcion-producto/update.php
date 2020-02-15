<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DescripcionProductos */

$this->title = 'Actualizar Descripción:'. $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Descripción', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="descripcion-productos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'listProductos'=>$listProductos
    ]) ?>

</div>
