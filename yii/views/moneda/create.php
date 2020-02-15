<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Monedas */

$this->title = 'Create Monedas';
$this->params['breadcrumbs'][] = ['label' => 'Monedas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monedas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
