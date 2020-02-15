<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menu Principal';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="producto-index">

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
                                    <?php if($cuotas) { ?>
                                    <div class="col-sm-6 col-xs-6 col-lg-6">
                                    <?php }else{ ?>
                                    <div class="col-sm-12 col-xs-12 col-lg-12">
                                    <?php } ?>
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="EVENTOS CONGRESO">
                                            <a href="<?=Url::toRoute(['index-menus'])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">assignment</i>
                                                </div>
                                             </a>
                                            <h6>EVENTOS</h6>
                                        </div>
                                    </div>
                                    <?php if($cuotas) { ?>
                                    <div class="col-sm-6 col-xs-6 col-lg-6">
                                        <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="CUOTAS DE SOTENIMIENTO">
                                            <a href="<?=Url::toRoute(['site/set-session', 'event' => 109])?>" >
                                                <input type="radio" name="job" value="Code">
                                                <div class="icon">
                                                    <i class="material-icons">work</i>
                                                </div>
                                             </a>
                                            <h6>CUOTAS DE SOTENIMIENTO</h6>
                                        </div>
                                    </div>
                                    <?php } ?>
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
