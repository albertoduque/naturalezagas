<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FacturasSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .tab .nav-tabs{
    border-bottom:0 none;
    background: #569073;
    border-radius: 0 20px 0 20px;
}
.tab .nav-tabs li a{
    background: transparent;
    border-radius: 0;
    font-size: 16px;
    border: none;
    color: #fff;
    padding: 12px 22px;
}
.tab .nav-tabs li a i{
    margin-right:10px;
    color:#fff;
}
.tab .nav-tabs li:first-child a{
    border-bottom-left-radius:20px;
}
.tab .nav-tabs li.active a,
.tab .nav-tabs li.active a i{
    border: 0 none;
    background:#3c763d;
    color:#fff;
}
.tab .nav-tabs li.active a:after{
    content: "";
    position: absolute;
    left: 45%;
    bottom: -14px;
    border: 7px solid transparent;
    border-top: 7px solid #3c763d;
}
.tab .tab-content{
    padding:12px;
    color:#5a5c5d;
    font-size: 14px;
    line-height:24px;
    margin-top: 25px;
    border-bottom:3px solid #3c763d;
}
@media only screen and (max-width: 480px) {
    .tab .nav-tabs,
    .tab .nav-tabs li{
        width:100%;
        background:transparent;
    }
    .tab .nav-tabs li.active a{
        border-radius:10px 10px 0 0;
    }
    .tab .nav-tabs li:first-child a{
        border-bottom-left-radius:0;
    }
    .tab .nav-tabs li a{
        margin-bottom:10px;
        border:1px solid lightgray;
        background: #569073;
    }
    .tab .nav-tabs li.active a:after{
        border:none;
    }
}


.agenda h2,
.componentes h2,
.informacion div h2,
.participacion h2, .agenda__lista span {
  color: #569073;
  font-family: 'Oswald', sans-serif;
  font-weight: 900;
  text-align: center;
  text-transform: uppercase;
}
.agenda {
  display: block;
  z-index: 1;
}
.agenda p {
  display: block;
  color:  #5d5d5d;
  font-size: 15px;
  font-style: italic;
  margin: 0;
  padding: 4px 0;
  text-align: left;
}
.agenda a:hover,.conferencistas--head a:hover,.conferencistas__lista--desc ul li .desarrollo a:hover, .somb a:hover {
  background: #0c343f;
}

.agenda .agenda__in {
  border-bottom: 1px solid #ccc;
  border-left: 1px solid #ccc;
  border-right: 1px solid #ccc;
}

.agenda .agenda__in ul {
  background: #ecedef;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: no-wrap;
      -ms-flex-wrap: no-wrap;
          flex-wrap: no-wrap;
  list-style: none;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
      -ms-flex-pack: center;
          justify-content: center;
  margin: 10px 0;
  padding: 0px;
  text-align: center;
}

.agenda .agenda__in ul li {
  border-right: 4px solid #fff;
  color: #349abb;
  display: inline-block;
  font-family: 'Oswald', sans-serif;
  font-size: 20px;
  /* font-weight: 500; */
  line-height: 20px;
  /* padding: 10px; */
  text-align: center;
  vertical-align: middle;
  width: 34%;
  -webkit-transition: 0.4s all;
  transition: 0.4s all;
}

