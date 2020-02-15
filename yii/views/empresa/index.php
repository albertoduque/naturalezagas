<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EmpresaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Empresas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="empresas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
     <p>
        <?= Html::a('NUEVA EMPRESA', ['empresa/create?visible=1'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'nombre',
            'identificacion',
            'direccion',
            'telefono',
            [
                'attribute' => 'is_patrocinios',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['is_patrocinios'] === '1' ? 'SI' : 'NO';
                }, 
                'filter' => Html::activeDropDownList($searchModel,'is_patrocinios',['1'=>'SI','0'=>'NO'],['class'=>'form-control','prompt' => 'Todos']),
               /* 'value' => function($model, $index, $dataColumn) {
                    $roleDropdown = TipoAsistentes::toList();
                    return $roleDropdown[$model->idPersona->idTipoAsistente->id];
                },*/
                'headerOptions' => ['style' => 'width:10%;text-align: center;'],
                'filterInputOptions' => ['placeholder' => 'Filtro','class' => 'form-control',],
            ],
            // 'telefono_extension',
            // 'movil',
            // 'id_ciudad',
            // 'afiliado_gremio',
            // 'estado',
            // 'id_sector_empresa',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:70px;'],'header'=>'','template' => '{view} {update}{delete}{users}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class=" glyphicon glyphicon-pencil "></span>', ['empresa/update', 'id' => $model['id'], 'redirectUrl' => 'empresa'], ['class' => 'profile-link']);
                        /*return Html::a('<span class=" glyphicon glyphicon-pencil "></span>', null, [
                            'class' => 'deleteCrud',
                            'id' => 'modaldelete',
                            'data' => [
                                'content' => 'Esta seguro que desea eliminar el Cargo?',
                                'ids'=>'cargo-delete',
                                'pjax' => '0',
                                'url' =>  Url::toRoute(['cargo/delete-ajax','id'=>$model['id'],'accion'=>'0']),
                            ],
                            'title' => Yii::t('app', 'Eliminar Cargo')
                        ]);*/

                    },
                    'users' =>  function ($url, $model) {
                        $url = Url::toRoute(['inscripcion/inscripcion-empresa-persona','idEmpresa'=>$model['id']]);
                        return Html::a('<span class=" glyphicon glyphicon-user "></span>', [$url], ['class' => 'profile-link','title' => Yii::t('app', 'Inscritos')]);
                    },  
                ]
            ],
        ],
    ]); ?>
</div>
