<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Eventos */

$this->title = 'ACTUALIZAR EVENTOS: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descripcion, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="eventos-update">
<h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: left;font-size: 18px;"> <?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,'listSectoresEmpresas' => $listSectoresEmpresas,'listTipoEventos' => $listTipoEventos,
        'listPais'=>$listPais,'listCiudad'=>$listCiudad,'listEventos'=>$listEventos
    ]) ?>

</div>