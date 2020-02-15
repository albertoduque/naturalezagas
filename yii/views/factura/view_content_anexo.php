<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Aire */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="content">

    <div style="text-align: 'right'; position:'absolute'; padding-left:120px; padding-top:60px;" > 
            <?=$factura[0]['fecha']. "-" .$contacto['ciudad']?>				
    </div>
   <!--Espaciado 1 linea  35 espaciado caracter-->
    <div style="text-align: 'right'; position:'absolute'; padding-left:75px; padding-top:10px;  ">
            <?=$contacto['identificacion_empresa'] != 0 ? $contacto['empresa'] : $contacto['persona']?> 
    </div>
    <div style="text-align: 'right'; position:'absolute'; padding-left:45px; padding-top:10px;">
            <?php $ext = $contacto['telefono_extension'] ? " ext ".$contacto['telefono_extension'] : ''; ?>
            <?=$contacto['identificacion_empresa'] != 0 ? $contacto['identificacion_empresa'] : $contacto['identificacion_persona']?>  <?= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". $contacto['telefono'].$ext?>
    </div>	
    
    	
    <div style="font-size: '12px'; text-align: 'right'; position:'absolute'; padding-left:95px; padding-top:10px;">
            <?=$contacto['direccion']?>
    </div>		
    <div style="font-size: '12px'; text-align: 'right'; position:'absolute'; padding-left:665px; padding-top:25px;">
            <?=$factura[0]['numero']?>
    </div>				
    <div style="position:'absolute'; left:'60px'; padding-top:45px;">		
        <table class="table" style="border-collapse: collapse;">
            <tbody align="center">
                <tr>
                     <td align="left"  colspan="3" style="padding-left: 20px;padding-right: '10px'; height: '11px';max-width: 120px;">
                        <strong><?=$factura[0]['producto'] ?> </strong>
                    </td>
                </tr>
                <?php foreach ($factura as $key=>$detalle) { ?>
                <tr>
                    <td align="left"  colspan="3" style="padding-left: 20px;padding-right: '10px'; max-width: 120px;border-collapse: collapse;line-height: 1px;font-size: 12px;">
                        <strong><?=$detalle['persona']?> </strong>
                    </td>
                    <td align="right"  colspan="2" style="padding-left: 12px;border-collapse: collapse;line-height: 1px;font-size: 12px;">						
                            <span style="float: 'left';"><?=$simbolo?></span> 						
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valorTotal'],0)?></span> 
                    </td>					
                </tr>
              
                <?php } ?>	
                <tr>
                    <td align="left"  colspan="3" style="padding-left: 20px;padding-right: '10px'; height: '20px';max-width: 120px;">
                        <strong><?=$factura[0]['observaciones'] ? $factura[0]['observaciones'] : ''?> </strong>
                    </td>
                </tr>
                <?php for ($i=0;$i<count($factura['productos']);$i++) {?>
                    <tr>
                        <td class="border" style="padding: '12px'; height: '47px';" colspan="4"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

   
   
</div>
