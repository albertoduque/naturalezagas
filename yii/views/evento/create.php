<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Eventos */

$this->title = 'Nuevo Eventos';
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eventos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,'listSectoresEmpresas' => $listSectoresEmpresas,'listTipoEventos' => $listTipoEventos,
        'listPais'=>$listPais,'listCiudad'=>$listCiudad,'listEventos'=>$listEventos
    ]) ?>

</div>
