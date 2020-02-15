<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CONTACTOS DE FACTURACIÓN';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contactos-index">

    <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <p>
        <?= isset($url)? Html::button('Crear Contacto',['value'=>$url,'class' => 'btn btn-success','id'=>'modal-personas-inscripciones']): Html::a('Create Contacto', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    
    <?php isset($url)? Pjax::begin(['id' => 'contacto-grid']) : '' ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' =>'Id',
                'attribute' => 'id',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            [
                'label' =>'Nombre',
                'attribute' => 'nombre',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            [
                'label' =>'Teléfono',
                'attribute' => 'telefono',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
             [
                'attribute' => 'movil',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            [
                'label' =>'Correo',
                'attribute' => 'correo',
                'headerOptions' => ['style' => 'text-align: center;'],
              
            ],
            // 'id_cargo',
            // 'tipo_contacto',
            // 'created_at',
            // 'modified_at',
            // 'deleted',

           ['class' => 'yii\grid\ActionColumn','contentOptions' => ['style' => 'width:60px;'],'header'=>'','template' => '{update} {delete}',
                'buttons' => [
                        'delete' => function ($url, $model) {
                                
                               return Html::a('<span class=" glyphicon glyphicon-trash "></span>', null, [
                                                'class' => 'deleteCrud',
                                                'id' => 'modaldelete5',
                                                'data' => [
                                                    'content' => 'Esta seguro que desea eliminar el contacto?',
                                                    'ids'=>'contacto-delete',
                                                    'pjax' => '0',
                                                    'url' =>  Url::toRoute(['contacto/delete-ajax','id'=>$model->id,'accion'=>'0']),
                                                ],
                                            ]);
                               
                            },
                             'update' => function ($url, $model) {
                                    return Html::a('<span class=" glyphicon glyphicon-pencil "></span>', ['contacto/update', 'id' => $model['id'], 'redirectUrl' => 'empresa'], ['class' => 'profile-link']);
                                 
                             },
                    ]
            ],
                                 
        ],
                                    
    ]); ?>
    <?php isset($url)? Pjax::end() : ''?>
</div>
