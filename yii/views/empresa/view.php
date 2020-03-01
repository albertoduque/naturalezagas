<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Empresas */

$this->title = $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="empresas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            'nombre',
            'identificacion',
            'direccion',
            'telefono',
            'telefono_extension',
            'movil',
            ['attribute' => 'pais','value' => $model->ciudad->idPais->nombre],
            ['attribute' => 'id_ciudad','value' => $model->ciudad->nombre],
            ['attribute' => 'afiliado_gremio','value' => $model->afiliado_gremio ? 'SI':'NO',],
           
        ],
    ]) ?>

</div>
