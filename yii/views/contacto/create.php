<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Contactos */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Contactos';
$this->params['breadcrumbs'][] = ['label' => 'Contactos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="contactos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
		,'listPais'=>  $listPais
		,'listDepartamento'=>$listDepartamento
		,'listCiudad'=> $listCiudad
		,'listCargos'=>$listCargos
    ]) ?>

</div>
