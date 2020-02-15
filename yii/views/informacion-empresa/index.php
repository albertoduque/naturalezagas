<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'INFORMACIÓN DE EMPRESA';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informacion-empresa-index">

    <h4 class="info-text"><?= Html::encode($this->title) ?></h4>

    <p>
        <?= $count == 0 ? Html::a('Nueva Información Empresa', ['create'], ['class' => 'btn btn-success']) : '' ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nombre',
            'direccion',
            'telefono',
            'pagina_web',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
