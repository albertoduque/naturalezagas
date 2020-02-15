<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Parametros */

$this->title = 'Nuevo Parametro';
$this->params['breadcrumbs'][] = ['label' => 'Parametros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parametros-create">

    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