.agenda .agenda__in ul li:last-child {
  border: none;
}
.agenda .agenda__in ul li span {
  border-top: 1px solid #349abb;
  display: block;
  font-size: 16px;
  margin: 3px auto;
  font-weight: 300;
  padding: 0 10px;
  width: 125px;
}
.agenda .agenda__in .activo {
  background: #349abb;
  color: #fff;
  position: relative;
  -webkit-transition: 0.4s all;
  transition: 0.4s all;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
}
.agenda .agenda__in .activo:after {
  background: url("../img/agenda-activa.png") center top no-repeat;
  bottom: -20px;
  content: "";
  display: block;
  height: 20px;
  left: 0;
  position: absolute;
  width: 100%;
}
.agenda .agenda__in .activo span {
  border-top: 1px solid #fff;
}
.agenda .agenda__in .agenda__in--contenido ul {
  background: #fff;
  display: block;
  width: 100%;
}
.agenda .agenda__in .agenda__in--contenido ul li {
  color: #5d5d5d;
  display: table;
  font-family: 'Roboto', sans-serif;
  font-weight: 300;
  margin: 0;
      padding-bottom: 15px;
  padding: 0;
  
  width: 100%;
  -webkit-transition: 0.4s all;
          transition: 0.4s all;
}
.agenda .agenda__in .agenda__in--contenido ul li:hover {
  background: #f7f7f7;
  -webkit-transition: 0.4s all;
          transition: 0.4s all;
}
.agenda .agenda__in .agenda__in--contenido ul li div {
  display: table-cell;
}
.agenda .agenda__in .agenda__in--contenido ul li span {
  border: none;
  display: inline;
  padding: 0;
  width: auto;
}
.agenda .agenda__in .agenda__in--contenido ul li .left {
  background: url("../img/asset-in-agenda.png") top right no-repeat;
  width: 30%;
}
.agenda .agenda__in .agenda__in--contenido ul li .left span {
  color: #569073;
}
.agenda .agenda__in .agenda__in--contenido ul li .right {
  padding: 0 30px 10px 30px;
  text-align: left;
}
.agenda .agenda__in .agenda__in--contenido ul li .right h3 {
  font-family: 'Oswald', sans-serif;
  font-weight: 300;
  font-size: 23px;
  line-height: 25px;
  margin: 0;
  text-align: left;
}
.agenda .agenda__in .agenda__in--contenido ul li .right p {
  font-size: 16px;
  font-style: italic;
  font-weight: 300;
}
.agenda .agenda__in .agenda__in--contenido ul li .right .lugar {
  font-style: italic;
  font-weight: 300;
  font-size: 16px;
}
.agenda .agenda__in .agenda__in--contenido ul li .right .lugar span {
  color: #569073;
  font-size: 16px;
  text-transform: capitalize;
  padding-right: 0.2em;
}
.agenda__desarrollo ul{
  margin-top:30px !important;
  padding:0px !important;
}
.agenda__desarrollo ul li{
  border-bottom:1px solid #CCC;
  border-left:0px !important;
  display: table;
  padding: 0px !important;
  width: 100%;
}
.agenda__desarrollo ul li div{
  display: inline-block;
  vertical-align: middle;
}
.agenda__desarrollo ul li .fecha{
  display: table-cell;
  font-size: 28px;
  padding-right:10px;
  line-height:30px;
  text-align: center;
  width: 20%;
}
.agenda__desarrollo ul li .color0{
  border-right:5px solid #569073;
  color: #569073;
}
.agenda__desarrollo ul li .color1{
  border-right:5px solid #0c343f;
  color: #0c343f;
}
.agenda__desarrollo ul li .color2{
  border-right:5px solid #349abb;
  color: #349abb;
}

