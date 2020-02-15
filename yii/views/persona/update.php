<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Personas */

$this->title = 'Actualizar Persona: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Inscripción', 'url' => ['/inscripcion/index-menu']];
//$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="personas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
       'model' => $model
	   ,'listCargos'=>$listCargos
	   ,'listPais'=>$listPais
	   ,'listDepartamento'=>$listDepartamento
	   ,'listCiudad'=>$listCiudad,
        'listAsistente'=>$listAsistente,
		'listTI'=>$listTI,
    ]) ?>

</div>
