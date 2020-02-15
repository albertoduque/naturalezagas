<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Inscripciones */

$this->title = 'NUEVA INSCRIPCIÓN';
$this->params['breadcrumbs'][] = ['label' => 'Inscripciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inscripciones-create">

    <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