.agenda__desarrollo ul li .desarrollo{
  padding:0 0 0 20px;
  width: 90%
}
.agenda__desarrollo ul li .desarrollo h3{
  font-size: 22px;
  padding: 0px;
  line-height: 22px;
  letter-spacing: -1px;
  text-align: left !important;
  background: none !important;
  margin: 18px 0 0px !important;
  text-transform: none;
}
.agenda__desarrollo ul li .desarrollo p{
  font-weight: 400;
  line-height: 16px;
  margin: 0px !important;
  text-align: left !important;
  width: 100% !important;
}
.agenda__desarrollo ul li .desarrollo .hora{
  display: block;
  font-size:13px;
  font-family: 'Roboto condensse', sans-serif;
  margin: 5px 0px;
}
.agenda__desarrollo ul li .desarrollo .lugar{
  display: block;
  font-weight: bold;
  padding: 8px 0 18px;
  text-transform: uppercase;
}
.agenda__lista span{
  color:#5d5d5d;
  display: block;
  margin:20px auto;
}
.agenda__lista .agenda__lista--nav ul {
  box-sizing:border-box;
  display: block;
  padding: 0px;
  text-align: center;
}
.agenda__lista .agenda__lista--nav ul li {
  border-left:none !important;
  box-sizing:border-box;
  display: inline-block;
  list-style: none;
  padding: 10px;
  vertical-align: top;
  width: 23%;
}
.agenda__lista .agenda__lista--nav ul li img{
  width: 100%;
}
.agenda__selectores{
  text-align: center;
}
.agenda__selectores select{
  border: none;
  color: #666666;
  font-size: 17px !important;
  margin: 0 1%;
  box-sizing:border-box;
  display: inline-block;
  vertical-align: top;
  width: 20%;
}
.agenda__selectores select{
  background: #E0E0E0;
  color:#666;
  padding:10px;
  font-size: 20px;
}
.agenda__selectores h4{
  box-sizing:border-box;
  display: inline-block;
  color: #BC3932;
  font-size: 26px;
  line-height: 24px;
  padding: 0px 10px;
  margin: 0px !important;
  text-align: right;
  text-transform: none;
  vertical-align: top;
  width: 25%;
}

.agenda__selectores .event {
  border-bottom: 1px solid rgba(160, 160, 160, 0.2);
  padding-bottom: 15px;
  margin-bottom: 20px;
  position: relative;
}

.agenda__selectores .event:last-of-type {
  padding-bottom: 0;
  margin-bottom: 0;
  border: none;
}

.agenda__selectores .event:before,
.agenda__selectores .event:after {
  position: absolute;
  display: block;
  top: 0;
}

.agenda__selectores .event:before {
  left: -177.5px;
  color: #212121;
  content: attr(data-date);
  text-align: right;
  /*  font-weight: 100;*/
  
  font-size: 16px;
  min-width: 120px;
}

.agenda__selectores .event:after {
  box-shadow: 0 0 0 8px #42A5F5;
  left: -30px;
  background: #212121;
  border-radius: 50%;
  height: 11px;
  width: 11px;
  content: "";
  top: 5px;
}

@media screen and (max-width: 740px) {
  #scrollbar1 .overview{
    padding:10px 0;
  }
  .agenda .agenda__in .agenda__in--contenido ul li div{
    text-align: left
  }
  .descktop{
    display: none;
  }
  .footer .footer__in .menu{
    display: none;
  }
  .footer .footer__in .footer__in--accionistas li{
    display: block;
    margin:20px 0;
    width: 100%;
  }
  .footer .footer__in .footer__in--accionistas li:last-child{
    border: none;
  }
  .header__bottom--accesos{
    float:none;
    width:100%;
  }
  .header .header__bottom .header__bottom--in .header__bottom--accesos ul{
    display: flex;
    flex-wrap:no-wrap;
    padding: 0px;
  }
  .header .header__bottom .header__bottom--in .header__bottom--accesos ul li{
    width: 50%;
  }
  .header .header__bottom .header__bottom--in .header__bottom--logo{
    float:none;
    margin:10px auto;
    width: 90%;
  }
  .header .header__top .header__top--left{
    align-items:stretch;
    display:flex;
    float:none;
    flex-wrap:no-wrap;
    font-size:12px;
    width:100%;
  }
  .header .header__top .header__top--left a{
    border:none;
    padding:0px;
  }
  .header__top--right{
    display: none;
  }
  .informacion .informacion__in{
    display: block;
  }
  .informacion .informacion__in div{
    display: block;
    margin:10px auto;
    width: 90%;
  }
  .informacion .informacion__in div:last-child{
    border-left:none;
    border-top:2px dotted #ccc;
    margin-top:40px;
  }
  .men-movil{
    border-right:1px solid white !important;
    display: block !important;
    margin-right: 10px;
    padding:0 10px !important;
  }
  .participacion ul li p{
    width: 65%;
  }
  .plataforma{
    display:none;
  }
}
@media screen and (max-width: 500px){
  #scrollbar1 .overview li{
    flex-wrap:wrap;
    padding: 10px 10px 20px 10px!important;
    border-bottom:1px dotted #ccc;
    position: relative;
  }
  .agenda .agenda__in .agenda__in--contenido ul li .left{
    order:2;
  }
  .agenda .agenda__in .agenda__in--contenido ul li .right{
    order:1;
  }
  .agenda .agenda__in .agenda__in--contenido ul li .left{
    background: none;
    width: 50%;
    position: absolute ;
    bottom:0px;
  }
  .agenda .agenda__in .agenda__in--contenido ul li div{
    display: block;
  }
  .contenido__interno h1, .contenido__interno h2{
    text-align: center;
  }
  .participacion ul li{
    width: 100%;
  }
  .participacion ul li p{
    width: 79%;
  }
  .logos .logos__in .small {width: 50%;}
  .logos .logos__in .big, .logos .logos__in .line{
    width: 100%;
  }
  .logos .logos__in .big img, .logos .logos__in .line img{
    max-width:250px;
  }
  .logos .logos__in .line a{
    width: 100% !important;
  }
}

