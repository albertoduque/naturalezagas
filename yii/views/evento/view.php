<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Eventos */

$this->title = $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eventos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php 
            echo Html::a('Eliminar', null, 
                    [
                        'class' => 'btn btn-danger deleteCrud',
                        'id' => 'modaldelete5',
                        'data' => [
                            'content' => 'Esta seguro que desea eliminar el evento?',
                            'ids'=>'evento-delete',
                            'pjax' => '0',
                            'url' =>  Url::toRoute(['evento/delete-ajax','id'=>$model->id,'accion'=>'0']),
                        ],
                    ]) 
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'descripcion:ntext',
            ['attribute' => 'fecha_hora_inicio',
             'value' => \Yii::t('app', Yii::$app->formatter->asDate($model->fecha_hora_fin,'php:d/m/Y')),
            ],
            ['attribute' => 'fecha_hora_fin',
             'value' => \Yii::t('app', Yii::$app->formatter->asDate($model->fecha_hora_fin,'php:d/m/Y')),
            ],
            'id_ciudad',
            'direccion',
            'descripcion_sitio:ntext',
            'tipo',
            'id_sector',
    
        ],
    ]) ?>

</div>
