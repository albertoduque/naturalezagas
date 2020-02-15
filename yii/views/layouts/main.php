<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url; 
use  yii\bootstrap\Modal;
use  yii\web\Session;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
       
       // 'brandLabel' => Html::img('@web/img/logo.png',['width'=>'100%','alt'=>Yii::$app->name,'class'=>'cotizador-logo']),
      //  'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    
    $session = Yii::$app->session;
    if (!\Yii::$app->user->isGuest && !$session->isActive && Yii::$app->request->url!="/") {
        $session->destroy();
        return Yii::$app->response->redirect(Url::to(['site/login']));
    }
    $evento="";
    $CUOTA = 109;
    if($session->get('event_id'))
    {
        $evento = strpos(strtoupper($session->get('event_name')), "CONGRESO") === false ? "EMPRESAS" : "INSCRIPCIONES";
        $urlEmpresa = $session->get('event_id')==$CUOTA ? '/empresa' : '/inscripcion/index-menu';
        $urlFacturacion = $session->get('event_id')==$CUOTA ? '/factura/facturados' : '/factura/index-menu';
    }
    $urlIndexMenu = strpos(strtoupper(Yii::$app->request->url), "INDEX-MENUS") === false ? false : true;
    $urlIndex = Url::home() ==  Yii::$app->request->url? false : true;
    $isIndex = $urlIndexMenu || $urlIndex;

    //$auth = Yii::$app->authManager;
     if (!\Yii::$app->user->isGuest && $session->isActive) {
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => [
            $session->get('event_id')  && $isIndex?
            ['label' => 'Home', 'url' => ['/site/login']] : '',
            Yii::$app->user->can('inscripciones') && $session->get('event_id') && $isIndex ?
            ['label' => $evento, 'url' => [$urlEmpresa]] : '',
            Yii::$app->user->can('inscripciones') && $session->get('event_id') && $isIndex && $session->get('event_id') <> $CUOTA ?
            ['label' => 'PATROCINIOS', 'url' => ['/empresa']] : '',
             $session->get('event_id') && $isIndex?
            ['label' => 'Facturación', 'url' => [$urlFacturacion]] : '',
            Yii::$app->user->can('reportes')  && $session->get('event_id')  && $isIndex ?
            ['label' => 'Reportes', 
                'items' => [
                     $session->get('event_id') != $CUOTA ?
                     ['label' => 'Reporte Inscritos al Evento', 'url' => ['/factura/generar-excel-participantes']] : '',
                      $session->get('event_id') != $CUOTA ?
                     ['label' => 'Reporte Patrocinios', 'url' => ['/factura/generar-excel-patrocinios']] : '',
                      $session->get('event_id') != $CUOTA ?
                     ['label' => 'Reporte de NC', 'url' => ['/factura/generar-excel-nc']] : '',
                     $session->get('event_id') == $CUOTA ?
                     ['label' => 'Reporte Cuota Sostenimiento', 'url' => ['/factura/generar-sostenimiento']] : '',
                      $session->get('event_id') == $CUOTA ?
                     ['label' => 'Reporte Notas Credito', 'url' => ['/factura/generar-sostenimientonc']] : '',
                     
                ],
            ] : '',
                Yii::$app->user->can('configuracion') && $session->get('event_id') && $isIndex?
            [ // el 2 es el rol de administrador
                'label' => 'Configuración',
                'items' => [
					'<li class="dropdown-submenu">
                         <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tablas DIAN</a>
                            <ul class="dropdown-menu">',
                                    ['label' => 'Ciudad', 'url' => ['/ciudad']],
                                    '<li class="divider"></li>',
									['label' => 'Departamentos', 'url' => ['/departamento']],
                                    '<li class="divider"></li>',
									['label' => 'Impuestos', 'url' => ['/impuestos']],
									'<li class="divider"></li>',
									['label' => 'Medio de Pago', 'url' => ['/medio-pago']],
									'<li class="divider"></li>',
									['label' => 'Moneda', 'url' => ['/moneda']],
									'<li class="divider"></li>',
                                     ['label' => 'País', 'url' => ['/pais']],
									'<li class="divider"></li>',
									['label' => 'Tipo Identificación', 'url' => ['/tipo-identificacion']],
                            '</ul>
                     </li>',
                     '<li class="dropdown-submenu">
                         <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tablas del Sistema</a>
                            <ul class="dropdown-menu">',
                                    ['label' => 'Eventos', 'url' => ['/evento']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Información Empresa', 'url' => ['/informacion-empresa/index']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Parametros', 'url' => ['/parametro']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Producto', 'url' => ['/producto/index']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Proveedor Tecnológico', 'url' => ['/proveedor-tecnologico/index']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Roles', 'url' => ['/rol']],
									'<li class="divider"></li>',
                                    ['label' => 'Usuarios', 'url' => ['/user']],
                            '</ul>
                     </li>',
                    '<li class="dropdown-submenu">
                         <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tablas del Evento</a>
                            <ul class="dropdown-menu">',
                                    ['label' => 'Cargos', 'url' => ['/cargo']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Empresas', 'url' => ['/empresa']],
                                    '<li class="divider"></li>',
                                    ['label' => 'Forma Pago', 'url' => ['/forma-pago']],
                                    '<li class="divider"></li>',
                                     ['label' => 'Tipo Asistente', 'url' => ['/tipo-asistente']],
                                   
                            '</ul>
                     </li>',
                    
                    
                ]
            ] : '',
            ['label' => '<span class="glyphicon glyphicon-off"></span> Logout [' . Html::encode(Yii::$app->user->identity->username) . ']',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']],
            $session->isActive && $session->get('event_id') ? 
            ['label'=> $session->get('event_name')] : '',
        ],
    ]);
     }
    NavBar::end();
     
    ?>
 <div class="logo-container">
    <div class="brand">
        Naturgas
    </div>
</div>
    
    <div class="container">
        <div style="float:right;margin-top: 10px;margin-right: 10px;">
         <?php if($session->isActive && $session->get('event_id')) echo $session->get('event_name') ?>
        </div>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
    <?php Modal::begin([
            'header'=>'<h4 id="modalHeader">Datos</h4>',
            'id'=>'modal-inicial',
            'size'=>'modal-lg'
       ]);
       echo "<div id='modalContent'></div>";
       Modal::end();?>
    <?php Modal::begin([
            'header'=>'<h4 id="modalHeader">Datos</h4>',
            'id'=>'modal-alterno',
            'size'=>'modal-lg'
       ]);
       echo "<div id='modalContent'></div>";
       Modal::end();?>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Naturgas S.A.S. <?= date('Y') ?></p>

   
    </div>
</footer>

<?php $this->endBody() ?>
    <div class="modalcompleto"></div>
</body>
</html>
<?php $this->endPage() ?>
