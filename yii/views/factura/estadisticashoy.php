<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InscripcionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Estadísticas Actuales';
$this->params['breadcrumbs'][] = $this->title;

?>
 <?php if(!$pdf) { ?>
<style type="text/css">

table {
font-family: "Lato","sans-serif";	}		/* added custom font-family  */

table.one {									 
margin-bottom: 3em;	
border-collapse:collapse;	}	

td {							/* removed the border from the table data rows  */    
width: 10em;					
padding: 1em; 		}		

th {							  /* removed the border from the table heading row  */
text-align: center;					
padding: 1em;
background-color: #e8503a;	     /* added a red background color to the heading cells  */
color: white;		}			      /* added a white font color to the heading text */

tr {	
height: 1em;	}

table tr:nth-child(even) {		      /* added all even rows a #eee color  */
       background-color: #eee;		}

table tr:nth-child(odd) {		     /* added all odd rows a #fff color  */
background-color:#fff;		}

</style>
 <?php } ?>
<div class="facturas-index">
     <h4 class="info-text" style=" font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;font-weight: bold;text-align: center;font-size: 18px;"> ESTADÍSTICAS ACTUALES</h4>
    <br>
    
     <?php if (Yii::$app->session->hasFlash('success')): ?>
  <div class="alert alert-success alert-dismissable">
  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
  <h4><i class="icon fa fa-check"></i>Correo Enviado!</h4>
  <?= Yii::$app->session->getFlash('success') ?>
  </div>
<?php endif; ?>    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row" >
        <h4>Inscritos</h4>
        <div class="col-lg-12">
            <div class="col-sm-6">
            <table class="table" style="width:400px">
                <thead >
                    <tr>
                <th style="text-align: center">Descripción</th>
                <th style="text-align: center">Cantidad</th>
                </tr>
                </thead>
                <tbody align="center">
                <?php 
                    $cont=0;
                    $ban=true;
                    $totalinscritos=0;
                    $color="#fff";
                     foreach ($modelEstadisticasCount as $key=>$detalle) {
                         if($detalle['isCount']==1)
                             $totalinscritos+=$detalle['inscritos'];
                         if($detalle['idEstados']==3) 
                        {
                            $insc = $detalle['inscritos'];
                            $ban=false; 
                        }
                        if($detalle['idEstados']==1) 
                        {
                            $pendiente = $detalle['inscritos'];
                        }
                     }
                 ?>	
                  <tr>    
                        <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                            <span style="text-align: center">TOTAL INSCRITOS</span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                            <span style="float: 'right'"><?=$totalinscritos?></span> 
                        </td>
                    </tr>
                   <?php  if(!$ban) { ?>
                        <tr style="background-color:<?= $color ?>">    
                            <td align="left"   style="padding: '12px'; height: '51px';">						
                                <span style="text-align: center">FACTURADOS</span> 
                            </td>
                            <td align="right"   style="padding: '12px'; height: '51px';">					
                                <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($insc+$pendiente,0)?></span> 
                            </td>
                        </tr>
                    <?php }?>
                  <?php 
                  $ban=true;
                    foreach ($modelEstadisticasCount as $key=>$detalle) { 
                        $cont++;
                        $mod=0;
                        $mod = $cont % 2 == 0;
                        $color =  $mod == 1 ? "#eee" : "#fff";
                        if($detalle['idEstados']==3) 
                        {
                            $insc = $detalle['inscritos'];
                            $ban=false; 
                        }
                        if($detalle['idEstados']==1) 
                        {
                            $pendiente = $detalle['inscritos'];
                        }
                    ?>
                    <tr style="background-color:<?= $color ?>">    
                        <td align="left"   style="padding: '12px'; height: '51px';">						
                            <span style="text-align: center"><?= $detalle['estados'] ?></span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';">					
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['inscritos'],0)?></span> 
                        </td>
                    </tr>
                    <?php } ?>	
                    
                   
                </tbody>
            </table>
            </div>
            <div class="col-sm-6">
            <table class="table" style="width:400px">
                <thead >
                <tr>
                    <th style="text-align: center">Descripción</th>
                    <th style="text-align: center">Valor</th>
                </tr>
                </thead>
                      <tbody align="center">
                <?php 
                    $cont=0;
                    $ban=true;
                    $totalinscritos=0;
                    $color="#fff";
                     foreach ($modelEstadisticasSum as $key=>$detalle) { 
                        $totalinscritos+=$detalle['valor'];
                         if($detalle['id']==3) 
                        {
                            $insc = $detalle['valor'];
                            $ban=false; 
                        }
                        if($detalle['id']==1) 
                        {
                            $pendiente = $detalle['valor'];
                        }
                     }
                 ?>	
                  <tr>    
                        <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                            <span style="text-align: center">TOTAL INSCRITOS</span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalinscritos,0)?></span> 
                        </td>
                    </tr>
                   <?php  if(!$ban) { ?>
                        <tr style="background-color:<?= $color ?>">    
                            <td align="left"   style="padding: '12px'; height: '51px';">						
                                <span style="text-align: center">FACTURADOS</span> 
                            </td>
                            <td align="right"   style="padding: '12px'; height: '51px';">					
                                <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($insc+$pendiente,0)?></span> 
                            </td>
                        </tr>
                    <?php }?>
                  <?php 
                  $ban=true;
                    foreach ($modelEstadisticasSum as $key=>$detalle) { 
                        $cont++;
                        $mod=0;
                        $mod = $cont % 2 == 0;
                        $color =  $mod == 1 ? "#eee" : "#fff";
                        if($detalle['id']==3) 
                        {
                            $insc = $detalle['valor'];
                            $ban=false; 
                        }
                        if($detalle['id']==1) 
                        {
                            $pendiente = $detalle['valor'];
                        }
                    ?>
                    <tr style="background-color:<?= $color ?>">    
                        <td align="left"   style="padding: '12px'; height: '51px';">						
                            <span style="text-align: center"><?= $detalle['estados'] ?></span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';">					
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span> 
                        </td>
                    </tr>
                    <?php } ?>	
                    
                    <?php if($ban) { ?>
                    <tr>    
                        <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                            <span style="text-align: center"><?= 'PAGO TOTAL' ?></span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                            <span style="float: 'right'"><?=0?></span> 
                        </td>
                    </tr>
                    <?php } ?>
                   
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="row" >
        <h4>Patrocinios</h4>
        <div class="col-lg-12">
            <div class="col-sm-6">
                <table class="table" style="width:400px">
                    <thead >
                        <tr>
                            <th style="text-align: center">Descripción</th>
                            <th style="text-align: center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                            <?php 
                                $parcialPago=$totalPago=$pendientePago=$pendienteInscritos=$parcialInscritos=$totalinscritos=$totalInscritos=$totalPagos=0;
                                foreach ($modelPatrocinios as $key=>$detalle) { 
                                    $totalinscritos+=$detalle['inscritos'];
                                    $totalPagos+=$detalle['total'];
                                    if(!isset($detalle['idEstados']))
                                    {
                                        $detalle['idEstados']=0;
                                    }
                                    if($detalle['idEstados']==1) 
                                    {
                                        $pendienteInscritos = $detalle['inscritos'];
                                        $pendientePago = $detalle['total'];
                                    }
                                     if($detalle['idEstados']==2) 
                                    {
                                        $parcialInscritos = $detalle['inscritos'];
                                        $parcialPago = $detalle['total'];
                                    }
                                     if($detalle['idEstados']==3) 
                                    {
                                        $totalInscritos = $detalle['inscritos'];
                                        $totalPago = $detalle['total'];
                                    }
                                 }
                             ?>	
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'TOTAL PATROCINIOS' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=$totalinscritos?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'FACTURADOS' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=$totalinscritos?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'PENDIENTE DE PAGO' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=$pendienteInscritos?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'PAGO TOTAL' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=$totalInscritos?></span> 
                                </td>
                            </tr>
                            
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6">
                 <table class="table" style="width:400px">
                    <thead >
                        <tr>
                            <th style="text-align: center">Descripción</th>
                            <th style="text-align: center">Valor</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                          <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'TOTAL PATROCINIOS' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalPagos,0)?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'FACTURADOS' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalPagos,0)?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'PENDIENTE DE PAGO' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($pendientePago,0)?></span> 
                                </td>
                            </tr>
                            <tr>    
                                <td align="left"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">						
                                    <span style="text-align: center"><?= 'PAGO TOTAL' ?></span> 
                                </td>
                                <td align="right"   style="padding: '12px'; height: '51px';border-bottom: 1px solid #ddd">					
                                    <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalPago,0)?></span> 
                                </td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>  
     <div><p style="font-weight: bold;">Nota : Los valores reflejados son antes de impuestos.</p></div> 
     <?php if(!$pdf) { ?>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    <?=  Html::a('Imprimir', FALSE, [
                        'id' => 'modal-modalht',
                        'title' => Yii::t('app', 'Imprimir'),
                        'data' => [
                            'title'=>'Reporte de Estadísticas',
                            'pjax' => '0',
                            'value' =>  Url::toRoute(['factura/generar-estadisticas-pdf']),
                        ],'class' => 'btn btn-success'
                    ]);?>
                    <?= Html::a('Enviar Correo', ['factura/send-email'], ['class' => 'btn btn-info']) ?>
                </p>
            </div>
        </div>
    <?php } ?>
</div>
