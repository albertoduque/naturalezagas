<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Pais */
if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Pais';
$this->params['breadcrumbs'][] = ['label' => 'Pais', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="pais-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
