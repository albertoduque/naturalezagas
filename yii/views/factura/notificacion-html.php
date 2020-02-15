<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
$totalinscritos=0;
?>
<head>
<link rel="stylesheet" type="text/css" href="../css/tool.css">
</head>
<center>    
    <table width="800" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="800" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#8aae44">
                        <tbody>
                            <tr>
                                <td width="459" rowspan="2" valign="top">
                                    <img src="http://www.naturgas.com.co/assets/img/logo.png" width="80" height="80" style="height:80%;width:80%" alt="Logo Claro" class="CToWUd">
                                </td>
                                <td width="290" height="40" align="center" valign="top" class="m_-6758057911451488687Estilo2" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px;color:#ffffff;background-color:#8aae44;vertical-align:bottom;text-align:right">
                                   Estadisticas Generadas
                                </td>
                                <td width="51" rowspan="2">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td height="20" align="center" valign="top" class="m_-6758057911451488687Estilo4" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:13px;color:#ec2329;font-weight:normal;background-color:#8aae44;text-align:right">
                                   <?= date("m.d.y")?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
            <td width="51" rowspan="2">
                &nbsp;
            </td>
            <tr>
                <td width="51" rowspan="2">
                    &nbsp;
                </td>
            <tr>
                <td width="51" rowspan="2">
                    &nbsp;
                </td>
            <tr>
            <tr>
                <td height="300" valign="top" align="center">
                    <table style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px;width:500px;border: 1px solid">
                <thead style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px;color:#ffffff;background-color:#8aae44;">
                <th style="text-align: center;border: 1px solid">Descripcion</th>
                <th style="text-align: center;border: 1px solid">Valor</th>
                </thead>
                <tbody align="center">
                    <?php 
                            $ban=true;
                            foreach ($modelEstadisticasCount as $key=>$detalle) { 
                                $totalinscritos+=$detalle['inscritos'];
                                if($detalle['idEstados']==3) 
                                    $ban=false; 
                    ?>
                    <tr>    
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">						
                            <span style="text-align: center"><?= $detalle['estados'] ?></span> 
                        </td>
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">					
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['inscritos'],0)?></span> 
                        </td>
                    </tr>
                    <?php } ?>	
                    <?php if($ban) { ?>
                    <tr>    
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">						
                            <span style="text-align: center"><?= 'FACTURADO TOTAL' ?></span> 
                        </td>
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">					
                            <span style="float: 'right'"><?=0?></span> 
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>    
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">						
                            <span style="text-align: center"><?= 'TOTAL INSCRITOS' ?></span> 
                        </td>
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">					
                            <span style="float: 'right'"><?=$totalinscritos?></span> 
                        </td>
                    </tr>
                     <?php 
                            $ban=true;
                            foreach ($modelEstadisticasSum as $key=>$detalle) { 
                                if($detalle['id']==3) 
                                    $ban=false; 
                    ?>
                    <tr>    
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">						
                            <span style="text-align: center"><?= $detalle['estados'] ?></span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';border: 1px solid">					
                            <span style="float: 'right'"><?=Yii::$app->formatter->asDecimal($detalle['valor'],0)?></span> 
                        </td>
                    </tr>
                    <?php } ?>	
                    <?php if($ban) { ?>
                    <tr>    
                        <td align="center"   style="padding: '12px'; height: '51px';border: 1px solid">						
                            <span style="text-align: center"><?= 'FACTURADO TOTAL' ?></span> 
                        </td>
                        <td align="right"   style="padding: '12px'; height: '51px';border: 1px solid">					
                            <span style="float: 'right'"><?=0?></span> 
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
                </td>
            </tr>
            
            <tr>
                <td height="127" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E3E6EB">
                        <tbody><tr>
                            <td width="800" height="66" valign="top" class="m_-6758057911451488687Estilo8" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:18px;color:#000000;text-align:center;vertical-align:bottom">
                                Este correo fue generado por el sistema de Eventos
                            </td>
                        </tr>
                        <tr>
                            <td height="61" align="center" valign="top" class="m_-6758057911451488687Estilo9" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:18px;color:#cc0000;text-align:center;vertical-align:top;font-weight:bold">
                                Cualquier duda comunicarse con naturgas@naturgas.com.co
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
            <tr>
                <td height="137" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#8aae44">
                        <tbody><tr>
                            <td width="70" rowspan="4">
                            </td>
                            <td width="61" height="18">
                            </td>
                            <td width="529">
                            </td>
                            <td width="107">
                            </td>
                            <td width="33">
                            </td>
                        </tr>
                        <tr>
                            <td height="45" valign="middle">
                                &nbsp;
                            </td>
                            <td valign="middle">
                                <span class="m_-6758057911451488687Estilo5" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:13px;color:#ffffff">
                                    Cll 72 # 10 -70 <br>
                                    Torre A Of 705<br>
                                    Bogotá - Colombia </span>
                                    <br> <span class="m_-6758057911451488687Estilo5" style="font-family:Geneva,Arial,Helvetica,sans-serif;font-size:13px;color:#ffffff">
                                    Visitanos  </span> <a href="http://naturgas" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=es&amp;q=http://claro.com.co&amp;source=gmail&amp;ust=1484172714331000&amp;usg=AFQjCNFvzA4EdSiAlD40B5njtCeGGRqu6Q">naturgas.com.co</a> </span>
                            </td>
                            <td rowspan="2" valign="middle">
                                 <img src="http://www.naturgas.com.co/assets/img/logo.png" width="80" height="80" style="height:80%;width:80%" alt="Logo Claro" class="CToWUd">
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td height="57" colspan="2" valign="bottom">
                               </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td height="17">
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
        </tbody>
    </table>
</center>

