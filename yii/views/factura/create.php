<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Facturas */
if (!Yii::$app->request->isAjax) {
$this->title = $model->is_patrocinios ? 'FACTURA PRODUCTOS' : 'FACTURA INSCRITOS';
$this->params['breadcrumbs'][] = ['label' => 'Facturas', 'url' => ['factura/facturados']];
$this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="facturas-create">


    <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>
    <?php
        if($model->is_patrocinios)
        {
            $clientes = $listClientes;
        }
        else
        {
            $clientes = $model->id_empresa ? $listEmpresas : $listPersonas  ;
        }
    ?>
    <?= $this->render('_form', [
        'model' => $model
			,'detalle_factura'=>$detalle_factura
			,'listProducto'=>$listProducto
			,'listMoneda'=>$listMoneda
            ,'listContacto'=>$listContacto
			,'listClientes'=>$clientes 
			,'listImpuestos'=>$listImpuestos 
			,'listMedioPago'=>$listMedioPago 
			,'title'=> isset($title) ? $title : ''
    ]) ?>

</div>
