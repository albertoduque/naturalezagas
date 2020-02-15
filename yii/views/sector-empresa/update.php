<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SectoresEmpresas */

$this->title = 'Actualizar Sectores Empresas: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Sectores Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sectores-empresas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
