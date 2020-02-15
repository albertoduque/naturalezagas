<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Aire */
/* @var $form yii\widgets\ActiveForm */

?>




    <div style="text-align: 'right'; position:fixed; padding-left:20px; margin-top:7.15cm;  ">
        <table class="table" style="padding-top:0px;">
            <tr>
                <td style="width: 400px;">
            <div style="text-align: 'left'; position:'relative'; left:110px; padding-top:0px;  line-height:15px;">
                    <br><strong><?=$totalLetras?></strong>
            </div>
                    </td>
                     <td style="padding-top:13px;">
                <table class="table" style="padding-top:0px;">

                <tr>
                    <td align="right" style="width: '110px';"></td>
                    <td align="right" class="border" ><span style="float: 'left'; font-size: '14px';"><?=$simbolo?> </span><span style="float: 'right'; font-size: '14px';"><?=Yii::$app->formatter->asDecimal($factura[0]['subtotal'],0) ?></span><br><br> </td>
                </tr>
                <tr>
                    <td align="right" ></td>
                    <td align="right" class="border" ><span style="float: 'left'; font-size: '14px';"><?=$simbolo?> </span><span style="float: 'right'; font-size: '14px';"><?=Yii::$app->formatter->asDecimal($factura[0]['iva'],0) ?></span><br><br></td>
                </tr>
                <tr>
                    <td align="right" ></td>
                    <td align="right" class="border" ><span style="float: 'left'; font-size: 16px;"><?=$simbolo?> </span><span style="float: 'right'; font-size: '16px';"><?= Yii::$app->formatter->asDecimal($factura[0]['total'],0)   ?></span></td>
                </tr>
                </table>
                </td>
                    </tr>
                     </table>
       
    </div>		

