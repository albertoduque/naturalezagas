<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MedioPago */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Medio Pago';
$this->params['breadcrumbs'][] = ['label' => 'Medio Pago', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="medio-pago-create">


    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
