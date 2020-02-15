<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TipoIdentificacion */

if (!Yii::$app->request->isAjax) {
$this->title = 'Nuevo Tipo Identificación';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Identificación', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="tipo-identificacion-create">


    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'drop'=>$drop
    ]) ?>

</div>
