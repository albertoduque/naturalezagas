<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Error';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>
                    Oops!</h1>
                <h2>
                    Acceso Restringido</h2>
                <div class="error-details">
                   Perdon, usted no tiene permiso para entrar a esta pagina!
                </div>
                <div class="error-actions">
                    <a href="<?= Url::home() ?>" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-home"></span>
                        Ir la inicio </a>
                    <!--<a href="index.php?r=site/login" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-user"></span> Loggin </a>-->
                </div>
            </div>
        </div>
    </div>
</div>

