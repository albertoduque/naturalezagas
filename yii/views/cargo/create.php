<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cargos */
if (!Yii::$app->request->isAjax) {
$this->title = 'NUEVO CARGO';
$this->params['breadcrumbs'][] = ['label' => 'Cargos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="cargos-create">


    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
