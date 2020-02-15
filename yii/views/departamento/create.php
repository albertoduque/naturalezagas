<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Departamento */

if (!Yii::$app->request->isAjax) {
    $this->title = 'Nuevo Departamento';
    $this->params['breadcrumbs'][] = ['label' => 'Departamentos', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}

?>
<div class="departamento-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'tipociudad'=>$tipociudad
    ]) ?>

</div>
