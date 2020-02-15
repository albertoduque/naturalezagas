<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Operacion */

$this->title = 'Update Operacion: ' . $model->idOperacion;
$this->params['breadcrumbs'][] = ['label' => 'Operacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idOperacion, 'url' => ['view', 'id' => $model->idOperacion]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="operacion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
