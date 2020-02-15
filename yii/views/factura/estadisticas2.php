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
    <div class="row" >
        <p>NATURGAS<br>Asociación Colombiana de Gas Natural
        <br>Informe de Ingresos Totales Inscripciones Congreso 2019<br>
        Informe a: <?=  date("j/n/Y") ?>
        </p>
        <div class="col-lg-12">
            <table class="table" style="width:90%">
                <thead >
                <tr style="background-color:#e8503a;color: white;">
                    <th style="text-align: center">Concepto</th>
                    <th style="text-align: center">Valor sin IVA</th>
                    <th style="text-align: center">IVA</th>
                    <th style="text-align: center">Valor total</th>
                </tr>
                </thead>
                <tbody align="center">
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($modelEstadisticasCount as $key=>$detalle) {
                    $totalSinIva += $detalle['valor'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['valor']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#eee" : "#fff";
                ?>
                <tr style="background-color:#fff">
                    <td align="left" >
                        <span style="text-align: center"><?= $detalle['estados'] ?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['valor'],0)?></span>
                    </td>
                </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left">
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>
                <tr style="background-color:#fff">
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                </tr>
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($valoresFacturados as $key=>$detalle) {
                    $totalSinIva += $detalle['valor'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['valor']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#eee" : "#fff";
                    ?>
                    <tr style="background-color:#fff">
                        <td align="left">
                            <span style="text-align: center"><?= $detalle['estados'] ?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['valor'],0)?></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left">
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>
                <tr style="background-color:#fff">
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                </tr>
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($valoresPagos as $key=>$detalle) {
                    $totalSinIva += $detalle['valor'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['valor']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#fff" : "#fff";
                    ?>
                    <tr style="background-color:<?= $color ?>">
                        <td align="left">
                            <span style="text-align: center"><?= $detalle['estados'] ?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['valor'],0)?></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left">
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <page_break>
    <div class="row" >
        <p>NATURGAS<br>Asociación Colombiana de Gas Natural
        <br>Informe de Cuentas por Cobrar Inscripciones Congreso 2019<br>
        Informe a: <?=  date("j/n/Y") ?>
        </p>
        <div class="col-lg-12">
            <table class="table" style="width:90%">
                <thead >
                <tr style="background-color:#e8503a;color: white;">
                    <th style="text-align: center">Concepto</th>
                    <th style="text-align: center">Valor sin IVA</th>
                    <th style="text-align: center">IVA</th>
                    <th style="text-align: center">Valor total</th>
                </tr>
                </thead>
                <tbody align="center">
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($valoresCC as $key=>$detalle) {
                    $totalSinIva += $detalle['valor'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['valor']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#eee" : "#fff";
                    ?>
                    <tr style="background-color:#fff">
                        <td align="left">
                            <span style="text-align: center"><?= $detalle['estados'] ?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                        </td>
                        <td align="right">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['valor'],0)?></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left">
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right">
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
    <page_break>     
    <div class="row" >
        <p>NATURGAS<br>Asociación Colombiana de Gas Natural
        <br>Informe de Patrocinios Inscripciones Congreso 2019<br>
        Informe a: <?=  date("j/n/Y") ?>
        </p>
        <div class="col-lg-12">
            <table class="table" style="width:90%">
                <thead >
                <tr style="background-color:#e8503a;color: white;">
                    <th style="text-align: center">Concepto</th>
                    <th style="text-align: center">Valor sin IVA</th>
                    <th style="text-align: center">IVA</th>
                    <th style="text-align: center">Valor total</th>
                </tr>
                </thead>
                <tbody align="center">
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($modelPatrocinios as $key=>$detalle) {
                    $totalSinIva += $detalle['total'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['total']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#eee" : "#fff";
                    ?>
                    <tr style="background-color:#fff">
                        <td align="left" >
                            <span style="text-align: center"><?= $detalle['nombre'] ?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['total'],0)?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['total'],0)?></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left" >
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>
                <tr style="background-color:#fff">
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                </tr>
                <?php
                $cont=0;
                $totalSinIva=0;
                $totalIva=0;
                $total=0;
                foreach ($patrociniosFacturados as $key=>$detalle) {
                    $totalSinIva += $detalle['valor'];
                    $totalIva += $detalle['iva'];
                    $total += ($detalle['iva']+$detalle['valor']);
                    $cont++;
                    $mod=0;
                    $mod = $cont % 2 == 0;
                    $color =  $mod == 1 ? "#eee" : "#fff";
                    ?>
                    <tr style="background-color:#fff">
                        <td align="left" >
                            <span style="text-align: center"><?= $detalle['nombre'] ?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva'],0)?></span>
                        </td>
                        <td align="right" >
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['iva']+$detalle['valor'],0)?></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr style="background-color:#ccc">
                    <td align="left" >
                        <span style="text-align: center">Total</span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalSinIva,0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalIva,0)?></span>
                    </td>
                    <td align="right" >
                        <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($total,0)?></span>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
    <page_break>
    <div class="row">
        <p>NATURGAS<br>Asociación Colombiana de Gas Natural
        <br>Informe de Participantes Inscripciones Congreso 2019<br>
        Informe a: <?=  date("j/n/Y") ?>
        </p>
        <div class="col-lg-12">
                <table class="table" style="width:400px">
                    <thead >
                    <tr style="background-color:#e8503a;color: white;">
                        <th style="text-align: center">Descripción</th>
                        <th style="text-align: center">No. de Personas</th>
                    </tr>
                    </thead>
                    <tbody align="center">
                    <?php
                    $cont=0;
                    $ban=true;
                    $totalinscritos=0;
                    $color="#fff";
                    $totalFacturable=0;
                    foreach ($inscritosTipo as $key=>$detalle) {
                        $totalinscritos+=$detalle['inscritos'];
                        if($detalle['facturable']=='SI') $totalFacturable+=$detalle['inscritos'];
                    }
                    ?>
                    <?php
                    $ban=true;
                    foreach ($inscritosTipo as $key=>$detalle) {
                        $cont++;
                        $mod=0;
                        $mod = $cont % 2 == 0;
                        $color =  $mod == 1 ? "#eee" : "#fff";

                        ?>
                        <tr style="background-color:<?= $color ?>">
                            <td align="left" >
                                <span style="text-align: center"><?= $detalle['estados'] ?></span>
                            </td>
                            <td align="right" >
                                <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['inscritos'],0)?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="left"   style="border-bottom: 1px solid #ddd">
                            <span style="text-align: center">TOTAL PAGANDO</span>
                        </td>
                        <td align="right"   style="border-bottom: 1px solid #ddd">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalFacturable,0)?></span>
                        </td>
                    </tr>
                    <tr>
                        <td align="left"   style="border-bottom: 1px solid #ddd">
                            <span style="text-align: center">TOTAL</span>
                        </td>
                        <td align="right"   style="border-bottom: 1px solid #ddd">
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($totalinscritos,0)?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>
    <div><p style="font-weight: bold;"></div> 