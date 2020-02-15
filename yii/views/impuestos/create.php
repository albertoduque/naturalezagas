<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Impuestos */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Impuesto';
$this->params['breadcrumbs'][] = ['label' => 'Impuesto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="impuestos-create">


    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
