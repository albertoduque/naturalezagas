<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Empresas */

$this->title = 'ACTUALIZAR EMPRESAS: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => $redirectUrl, 'url' => [$redirectUrl]];
//$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Actualizar'; ['/inscripcion/index-menu']
?>
<div class="empresas-update">

      <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,'listSectoresEmpresas' => $listSectoresEmpresas,
        'listPais'=>$listPais
		,'listDepartamento'=>$listDepartamento
        ,'listCiudad'=>$listCiudad,
        'contacto'=>$contacto,
        'redirectUrl'=>$redirectUrl,
        'listPt'=>$listPt,
        'listTI'=>$listTI
    ]) ?>

</div>