</style>


<section class="agenda centrar">
    <h2>Agenda académica</h2>
              <div class="col-md-12">
            <div class="tab" role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-calendar"></i>Miercoles 22 Marz</a></li>
                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-calendar"></i>Jueves 23 Marz</a></li>
                    <li role="presentation"><a href="#Section3" aria-controls="messages" role="tab" data-toggle="tab"><i class="fa fa-calendar"></i>Viernes 24 Marz</a></li>
                </ul>
                    <div class="tab-content">
                         <section class="agenda__in">
                       <div role="tabpanel" class="tab-pane fade in active" id="Section1">
                            <div class="row"></div>
          
            <div class="agenda__in--contenido">
              <div id="scrollbar1">
                <div class="scrollbar">
                  <div class="track">
                    <div class="thumb">
                      <div class="end"></div>
                    </div>
                  </div>
                </div> 
                <div class="viewport">
                    <ul class="overview">
                        <li class="day1 event" style="display: table;" data-date="2010/2012">
                           
                            <div class="left"><span>Hora</span>10:00 AM</div>
                          <div class="right">
                            <h3><strong>Perspectivas macroeconómicas de Colombia y del sector de hidrocarburos</strong></h3>
                            <p>Conferencia Magistral<br>
                            <strong>Santiago Castro.</strong> Presidente Asobancaria – Presidente Consejo Gremial Nacional.!</p>
                            <div class="lugar"><span>Lugar</span>Salón principal - Pab. 11 al 14</div>
                          </div>
                        </li>
                        <li class="day1" style="display: table;">
                            <div class="left"><span>Hora</span>10:00 AM</div>
                          <div class="right">
                            <h3><strong>Perspectivas macroeconómicas de Colombia y del sector de hidrocarburos</strong></h3>
                            <p>Conferencia Magistral<br>
                            <strong>Santiago Castro.</strong> Presidente Asobancaria – Presidente Consejo Gremial Nacional.!</p>
                            <div class="lugar"><span>Lugar</span>Salón principal - Pab. 11 al 14</div>
                          </div>
                        </li>
                        <li class="day2" style="display: none;">
                          <div class="left"><span>Hora</span>7:30 AM</div>
                          <div class="right">
                            <h3><strong>Registro Agenda Académica y entrega de credenciales</strong></h3>
                            <p>Registro Agenda Académica y entrega de credenciales!</p>
                            <div class="lugar"><span>Lugar</span>Punto de Registro Pabellón 10</div>
                          </div>
                        </li>
                    </ul>
                </div> 
              </div>
            </div>
                            </div>
                         <div role="tabpanel" class="tab-pane fade" id="Section2">
                            <div class="row">
                                
                            </div>
                        </div>
                              </section>
                    </div>
                </div>
            </div>
</section>