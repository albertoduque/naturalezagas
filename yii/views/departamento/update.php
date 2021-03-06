<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Departamento */

$this->title = 'Actualizar Departamento: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Departamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->nombre]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="departamento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'tipociudad'=>$tipociudad
    ]) ?>

</div>
