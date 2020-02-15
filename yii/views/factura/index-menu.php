<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menu Facturación';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="producto-index">
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<hr>
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="wizard-container">
                <div class="card wizard-card" data-color="red" id="wizard">
                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#captain" data-toggle="tab"><?= Html::encode($this->title) ?></a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane" id="captain">
                            <h4 class="info-text">Que operación desea realizar? </h4>
                            <div class="row">
                                <div class="col-sm-10 col-sm-offset-1">
                                    
                                    <div class="col-sm-6 col-xs-6 col-lg-3" style="display: none">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="facturar inscritos">
                                            <a href="<?=Url::toRoute(['factura/facturacion'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">assignment</i>
                                                </div>
                                             </a>
                                            <h6>FACTURAR INSCRITOS</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-lg-3" style="display: none">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="Facturar Patrocinios">
                                            <a href="<?=Url::toRoute(['factura/create'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">work</i>
                                                </div>
                                             </a>
                                            <h6>FACTURAR PATROCINIOS</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-lg-6">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="Productos Facturados">
                                            <a href="<?=Url::toRoute(['factura/facturados'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">assignment_turned_in</i>
                                                </div>
                                            </a>    
                                            <h6>GESTIÓN DE FACTURACIÓN</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-lg-6">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="Estadísticas Actuales">
                                            <a href="<?=Url::toRoute(['factura/estadisticas'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">assessment</i>
                                                </div>
                                            </a>    
                                            <h6>ESTADÍSTICAS</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
            </div> <!-- wizard container -->
        </div>
    </div> <!-- row -->
</div> <!--  big container -->
</div>
