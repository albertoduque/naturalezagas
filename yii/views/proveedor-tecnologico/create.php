<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProveedorTecnologico */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Proveedor Tecnológico';
$this->params['breadcrumbs'][] = ['label' => 'Proveedor Tecnológicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="proveedor-tecnologico-create">


    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>