<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\SectoresEmpresas */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sectores Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sectores-empresas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php 
            echo Html::a('Eliminar', null, 
                    [
                        'class' => 'btn btn-danger deleteCrud',
                        'id' => 'modaldelete5',
                        'data' => [
                            'content' => 'Esta seguro que desea eliminar el sector empresa?',
                            'ids'=>'sectorempresa-delete',
                            'pjax' => '0',
                            'url' =>  Url::toRoute(['sector-empresa/delete-ajax','id'=>$model->id,'accion'=>'0']),
                        ],
                    ]) 
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            [
                'label' => 'Estado',
                'attribute' => 'Estado',
                'value' =>  $model->deleted == 0 ? 'Activo' : 'Inactivo'
            ],
        ],
    ]) ?>

</div>
