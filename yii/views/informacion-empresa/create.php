<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\InformacionEmpresa */

$this->title = 'NUEVA INFORMACIÓN DE EMPRESA';
$this->params['breadcrumbs'][] = ['label' => 'Información Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informacion-empresa-create">

    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
