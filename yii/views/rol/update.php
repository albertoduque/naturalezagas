<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Rol */

$this->title = 'ACTUALIZAR ROL: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->idRol]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="rol-update">

    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,'permisos'=>$permisos
    ]) ?>

</div>
