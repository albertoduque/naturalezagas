<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Rol;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>
    <p>
        <?= Html::a('Nuevo Usuario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'cedula',
            'nombre',
            [ 
                'attribute' => 'email',
                'label' => 'Correo',
            ],
            [ 
                'attribute' => 'username',
                'label' => 'Usuario',
            ],
            [
                'attribute' => 'rol_nombre',
                'label' => 'Rol',
                'filter' => Html::activeDropDownList($searchModel,'rol_nombre',Rol::dropdown(),['class'=>'form-control','prompt' => 'Todos']),
                'value' => function($model, $index, $dataColumn) {
                    $roleDropdown = Rol::dropdown();
                    return $roleDropdown[$model->rol_id];
                },
            ],                 
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
