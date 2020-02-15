<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menu Inscripciones';
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
                                    <div class="col-sm-6">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="Inscripción Empresas">
                                            <a href="<?=Url::toRoute(['inscripcion/inscripcion-empresa'])?>" >
                                                <input type="radio" name="job" value="Design">
                                                <div class="icon">
                                                    <i class="material-icons">business</i>
                                                </div>
                                            </a>
                                            <h6>Inscripción Empresas</h6>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="Consultar Empresas">
                                            <a href="<?=Url::toRoute(['/empresa'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">location_city</i>
                                                </div>
                                            </a>    
                                            <h6>Consultar Empresas</h6>
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
