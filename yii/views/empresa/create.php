<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Empresas */

$this->title = 'Nueva Empresa';
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="empresas-create">

    <?= $this->render('_form', [
        'model' => $model,'listSectoresEmpresas' => $listSectoresEmpresas,
        'listPais'=>$listPais
		,'listDepartamento'=>$listDepartamento
		,'listCiudad'=>$listCiudad,
        'contacto'=>$contacto,'listCargos'=>$listCargos,
        'persona'=>$persona,
        'visible'=>$visible,
        'listPt'=>$listPt,
        'listTI'=>$listTI,
    ]) ?>

</div>
