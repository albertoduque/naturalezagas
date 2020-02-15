<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contactos */

$this->title = 'Actualizar Contactos: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Contactos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->nombre]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contactos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
		,'listPais'=>  $listPais
		,'listDepartamento'=>$listDepartamento
		,'listCiudad'=> $listCiudad
		,'listCargos'=>$listCargos
    ]) ?>

</div>
