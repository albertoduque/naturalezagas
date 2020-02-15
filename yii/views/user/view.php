<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->cedula;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode("Detalle del Usuario: ".$this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'deletes btn btn-danger',
            'data' => [
                'content' => 'Esta seguro que desea eliminar el usuario?',
            ],
        ]) ?>
    </p>
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cedula',
            'nombre',
            'telefono',
            'email:email',
            [ 
                'attribute' => 'username',
                'label' => 'Usuario',
            ],
            ['attribute' => 'created_at',
             'value' => \Yii::t('app', Yii::$app->formatter->asDate($model->created_at,'php:d/m/Y')),
              
               'label' => 'Fecha de Creado',
            ],
            [ 
                'attribute' => 'rol.nombre',
                'label' => 'Rol',
            ],
            [
                'label' => 'status',
                'attribute' => 'status',
                'value' =>  $model->status == 10 ? 'ACTIVO' : 'INACTIVO'
            ],
        ],
    ]) ?>
</div>
