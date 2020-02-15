<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Ciudad */

if (!Yii::$app->request->isAjax) {
    $this->title = 'Nueva Ciudad';
    $this->params['breadcrumbs'][] = ['label' => 'Ciudades', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}

?>
<div class="ciudad-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'tipociudad'=>$tipociudad,
		'listPais'=>$listPais,'listDepartamento'=>$listDepartamento,
    ]) ?>

</div>
