<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MedioPago */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Tipo Nota';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Nota', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="medio-pago-create">


    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
