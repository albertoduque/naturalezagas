<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DescripcionProductos */

$this->title = 'Create Descripcion Productos';
$this->params['breadcrumbs'][] = ['label' => 'Descripcion Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="descripcion-productos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'listProductos'=>$listProductos
    ]) ?>

</div>
