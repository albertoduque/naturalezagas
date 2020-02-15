<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Personas */
if (!Yii::$app->request->isAjax) {
$this->title = 'NUEVO PARTICIPANTE';
$this->params['breadcrumbs'][] = ['label' => 'Inscripción', 'url' => ['/inscripcion/index-menu']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="personas-create">

     <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>

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
