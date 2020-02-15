//var urlGlobal='http://eventos.naturgas.com.co/';
var urlGlobal='http://eventosfase2.naturgas.com.co/';
//urlGlobal='/natura/web/';
 var clearFormInscritos = function (){
        $('#personas-tipo_documento').val("");
        $('#personas-identificacion').val("");
        $('#personas-nombre').val("");
        $('#personas-apellido').val("");
        $('#personas-telefono').val("");
        $('#personas-movil').val("");
        $('#personas-direccion').val("");
        $('#personas-pais').val("");
        $('#personas-id_ciudad').val("");
        $('#personas-email').val("");
        $('#personas-id_cargo').val(39);
        $('#personas-id_tipo_asistente').val(19);
        $('#personas-estado').val("");
    }
    
var verificarnit = function (e,id){
    if(id==1)
        $('#empresa-inscripcion-form-id').submit();
    else if(id==2)
        $('#contacto-inscripcion-form-id').submit();
}

var salir = function (){
    window.location.href=urlGlobal+'inscripcion/index-menu';
}
$body = $("body");

$(document).on({
            ajaxStart: function() { $body.addClass("loading");    },
            ajaxStop: function() { $body.removeClass("loading"); }    
    });
    
$(document.body).on('beforeSubmit', '#empresa-form-id', function() {
     var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    var actual_url = window.location.href ;
    if(form.find('.has-error').length) {
        $.alert({
            icon: 'glyphicon glyphicon-info-sign',
            title: 'Advertencia',
            theme: 'material',
            content: 'Error debe llenar todos los campos',
            confirmButtonClass: 'btn-info',
            confirmButton: 'Aceptar',

        });
        return false;
    }
     $.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Registro Exitoso',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                    window.location.href = urlGlobal+"empresa";
                   // btn-empresa 
                }
                else if(option.respuesta==0)
                {
                        $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'Error debe llenar todos los campos___',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',

                        });
                }

            });
        });
        return false;
});  
$(document.body).on('beforeSubmit', '#empresa-inscripcion-form-id', function() {  
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    var actual_url = window.location.href ;
    if(form.find('.has-error').length) {
        $.alert({
            icon: 'glyphicon glyphicon-info-sign',
            title: 'Advertencia',
            theme: 'material',
            content: 'Error debe llenar todos los campos',
            confirmButtonClass: 'btn-info',
            confirmButton: 'Aceptar',

        });
        return false;
    }
     $.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).scrollTop(0);
                    $('#inscripciones-id_empresa').val(option.empresa_id);
                    var ruta =$('#modal-personas-inscripciones').val();
                    var ids = ruta.split('?');
                    ids[0] += '?id_empresa='+option.empresa_id;
                    $('#modal-personas-inscripciones').val(ids[0]);
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Registro Exitoso',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                    
                    $( ".btn-next" ).trigger( "click" );
                    $( ".btn-empresa" ).hide();
                    $( ".btn-contacto" ).show();
                    var action_url2 =  urlGlobal+"inscripcion/inscripcion-empresa";
                    action_url2 += "/0/"+option.empresa_id;
                    $.pjax.reload({container:'#contactos-data-grid', url:action_url2, replace:false,timeout : 2000});
                    form.trigger("reset");
                   // btn-empresa 
                }
               
                else if(option.respuesta==0)
                {
                    var errorrut=0;
                    var empresa=option.idEmpresa;
                    $.each(option.data,function(index,option) {
                        if(index=="empresas-identificacion")
                        {
                            $.confirm({
                                    icon: 'glyphicon glyphicon-info-sign',
                                    title: 'Advertencia',
                                    theme: 'material',
                                    confirmButtonClass: 'btn-success',
                                    cancelButtonClass: 'btn-danger',
                                    confirmButton: 'Aceptar',
                                    cancelButton: 'Cancelar',
                                    content: 'Empresa Existente Desea Ingresar Personas?',
                                confirm: function(){
                                    window.location.href = urlGlobal+"inscripcion/inscripcion-empresa-persona?idEmpresa="+empresa;
                                },
                                cancel: function(){
                                    $("#empresas-identificacion").val(' ');   
                                    $("#escenario-manejo").val(' ');  
                                    $("#empresas-identificacion").closest(".form-group").addClass("is-focused");
                                }
                            });
                            errorrut=1;
                        }
                        $("#"+index).closest(".form-group").removeClass("has-error");
                        $("#"+index).parent().find(".help-block").text(""); 
                        $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#"+index).closest(".form-group").addClass("has-error");
                       console.log("index",index,option);
                    });
                    if(!errorrut)
                    {
                        $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'Error debe llenar todos los campos___',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',

                        });
                    }
                }

            });
        });
        return false;
});

$(document.body).on('change', '#facturas-fecha', function() {
    var serie=$("#facturas-serie").val();
    var date=$("#facturas-fecha").val();
    var tipoDocumento = $("#facturas-tipodocumento").val();
    console.log("tipoDocumento",tipoDocumento);
    var numbers=$("#facturas-numero").val();
    $.ajax({
        method: "POST",
        url: urlGlobal+"factura/validate-date-invoice",
        data: {serie:serie,date:date,number:numbers}
    }).done(function(result){
        if(result==0)
        {
            $.alert({
                icon: 'glyphicon glyphicon-info-sign',
                title: 'Advertencia',
                theme: 'material',
                content: 'Fecha debe ser mayor a la ultima factura',
                confirmButtonClass: 'btn-info',
                confirmButton: 'Aceptar',
            });
            $("#facturas-fecha").val('');
        }
    });
});
$(document.body).on('beforeSubmit', '#debit-form-id', function() {
    console.log("form..dete");
    var form = $(this);
    var idEmpresa=$('#inscripciones-id_empresa').val();
    var tipoFactura = 'NC';
    var form_data = form.serialize();
    form_data += "&Contactos[id_empresa]=" + encodeURIComponent(idEmpresa);
    form_data += "&Facturas[tipo_factura]=" + encodeURIComponent(tipoFactura);
    var action_url = form.attr("action");
    /* if(form.find('.has-error').length) {
         return false;
     }*/
    $.ajax({
        method: "POST",
        url: action_url,
        data: form_data
    }).done(function(result){
        console.log("result",result);
        $.each(result,function(index,option) {
            if(option.respuesta==1)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'Registro Exitoso',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',

                });
                window.location.href = option.redirect;
            }
            else if(option.respuesta==0)
            {   console.log("Error");
               $.each(option.error,function(index,option) {
                    index = index === 'observacion' ? 'descripcion' : index;
                    $("#detallefactura-"+index).closest(".form-group").removeClass("has-error");
                    $("#detallefactura-"+index).parent().find(".help-block").text("");
                    $("#detallefactura-"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#detallefactura-"+index).closest(".form-group").addClass("has-error");
                });
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'Error debe llenar todos los campos' + option.data,
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',

                });
            }
            else if(option.respuesta==2)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'No se realizo la transmisión de la Nota de Debito',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });
                setTimeout(function(){window.location.href = option.redirect;}, 2000);
            }
			else if(option.respuesta==3)
            {
                $.alert({
					icon: 'glyphicon glyphicon-info-sign',
					title: option.descripcionProceso,
					theme: 'material',
					content: option.listaMensajesProceso,
					confirmButtonClass: 'btn-info',
					confirmButton: 'Aceptar',
					confirm: function(){
						window.location.href=option.redirect;
					},
				});
				//setTimeout(function(){window.location.href = option.redirect;}, 2000);
            }

        });
    });
    return false;

});

$(document.body).on('beforeSubmit', '#nc-form-id', function() { 
    console.log("form..dete");
    var form = $(this);
    var idEmpresa=$('#inscripciones-id_empresa').val();
    var tipoFactura = 'NC';
    var form_data = form.serialize();
    form_data += "&Contactos[id_empresa]=" + encodeURIComponent(idEmpresa);
    form_data += "&Facturas[tipo_factura]=" + encodeURIComponent(tipoFactura);
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        return false;
    }
     $.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Registro Exitoso',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                    window.location.href = option.redirect;
                }
                else if(option.respuesta==0)
                {   console.log("Error");
                   $.each(option.data,function(index,option) {
                        $("#"+index).closest(".form-group").removeClass("has-error");
                        $("#"+index).parent().find(".help-block").text(""); 
                        $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#"+index).closest(".form-group").addClass("has-error");
                       
                    });
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error debe llenar todos los campos ' + option.data,
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                }
                else if(option.respuesta==3)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error su Nota de credito supera el monto de la factura = ' + option.data,
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                }
                else if(option.respuesta==2)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'No se realizo la transmisión de la Nota de Credito',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                    setTimeout(function(){window.location.href = option.redirect;}, 2000);
                }
				else if(option.respuesta==4)
				{
					$.alert({
						icon: 'glyphicon glyphicon-info-sign',
						title: option.descripcionProceso,
						theme: 'material',
						content: option.listaMensajesProceso,
						confirmButtonClass: 'btn-info',
						confirmButton: 'Aceptar',
						confirm: function(){
							window.location.href=option.redirect;
						},
					});
					//setTimeout(function(){window.location.href = option.redirect;}, 2000);
				}

            });
        });
        return false;

}); 

function validateInvoice(){
    console.log("acac");
    var serie=$("#facturas-serie").val();
    var date=$("#facturas-fecha").val();
    var numbers=$("#facturas-numero").val();
    $.ajax({
        method: "POST",
        url: urlGlobal+"factura/validate-date-invoice",
        data: {serie:serie,date:date,number:numbers}
    }).done(function(result){
        if(result==0)
        {
            $.alert({
                icon: 'glyphicon glyphicon-info-sign',
                title: 'Advertencia',
                theme: 'material',
                content: 'Fecha debe ser mayor a la ultima factura',
                confirmButtonClass: 'btn-info',
                confirmButton: 'Aceptar',
            });
            $("#facturas-fecha").val('');
        }
        return response = result == 1 ? true : false;
    });
}

$(document.body).on('beforeSubmit', '#factura-form-id', function() { 

        console.log("form..dete");
        var form = $(this);
        var idEmpresa=$('#inscripciones-id_empresa').val();
        var tipoFactura = 'FA';
        var form_data = form.serialize();
        form_data += "&Contactos[id_empresa]=" + encodeURIComponent(idEmpresa);
        form_data += "&Facturas[tipo_factura]=" + encodeURIComponent(tipoFactura);
        var action_url = form.attr("action");


        if(form.find('.has-error').length) {
            return false;
        }


    var serie=$("#facturas-serie").val();
    var date=$("#facturas-fecha").val();
    var numbers=$("#facturas-numero").val();
    $.ajax({
        method: "POST",
        url: urlGlobal+"factura/validate-date-invoice",
        data: {serie:serie,date:date,number:numbers}
    }).done(function(result){
        if(result==0)
        {
            $.alert({
                icon: 'glyphicon glyphicon-info-sign',
                title: 'Advertencia',
                theme: 'material',
                content: 'Fecha debe ser mayor a la ultima factura',
                confirmButtonClass: 'btn-info',
                confirmButton: 'Aceptar',
            });
            $("#facturas-fecha").val('');
        }
        else
        {
            $.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
                $.each(result,function(index,option) {
                    if(option.respuesta==1)
                    {
                        $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'Registro Exitoso',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',

                        });
                        window.location.href = option.redirect;
                    }
                    else if(option.respuesta==0)
                    {   console.log("Error");
                        $.each(option.data,function(index,option) {
                            $("#"+index).closest(".form-group").removeClass("has-error");
                            $("#"+index).parent().find(".help-block").text("");
                            $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                            $("#"+index).closest(".form-group").addClass("has-error");

                        });
                        $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'Error debe llenar todos los campos',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',

                        });
                    }
                    else if(option.respuesta==2)
                    {
                        $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'No se realizo la transmisión de la factura',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',
                            confirm: function(){
                                window.location.href=option.redirect;
                            },
                        });
                    }
					else if(option.respuesta==3){
						$.alert({
							icon: 'glyphicon glyphicon-info-sign',
							title: option.descripcionProceso,
							theme: 'material',
							content: option.listaMensajesProceso,
							confirmButtonClass: 'btn-info',
							confirmButton: 'Aceptar',
							confirm: function(){
                                window.location.href=option.redirect;
                            },
						});
						//setTimeout(function(){window.location.href = option.redirect;}, 2000);
					}

                });
            }).fail(function() {
               $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'No se recibio respuesta verificar en Transmisión de Facturas',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',
                             confirm: function(){
                                window.location.href=urlGlobal+"factura/facturados";
                            },
                        });
            });
        }
        return response = result == 1 ? true : false;
    });



    return false;

});


$(document.body).on('beforeSubmit', '#creditmult-form-id', function() {
    console.log("form..dete");
    var form = $(this);
    var idEmpresa=$('#inscripciones-id_empresa').val();
    var tipoFactura = 'NC';
    var form_data = form.serialize();
    form_data += "&Contactos[id_empresa]=" + encodeURIComponent(idEmpresa);
    form_data += "&Facturas[tipo_factura]=" + encodeURIComponent(tipoFactura);
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        return false;
    }
    $.ajax({
        method: "POST",
        url: action_url,
        data: form_data
    }).done(function(result){
        console.log("result",result);
        $.each(result,function(index,option) {
            if(option.respuesta==1)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'Registro Exitoso',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',

                });
                window.location.href = option.redirect;
            }
            else if(option.respuesta==0)
            {   console.log("Error");
                $.each(option.data,function(index,option) {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text("");
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");

                });
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'Error debe llenar todos los campos',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',

                });
            }
            else if(option.respuesta==2)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'No se realizo la transmisión de la factura',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });
                setTimeout(function(){window.location.href = option.redirect;}, 2000);
            }
			else if(option.respuesta==3)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Error',
                    theme: 'material',
                    content: 'No se realizo la transmisión',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });
            }
			else if(option.respuesta==4)
            {
                $.alert({
					icon: 'glyphicon glyphicon-info-sign',
					title: option.descripcionProceso,
					theme: 'material',
					content: option.listaMensajesProceso,
					confirmButtonClass: 'btn-info',
					confirmButton: 'Aceptar',
					confirm: function(){
						window.location.href=option.redirect;
					},
				});
				//setTimeout(function(){window.location.href = option.redirect;}, 2000);
            }

        });
    });
    return false;

});

$(document.body).on('beforeSubmit', '#contacto-inscripcion-form-id', function() { 
//$("#contacto-inscripcion-form-id" ).submit(function( event ) {
    //event.stopImmediatePropagation();
    //event.preventDefault();
    var form = $(this);
    var idEmpresa=$('#inscripciones-id_empresa').val();
    var form_data = form.serialize();
    form_data += "&Contactos[id_empresa]=" + encodeURIComponent(idEmpresa); 
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        return false;
    }
     $.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Registro Exitoso',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                    $( ".btn-next" ).trigger( "click" );
                    $( ".btn-contacto" ).hide();
                }
                else if(option.respuesta==0)
                {   console.log("Error");
                   $.each(option.data,function(index,option) {
                        $("#"+index).closest(".form-group").removeClass("has-error");
                        $("#"+index).parent().find(".help-block").text(""); 
                        $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#"+index).closest(".form-group").addClass("has-error");
                       
                    });
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error debe llenar todos los campos',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                }

            });
        });
        return false;
});  

$(document.body).on("afterValidate",'#person-form-id', function (event, messages) {
    var form = $(this);
    if(form.find('.has-error').length) {
        $.alert({
            icon: 'glyphicon glyphicon-info-sign',
            title: 'Advertencia',
            theme: 'material',
            content: 'Debe llenar los datos obligatorios',
            confirmButtonClass: 'btn-info',
            confirmButton: 'Aceptar',
        });
    }
    
}); 

function exitInscritos(){
    clearFormInscritos();
    $(document).find('#modal-inicial').modal('hide');
}

$(document.body).on('beforeSubmit', '#person-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var drops=form.data('id');
    var form_data = form.serialize();
    var action_url = form.attr("action");

   
    var action_url2 = urlGlobal+'inscripcion/inscripcion-empresa';
    if(form.find('.has-error').length) {
        console.log("person");
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    var idEmpresa=$('#inscripciones-id_empresa').val();
                    var idVisible = $('#inscripciones-visibles').val();
                    if(idEmpresa)
                    {
                        action_url2 += "/0/"+idEmpresa;
                        if(idVisible!="1" || idVisible==undefined)
                            $(document).find('#modal-inicial').modal('hide');
                        if($("#personas-data-grid").is(":visible"))
                        {
                            $.alert({
                                icon: 'glyphicon glyphicon-info-sign',
                                title: 'Advertencia',
                                theme: 'material',
                                content: 'Inscrito Guardado con exito',
                                confirmButtonClass: 'btn-info',
                                confirmButton: 'Aceptar',
                            }); 
                           clearFormInscritos();
                            $.pjax.reload({container:'#personas-data-grid', url:action_url2, replace:false});
                        }
                        form.trigger("reset");
                    }
                    else
                    {
                         $.alert({
                            icon: 'glyphicon glyphicon-info-sign',
                            title: 'Advertencia',
                            theme: 'material',
                            content: 'Inscrito Guardado con exito',
                            confirmButtonClass: 'btn-info',
                            confirmButton: 'Aceptar',
                        });
                       clearFormInscritos();
                        //window.location.href = "/register/basic/web/inscripcion";
                    }
                }
                else if(option.respuesta==0)
                {
                    $.each(option.data,function(index,option) {
                        $("#"+index).closest(".form-group").removeClass("has-error");
                        $("#"+index).parent().find(".help-block").text(""); 
                        $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#"+index).closest(".form-group").addClass("has-error");
                       console.log("index",index,option);
                    });
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar Inscrito',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                       
                }
                else if(option.respuesta==3)
                {
                    $.each(option.data,function(index,option) {
                        $("#"+index).closest(".form-group").removeClass("has-error");
                        $("#"+index).parent().find(".help-block").text("");
                        $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#"+index).closest(".form-group").addClass("has-error");
                        console.log("index",index,option);
                    });
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Persona ya Inscrita',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });

                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
});


$(document.body).on('beforeSubmit', '#event-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var drops=form.data('id');
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        console.log("person");
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $.alert({
                       icon: 'glyphicon glyphicon-info-sign',
                       title: 'Advertencia',
                       theme: 'material',
                       content: 'Evento Guardado con exito',
                       confirmButtonClass: 'btn-info',
                       confirmButton: 'Aceptar',
                   });
                    var action_url2 = urlGlobal+'evento/index/';
                    window.location.href = action_url2;
                }
                else if(option.respuesta==0)
                {
                    $.each(option.error,function(index,option) {
                        $("#eventos-"+index).closest(".form-group").removeClass("has-success");
                        $("#eventos-"+index).parent().find(".help-block").text(""); 
                        $("#eventos-"+index).after("<div class=\"help-block\">"+option+"</div>");
                        $("#eventos-"+index).closest(".form-group").addClass("has-error is-focused");
                    });
                    var error = option.error['nombre'] ? option.error['nombre'] : '';
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar Evento '+error,
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                       
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
});
$(document.body).on('beforeSubmit', '#descripcion-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var drops=form.data('id');
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        console.log("person");
        return false;
    }
    $.ajax({
        type: "POST",
        url: action_url,
        data: form_data
    }).done(function(result){
        $.each(result,function(index,option) {
            if(option.respuesta==1)
            {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'DescripciÃ³n Guardada con exito',
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });
                var action_url2 = urlGlobal+'descripcion-producto/index/';
                window.location.href = action_url2;
            }
            else if(option.respuesta==0)
            {
                $.each(option.error,function(index,option) {
                    $("#descripcion-producto-"+index).closest(".form-group").removeClass("has-success");
                    $("#descripcion-producto-"+index).parent().find(".help-block").text("");
                    $("#descripcion-producto-"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#descripcion-producto-"+index).closest(".form-group").addClass("has-error is-focused");
                });
                var error = option.error['nombre'] ? option.error['nombre'] : '';
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: 'Error al guardar DescripciÃ³n '+error,
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });

            }
            else
            {
                $("#"+index).closest(".form-group").removeClass("has-error");
                $("#"+index).parent().find(".help-block").text("");
                $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                $("#"+index).closest(".form-group").addClass("has-error");
            }
        });
    });
    return false;
});
$(document.body).on('beforeSubmit', '#recibo-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).find('#modal-inicial').modal('hide');
                    $.pjax.reload({container:'#recibos-grid'});
                    form.trigger("reset");
                }
                else if(option.respuesta==0)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
});

$(document.body).on('beforeSubmit', '#notas-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    
    if(form.find('.has-error').length) {
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).find('#modal-inicial').modal('hide');
                    var actual_url = urlGlobal+'inscripcion';
                    $.pjax.reload({container:'#inscripciones-personas-grid', url:actual_url, replace:false});
                    
                    form.trigger("reset");
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Cambio Realizado con Exito',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else if(option.respuesta==0)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
}); 
$(document.body).on('beforeSubmit', '#cambiar-participante-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).find('#modal-inicial').modal('hide');
                    $.pjax.reload({container:'#inscripciones-personas-grid'});
                    form.trigger("reset");
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Cambio Realizado con Exito',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else if(option.respuesta==0)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
}); 

$(document.body).on('beforeSubmit', '#forma-pago-form', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var form_data = form.serialize();
                var action_url = form.attr("action");
                
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                      $.each(result,function(index,option) {
                        if(option.respuesta==1)
                        {
                            form.trigger("reset");
                        }
                        else
                        {
                            $("#"+index).closest(".form-group").removeClass("has-success");
                            $("#"+index).closest(".form-group").removeClass("has-error");
                            $("#"+index).parent().find(".help-block").text(""); 
                            $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                            $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            $.alert({
                                icon: 'glyphicon glyphicon-info-sign',
                                title: 'Advertencia',
                                theme: 'material',
                                content: 'Error al guardar Forma de Pago',
                                confirmButtonClass: 'btn-info',
                                confirmButton: 'Aceptar'
                            });
                        }
                        });
                    });
                    return false;
        });

$(document.body).on('beforeSubmit', '#tipo-asistentes-form', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var form_data = form.serialize();
                var action_url = form.attr("action");
                
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                      $.each(result,function(index,option) {
                        if(option.respuesta==1)
                        {
                            form.trigger("reset");
                        }
                        else
                        {
                            $("#"+index).closest(".form-group").removeClass("has-success");
                            $("#"+index).closest(".form-group").removeClass("has-error");
                            $("#"+index).parent().find(".help-block").text(""); 
                            $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                            $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            $.alert({
                                icon: 'glyphicon glyphicon-info-sign',
                                title: 'Advertencia',
                                theme: 'material',
                                content: 'Error al guardar Tipo Asistente',
                                confirmButtonClass: 'btn-info',
                                confirmButton: 'Aceptar'
                            });
                        }
                        });
                    });
                    return false;
        });

$(document.body).on('beforeSubmit', '#cargo-form-id', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var form_data = form.serialize();
                var action_url = form.attr("action");
                
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                      $.each(result,function(index,option) {
                        if(option.respuesta==1)
                        {
                            $(document).find('#modal-alterno').modal('hide')
                            if(option.types=="personas")
                            {
                              var actual_url = urlGlobal+'persona/create/1';
                               //var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#persona-dropDownList', url:actual_url, replace:false});
                            }
                            if(option.types=="contactos")
                            {
                                var actual_url = urlGlobal+'contacto/create';
                                //var actual_url = '/contacto/create';
                                $.pjax.reload({container:'#cargo-dropDownList', url:actual_url, replace:false});
                            }
                            
                            $(document).on('pjax:complete', function() {
                                var names = '#'+option.types+'-id_cargo';
                                $('#'+option.types+'-id_cargo').val(option.id);
                            })
                            form.trigger("reset");
                        }
                        else
                        {
                            $("#"+index).closest(".form-group").removeClass("has-success");
                            $("#"+index).closest(".form-group").removeClass("has-error");
                            $("#"+index).parent().find(".help-block").text(""); 
                            $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                            $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            $.alert({
                                icon: 'glyphicon glyphicon-info-sign',
                                title: 'Advertencia',
                                theme: 'material',
                                content: 'Error al guardar Cargo',
                                confirmButtonClass: 'btn-info',
                                confirmButton: 'Aceptar'
                            });
                        }
                        });
                    });
                    return false;
        });
    
$(document.body).on('beforeSubmit', '#contact-form-id', function() {   // valido el formulario de caractersitica en ajax
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        console.log("person");
        return false;
    }
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).find('#modal-inicial').modal('hide');
                    $.pjax.reload({container:'#contacto-grid'});
                    form.trigger("reset");
                }
                else if(option.respuesta==0)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
});


$(document.body).on('click', '.deleteCrud',function(e){
        e.preventDefault();
        var url=$(this).data('url');
        var id= $(this).attr('id');
        var actual_url = window.location.href ;
        console.log("actual",actual_url);
        $.confirm({
            icon: 'glyphicon glyphicon-info-sign',
            title: 'Advertencia',
            theme: 'material',
            confirmButtonClass: 'btn-success',
            cancelButtonClass: 'btn-danger',
            confirmButton: 'Aceptar',
            cancelButton: 'Cancelar',
            content: $('#'+id).data( "content" ),
            confirm: function(){
                $.get(
                    url,
                    function (respuesta) {
                        console.log(respuesta);
                        $.each(respuesta,function(index,option) {
                            
                            if(option.respuesta)
                            {
                                 if(option.action==1)
                                 {
                                     $.pjax.reload({container:option.reload, url:actual_url, replace:false});
                                 }
                                 else if(option.action==2)
                                 {
                                     window.location.href =option.reload;
                                 }
                                 else if(option.action==3)
                                 {
                                     window.location.href = actual_url;
                                 }
                            }
                           
                            $.alert({
                                icon: 'glyphicon glyphicon-info-sign',
                                title: 'Advertencia',
                                theme: 'material',
                                content: option.respuesta ? option.msgSuccess : option.msgError,
                                confirmButtonClass: 'btn-info',
                                confirmButton: 'Aceptar',
                            });
                        });
                    }
                );
            }
        }); 
    });   
    
$(document.body).on('click', '#modal-modalButton,#modal-personas-inscripciones',function(){
        var form=$(this);
        $("#modal-inicial").modal('show').find('#modalContent').load(form.attr('value'));
        $("#modal-inicial").modal('show').find('#modalHeader').text(form.attr('data-title'));

});

$(document.body).on('click', '#modal-modalht',function(){
    var form=$(this);  
    $.get(
        form.data('value'),
        function (data) {
            //$("#modal-inicial").modal('show').find('#modalContent').load(form.attr('data-value'));
            //$("#modal-inicial").modal('show').find('#modalHeader').text(form.attr('data-title'));

            $("#modal-inicial").modal('show').find('#modalHeader').html(form.data('title'));
            $("#modal-inicial").modal('show').find('#modalContent').html(data);
        }
    );
        
});

$(document.body).on('click', '#modal-modalAlterno',function(){
        var form=$(this);
        $("#modal-alterno").modal('show').find('#modalContent').load(form.attr('value'));
        $("#modal-alterno").modal('show').find('#modalHeader').text(form.attr('data-title'));

});
    
$(document.body).on('beforeSubmit', '#pais-form', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var drops=form.data('id');
                var form_data = form.serialize();
                var action_url = form.attr("action");
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                        console.log("respuesta",result);
                        $.each(result,function(index,option) {
                            if(option.types=="personas")
                            {
                                $(document).find('#modal-alterno').modal('hide');
                                var actual_url = urlGlobal+'persona/create/1';
                                //var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#personas-pais-dropDownList', url:actual_url, replace:false});
                                 $(document).on('pjax:complete', function() {
                                    $('#personas-pais').val(option.id);
                                });
                            }
                            else if(option.types=="contacto")
                            {
                                $(document).find('#modal-alterno').modal('hide');
                                var actual_url = urlGlobal+'persona/create/1';
                                //var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#contactos-pais-dropDownList'});
                                 $(document).on('pjax:complete', function() {
                                    $('#contactos-pais').val(option.id);
                                });
                            }
                            else if(option.respuesta==1)
                            { 
                                $(document).find('#modal-inicial').modal('hide');
                                $.pjax.reload({container:'#'+drops+'-pais-dropDownList'});
                                $(document).on('pjax:complete', function() {
                                    $('#'+drops+'-pais').val(option.id);
                                    $('#'+drops+'-ciudad_id').empty();
                                    var listItems= "";
                                    listItems+= "<option value=>CIUDAD</option>";
                                    $('#'+drops+'-ciudad_id').html(listItems);
                                });
                                form.trigger("reset");
                            }
                            else if(option.respuesta==2)
                            {
                                var modal=$('#modal').is(':visible');
                                if(!modal)
                                {
                                  window.location.href = urlGlobal+option.url.substring(1);
                                }
                                if(modal)
                                {
                                    $(document).find('#modal').modal('hide');
                                    $.pjax.reload({container:'#'+drops+'-pais-dropDownList'});
                                    $(document).on('pjax:complete', function() {
                                        $('#'+drops+'-pais').val(option.id);
                                    });
                                }
                                form.trigger("reset");
                            }
                            else if(option.respuesta==0)
                            {
                                $.alert({
                                    icon: 'glyphicon glyphicon-info-sign',
                                    title: 'Advertencia',
                                    theme: 'material',
                                    content: 'Error al guardar pais',
                                    confirmButtonClass: 'btn-info',
                                    confirmButton: 'Aceptar',
                                });
                            }
                            else
                            {
                                $("#"+index).closest(".form-group").removeClass("has-success");
                                $("#"+index).closest(".form-group").removeClass("has-error");
                                $("#"+index).parent().find(".help-block").text(""); 
                                $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                                $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            }
                        });
                    });
                    return false;
        });
        
        
    $(document.body).on('change', '#eventos-copyevent', function() { 
            $(this).val()==1 ? $(".copyEventIdClass").fadeIn() : $(".copyEventIdClass").hide();
   });
$(document.body).on('beforeSubmit', '#ciudad-form', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var drops=form.data('id');
                var form_data = form.serialize();
                var action_url = form.attr("action");
                var actual_url = window.location.href ;
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                        $.each(result,function(index,option) {
                            if(option.types=="personas")
                            {
                                var listItems= "";
                                listItems+= "<option value>Seleccione</option>";
                                $.each(option.lista,function(index,option) {
                                    listItems+= "<option value='" + index + "'>" + option + "</option>";
                                });
                                
                                if(listItems)
                                    $('#personas-id_ciudad').html(listItems);
                                $(document).find('#modal-alterno').modal('hide');
                                var actual_url = urlGlobal+'persona/create/1';
                                //var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#persona-ciudad-dropDownList', url:actual_url, replace:false});
                                $(document).on('pjax:complete', function() {//toca recorrer loop de option 
                                    $('#personas-id_ciudad').val(option.id);
                                    $('#personas-pais').val(option.pais);
                                })
                                form.trigger("reset");
                            }
                            else if(option.types=="empresa")
                            {
                                $(document).find('#modal-inicial').modal('hide');
                                var actual_url = urlGlobal+'inscripcion/inscripcion-empresa';
                                //var actual_url = "/inscripcion/inscripcion-empresa";
                                actual_url += "?&pais="+option.pais;
                                console.log(actual_url);
                                //var actual_url = '/register/basic/web/persona/create/1';
                              // var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#ciudad-dropDownList', url:actual_url, replace:false});
                                $(document).on('pjax:complete', function() {
                                    $('#empresas-id_ciudad').val(option.id);
                                })
                            }
							else if(option.types=="ciudad")
                            {
                                //$(document).find('#modal-inicial').modal('hide');
                                var actual_url = urlGlobal;
                                //var actual_url = "/inscripcion/inscripcion-empresa";
                                //actual_url += "?&pais="+option.pais;
                                //console.log(actual_url);
                                //var actual_url = '/register/basic/web/persona/create/1';
                              // var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#departamento-dropDownList', url:actual_url, replace:false});
                                $(document).on('pjax:complete', function() {
                                    $('#ciudad-id_padre').val(option.id);
                                })
                            }
                            else if(option.types=="contacto")
                            {
                                var listItems= "";
                                listItems+= "<option value>Seleccione</option>";
                                $.each(option.lista,function(index,option) {
                                    listItems+= "<option value='" + index + "'>" + option + "</option>";
                                });
                                
                                if(listItems)
                                    $('#contactos-id_ciudad').html(listItems);
                                $(document).find('#modal-alterno').modal('hide');
                                //var actual_url = "/inscripcion/inscripcion-empresa";
                                var actual_url = urlGlobal+'inscripcion/inscripcion-empresa';
                                actual_url += "?&pais="+option.pais;
                                $.pjax.reload({container:'#ciudad-dropDownList'});
                                $(document).on('pjax:complete', function() {
                                    $('#contactos-id_ciudad').val(option.id);
                                    $('#contactos-pais').val(option.pais);
                                });
                            }
                            else if(option.respuesta==1)
                            {
                                if(drops==0) //Empresas
                                {
                                    if($('#ciudad-dropDownList').val()==undefined)
                                    {
                                         window.location.href =option.url;
                                         console.log(option.url);
                                    }
                                    else
                                    {
                                        $(document).find('#modal-inicial').modal('hide');
                                    
                                        actual_url += "?&pais="+option.pais;
                                        console.log(actual_url);
                                        $.pjax.reload({container:'#ciudad-dropDownList', url:actual_url, replace:false});
                                        $(document).on('pjax:complete', function() {
                                            $('#empresas-id_ciudad').val(option.id);
                                        })
                                        form.trigger("reset");
                                    }
                                }
                                else if(drops==1) //Personas
                                {
                                    if($('#persona-ciudad-dropDownList').val()==undefined)
                                    {
                                         window.location.href =option.url;
                                         console.log(option.url);
                                    }
                                    else
                                    {
                                        $(document).find('#modal-inicial').modal('hide');
                                    
                                        actual_url += "?&pais="+option.pais;
                                        console.log(actual_url);
                                        $.pjax.reload({container:'#persona-ciudad-dropDownList', url:actual_url, replace:false});
                                        $(document).on('pjax:complete', function() {
                                            $('#personas-id_ciudad').val(option.id);
                                        })
                                        form.trigger("reset");
                                    }
                                }
                            }
                            else if(option.respuesta==2)
                            {
                                var modal=$('#modal').is(':visible');
                                if(!modal)
                                {
                                  window.location.href = urlGlobal+option.url.substring(1);
                                }
                            }
                            else
                            {
                                $("#"+index).closest(".form-group").removeClass("has-success");
                                $("#"+index).closest(".form-group").removeClass("has-error");
                                $("#"+index).parent().find(".help-block").text(""); 
                                $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                                $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            }
                        });
                    });
                    return false;
        });    
		
$(document.body).on('beforeSubmit', '#departamento-form', function() {   // valido el formulario de caractersitica en ajax
                var form = $(this);
                var drops=form.data('id');
                var form_data = form.serialize();
                var action_url = form.attr("action");
                var actual_url = window.location.href ;
                if(form.find('.has-error').length) {
                        return false;
                }
                $.ajax({
                        type: "POST",
                        url: action_url,
                        data: form_data
                    }).done(function(result){
                        $.each(result,function(index,option) {
                            if(option.types=="personas")
                            {
                                var listItems= "";
                                listItems+= "<option value>Seleccione</option>";
                                $.each(option.lista,function(index,option) {
                                    listItems+= "<option value='" + index + "'>" + option + "</option>";
                                });
                                
                                if(listItems)
                                    $('#personas-id_departamento').html(listItems);
                                $(document).find('#modal-alterno').modal('hide');
                                var actual_url = urlGlobal+'persona/create/1';
                                //var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#persona-departamento-dropDownList', url:actual_url, replace:false});
                                $(document).on('pjax:complete', function() {//toca recorrer loop de option 
                                    $('#personas-id_departamento').val(option.id);
                                    $('#personas-pais').val(option.pais);
                                })
                                form.trigger("reset");
                            }
                            else if(option.types=="empresa")
                            {
                                $(document).find('#modal-inicial').modal('hide');
                                var actual_url = urlGlobal+'inscripcion/inscripcion-empresa';
                                //var actual_url = "/inscripcion/inscripcion-empresa";
                                actual_url += "?&pais="+option.pais;
                                console.log(actual_url);
                                //var actual_url = '/register/basic/web/persona/create/1';
                              // var actual_url = '/persona/create/1';
                                $.pjax.reload({container:'#departamento-dropDownList', url:actual_url, replace:false});
                                $(document).on('pjax:complete', function() {
                                    $('#empresas-id_departamento').val(option.id);
                                })
                            }
                             else if(option.types=="contacto")
                            {
                                var listItems= "";
                                listItems+= "<option value>Seleccione</option>";
                                $.each(option.lista,function(index,option) {
                                    listItems+= "<option value='" + index + "'>" + option + "</option>";
                                });
                                
                                if(listItems)
                                    $('#contactos-id_departamento').html(listItems);
                                $(document).find('#modal-alterno').modal('hide');
                                //var actual_url = "/inscripcion/inscripcion-empresa";
                                var actual_url = urlGlobal+'inscripcion/inscripcion-empresa';
                                actual_url += "?&pais="+option.pais;
                                $.pjax.reload({container:'#departamento-dropDownList'});
                                $(document).on('pjax:complete', function() {
                                    $('#contactos-id_departamento').val(option.id);
                                    $('#contactos-pais').val(option.pais);
                                });
                            }
                            else if(option.respuesta==1)
                            {
                                if(drops==0) //Empresas
                                {
                                    if($('#departamento-dropDownList').val()==undefined)
                                    {
                                         window.location.href =option.url;
                                         console.log(option.url);
                                    }
                                    else
                                    {
                                        $(document).find('#modal-inicial').modal('hide');
                                    
                                        actual_url += "?&pais="+option.pais;
                                        console.log(actual_url);
                                        $.pjax.reload({container:'#departamento-dropDownList', url:actual_url, replace:false});
                                        $(document).on('pjax:complete', function() {
                                            $('#empresas-id_departamento').val(option.id);
                                        })
                                        form.trigger("reset");
                                    }
                                }
                                else if(drops==1) //Personas
                                {
                                    if($('#persona-departamento-dropDownList').val()==undefined)
                                    {
                                         window.location.href =option.url;
                                         console.log(option.url);
                                    }
                                    else
                                    {
                                        $(document).find('#modal-inicial').modal('hide');
                                    
                                        actual_url += "?&pais="+option.pais;
                                        console.log(actual_url);
                                        $.pjax.reload({container:'#persona-departamento-dropDownList', url:actual_url, replace:false});
                                        $(document).on('pjax:complete', function() {
                                            $('#personas-id_departamento').val(option.id);
                                        })
                                        form.trigger("reset");
                                    }
                                }
                            }
                            else if(option.respuesta==2)
                            {
                                var modal=$('#modal').is(':visible');
                                if(!modal)
                                {
                                  window.location.href = urlGlobal+option.url.substring(1);
                                }
                            }
                            else
                            {
                                $("#"+index).closest(".form-group").removeClass("has-success");
                                $("#"+index).closest(".form-group").removeClass("has-error");
                                $("#"+index).parent().find(".help-block").text(""); 
                                $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                                $("#"+index).closest(".form-group").addClass("has-error is-focused");
                            }
                        });
                    });
                    return false;
        });    

$(document.body).on('change',"#facturas-id_moneda", function(){
    $(this).val()!=1 ? $('.trm-hidden').fadeIn('10') : $('.trm-hidden').hide();
    //$('#facturas-trm').numeric(false);
    $('#facturas-trm').number( true, 2 );
    $('#facturas-trm').val('');     
});

$(document.body).on('change',"#empresas-id_tipo_identificacion", function(){
    $('#empresas-identificacion').val('');
    $('#escenario-manejo').val('');
    let dataString = {id:$(this).val() }
    let url = urlGlobal+'tipo-identificacion/get-ischeck'
    $.ajax({
          type: "GET",
          data: dataString,
          url: url,
          dataType: "json",
          success: function(response){
              if(response.respuesta == 1 && response.data == 1){
                $('#empresas-identificacion').numeric(false);
                $('#empresas-identificacion').on( "keypress" , runScript);
                $('#empresas-identificacion').on( "focusout" , focusOutNit);
                $('#empresas-identificacion').attr('maxlength',11);
              }
              if(response.respuesta == 1 && response.data != 1){
                $('#empresas-identificacion').numeric(true);
               // $('#empresas-identificacion').off( "keypress" );
                $('#empresas-identificacion').on( "keypress" , runScript);
                $('#empresas-identificacion').attr('maxlength',20);
                $(document.body).off('focusout',"#empresas-identificacion");
                //$('#empresas-identificacion').off( "focusout" , focusOutNit);
                $('#empresas-identificacion').on( "focusout" , {type: 1}, focusOutNit);
              }
          }
    });
});


$(document.body).on('change',"#personas-id_tipo_identificacion", function(){
    $('#personas-identificacion').val('');
    $('#escenario-manejo').val('');
    let dataString = {id:$(this).val() }
    let url = urlGlobal+'tipo-identificacion/get-ischeck'
    $.ajax({
          type: "GET",
          data: dataString,
          url: url,
          dataType: "json",
          success: function(response){
              if(response.respuesta == 1 && response.data == 1){
                $('#personas-identificacion').numeric(false);
                $('#personas-identificacion').on( "focusout" , validarPersona);
                $('#personas-identificacion').attr('maxlength',11);
              }
              if(response.respuesta == 1 && response.data != 1){
                $('#personas-identificacion').numeric(true);
                $('#personas-identificacion').attr('maxlength',20);
                $(document.body).off('focusout',"#personas-identificacion");
                $('#personas-identificacion').on( "focusout" , validarPersona);
              }
          }
    });
});


$(document.body).on('change',"#personas-pais", function(){
	if($(this).val() != 1){
		$(this).attr("data-preview","personas-id_ciudad");
		$(this).attr("data-url","/ciudad/to-list-ciudad");
		$("#personas-id_padre").prop("disabled", true);
		$("#personas-id_ciudad").val("");
		$("#personas-id_ciudad").html("<option value>Seleccione</option>");
		$("#personas-id_padre").val("");
		$("#personas-id_padre").html("<option value>Seleccione</option>");
	}
	else{
		$(this).attr("data-preview","personas-id_padre");
		$(this).attr("data-url","/departamento/to-list-departamento");
		$("#personas-id_padre").prop("disabled", false);
		$("#personas-id_padre").html("<option value>Seleccione</option>");
	}
});

$(document.body).on('change',"#empresas-pais", function(){
	if($(this).val() != 1){
		$(this).attr("data-preview","empresas-id_ciudad");
		$(this).attr("data-url","/ciudad/to-list-ciudad");
		$("#empresas-id_padre").prop("disabled", true);
		$("#empresas-id_ciudad").val("");
		$("#empresas-id_ciudad").html("<option value>Seleccione</option>");
		$("#empresas-id_padre").val("");
		$("#empresas-id_padre").html("<option value>Seleccione</option>");
	}
	else{
		$(this).attr("data-preview","empresas-id_padre");
		$(this).attr("data-url","/departamento/to-list-departamento");
		$("#empresas-id_padre").prop("disabled", false);
		$("#empresas-id_padre").html("<option value>Seleccione</option>");
	}
});

$(document.body).on('change',"#contactos-pais", function(){
	if($(this).val() != 1){
		$(this).attr("data-preview","contactos-id_ciudad");
		$(this).attr("data-url","/ciudad/to-list-ciudad");
		$("#contactos-id_padre").prop("disabled", true);
		$("#contactos-id_ciudad").val("");
		$("#contactos-id_ciudad").html("<option value>Seleccione</option>");
		$("#contactos-id_padre").val("");
		$("#contactos-id_padre").html("<option value>Seleccione</option>");
	}
	else{
		$(this).attr("data-preview","contactos-id_padre");
		$(this).attr("data-url","/departamento/to-list-departamento");
		$("#contactos-id_padre").prop("disabled", false);
		$("#contactos-id_padre").html("<option value>Seleccione</option>");
	}
});

$(document.body).on('change',"#facturas-tipo_compra", function(){
	switch($(this).val()){
		case 2:
		case '2':
			$(this).attr("aria-required","true");
			$(this).attr("aria-invalid","true");
			$(".field-facturas-periodo_pago").addClass("required");
			$("#facturas-periodo_pago").prop("disabled", false);
			
			$(".field-facturas-fecha_vencimiento").addClass("required");
			$("#facturas-fecha_vencimiento").prop("disabled", false);
			break;
		case 1:
		default:
			$(this).attr("aria-required","false");
			$(this).removeAttr("aria-required");
			$(".field-facturas-periodo_pago").removeClass("required");
			$(".field-facturas-periodo_pago").removeClass("has-error");
			$(".field-facturas-periodo_pago").removeClass("has-success");
			$("#facturas-periodo_pago").prop("disabled", true);
			$("#facturas-periodo_pago").val("");
			
			$(".field-facturas-fecha_vencimiento").removeClass("required");
			$(".field-facturas-fecha_vencimiento").removeClass("has-error");
			$(".field-facturas-fecha_vencimiento").removeClass("has-success");
			$("#facturas-fecha_vencimiento").prop("disabled", true);
			$("#facturas-fecha_vencimiento").val("");
			break;
	}
});



//Función para colocar la fecha de Vencimiento según Periodo de Pago Seleccionado
$(document.body).on('change',"#facturas-periodo_pago", function(){
	var dias = parseInt($(this).val());
	var fecha = formatdate($("#facturas-fecha").val());
	var date  = new Date(fecha);
	date.setDate(date.getDate() + dias);
	
	var dd = date.getDate();
    var mm = date.getMonth() + 1;
	mm = (mm < 10) ? "0"+mm : mm;
	dd = (dd < 10) ? "0"+dd : dd;
	var y = date.getFullYear();

    var someFormattedDate = dd + '/' + mm + '/' + y;
	
	$("#facturas-fecha_vencimiento").val(someFormattedDate);
});


$(document.body).on('change',"#facturas-fecha_vencimiento", function(){
	var fecha = formatdate($("#facturas-fecha").val());
	var fecha_vencimiento = formatdate($(this).val());
	console.log("est");
	//alert(fecha);
	//alert(fecha_vencimiento);
	
	var i = 86400000; //86400000 = Un día
	var x = (fecha_vencimiento - fecha);
	var y = x / i;
	//alert(y);
	
	if(fecha_vencimiento < fecha){
		$(this).val("");
		$(this).focus();
		$.alert({
			icon: 'glyphicon glyphicon-info-sign',
			title: 'Advertencia',
			theme: 'material',
			content: 'La fecha de vencimiento no debe ser menor a la fecha de la factura',
			confirmButtonClass: 'btn-info',
			confirmButton: 'Aceptar',
		});
	}
	
	
	//Selecciona el periodo de pago correspondiente al numero de días 
	var existe = false; 
	$('#facturas-periodo_pago option').each(function(){
        if ($(this).val() == y){
			$('#facturas-periodo_pago').val(y);
			existe = true;
        }
    });
	
	//Si periodo de pago no existe lo inserta en el combo
	if(existe == false){
		$('#facturas-periodo_pago').append(new Option(y, y, true, true));
	}
});



function formatdate(date){
	var parts = date.split("/");
	var date = new Date(parts[1] + "/" + parts[0] + "/" + parts[2]);
	return date.getTime();
}




$(document.body).on('change',".dropDownList", function(){
            var drop = $(this);
            var idAnterior=drop.val();
            var data = drop.attr("data-set");
            var url = drop.attr("data-url");
            var dataString=data+"="+idAnterior;
            var siguiente = drop.attr("data-preview");
                $.ajax({
                      type: "POST",
                      data: dataString,
                      url: url,
                      dataType: "json",
                      success: function(respuesta){
                          var listItems= "";
                         listItems+= "<option value>Seleccione</option>";
                          $.each(respuesta,function(index,option) {
                             console.log(index+"  "+option);
                             listItems+= "<option value='" + index + "'>" + option + "</option>";
                           });
                           console.log(siguiente);
                           if(listItems)
                            $('#'+siguiente).html(listItems);
                      },
                        error:function(xhr,err){
                              if(xhr.status==200)
                              {
                                      $.alert({
                                          icon: 'glyphicon glyphicon-info-sign',
                                          title: 'Advertencia',
                                          theme: 'material',
                                          content: 'Error al realizar calculo',
                                          confirmButtonClass: 'btn-info',
                                          confirmButton: 'Aceptar',

                                      });
                              }
                         }
                });
        });
    
$(document).ready(function(){
    $('#empresas-identificacion');
    $('#empresas-telefono').numeric(false);
    $('#empresas-telefono_extension').numeric(false);
    $('#empresas-movil').numeric(false);
    $('#contactos-telefono').numeric(false);
    $('#contactos-telefono_extension').numeric(false);
    $('#contactos-movil').numeric(false);
    $('#personas-telefono').numeric(false);
    $('#personas-telefono_extension').numeric(false);
    $('#personas-movil').numeric(false);
    $('#facturas-porcentajeiva').numeric(false);
    $('#detallerecibos-valor').number( true, 2 );
    $("#facturas-subtotal").maskMoney({thousands:',', precision:'0'});
    $("#facturas-iva").maskMoney({thousands:',', precision:'0'});
    $("#facturas-total").maskMoney({thousands:',', precision:'0'});
    
    $(".close-alert").click(function(e){
        $(this).parent().remove();
        e.preventDefault();
    });
    $(".btn-finish").click(function(){
        window.location.href = urlGlobal+"inscripcion";
    });
    $(".btn-exit").click(function(){
        window.location.href = urlGlobal+"congreso/inscripcion-empresa";
    });
        
    $(document.body).on('click','.id_Desde', function(e){
        e.preventDefault();
        var s= $(this).datepicker({ 
            inline: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            format: 'dd/mm/yyyy',
            showOn: "both" }).focus().on('changeDate', function(ev){
                s.datepicker('hide');
            });
    });
    
   $(document.body).on('beforeSubmit', '#update-status-form', function() {  
        console.log("person");// valido el formulario de caractersitica en ajax
    var form = $(this);
    var form_data = form.serialize();
    var action_url = form.attr("action");
    if(form.find('.has-error').length) {
        console.log("person");
        return false;
    }
    console.log("enter");
    $.ajax({
            type: "POST",
            url: action_url,
            data: form_data
        }).done(function(result){
             console.log("person");
            $.each(result,function(index,option) {
                if(option.respuesta==1)
                {
                    $(document).find('#modal-inicial').modal('hide');
                    $.pjax.reload({container:'#recibos-grid'});
                    form.trigger("reset");
                }
                else if(option.respuesta==0)
                {
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Error al guardar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',
                    });
                }
                else
                {
                    $("#"+index).closest(".form-group").removeClass("has-error");
                    $("#"+index).parent().find(".help-block").text(""); 
                    $("#"+index).after("<div class=\"help-block\">"+option+"</div>");
                    $("#"+index).closest(".form-group").addClass("has-error");
                }
            });
        });
        return false;
});
     
    function loadSelectPatrocinios(listProducto,listInscripcion){
        console.log("lop");
        var dropdown=$(this);
        var id = 1;
        var dataString="idInscripcion="+id+"&listInscripcion="+JSON.stringify(listInscripcion)+"&listProducto="+JSON.stringify(listProducto);
        var listItems= "";
            
        $.ajax({
                  type: "POST",
                  data: dataString,
                  //url: "/factura/dropdown-lista",
                  url: "factura/dropdown-lista",
                  dataType: "json",
                }).done(function(respuesta){
                    var ranid=Math.random();
                    listItems+= "<option value>Seleccione</option>";
                    $.each(respuesta,function(index,option) {
                        listItems+= "<option value='" + index + "'>" + option + "</option>";
                    });
                    var newRow = $("<tr>");
                    var cols = "";
                    var selec = '<select  class="form-control" name="DetalleFactura[id_inscripcion]" aria-required="true" onchange="javascript:getProducto(this)">'+listItems+'</select>';
                        cols += '<td class="col-sm-7"><div class="form-group field-detallefactura-valortotal required is-empty">'+selec+'<div class="help-block"></div></div></td>';
                        cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-valortotal" class="form-control" name="DetalleFactura[cantidad]" aria-required="true"><input type="hidden" id="detallefactura-producto" class="form-control" name="DetalleFactura[producto]" aria-required="true"><div class="help-block"></div></div></td>';
                        cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-subtotal"'+ranid+' class="form-control is-number" name="DetalleFactura[subtotal]" aria-required="true"><div class="help-block"></div></div></td>';
                        cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-iva"'+ranid+' class="form-control is-number" name="DetalleFactura[iva]" aria-required="true" ><div class="help-block"></div></div></td>';
                        cols += '<td class="col-sm-2"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-valortotal"'+ranid+' class="form-control" name="DetalleFactura[valorTotal]" aria-required="true" disabled><div class="help-block"></div></div></td>';
                        cols += '<td class="col-sm-1"><button type="button"  class="btn btn-danger btn-round btn-just-icon ibtnDel" value="delete" title="" rel="tooltip" data-title="Empresa : mafirma FINAL" data-original-title=" Borrar Item"><i class="material-icons">delete</i><div class="ripple-container"></div></button></td>';
                    newRow.append(cols);
                    $("table.order-list").append(newRow);
            });
    }
    
    $("#recorrer").on("click",function(){
        $("tr").each(function(){
            console.log($(this).find('[name^="DetalleFactura[descuento]"]').val());
        });
    });
    
    $("table.order-list").on("click", ".ibtnDel", function (event) {
         $(this).closest("tr").remove();
         calculateGrandTotal();
     });
     
     $("table.order-list").on("click", ".ibtnDelNC", function (event) {
         $(this).closest("tr").remove();
         //setTimeout(function(){ calculateGrandTotalNC(); }, 1000);
         calculateGrandTotalNC();
     });
    
    $("#facturas-porcentajeiva").keypress(function (event) {
        console.log("enter");
        var e = event; // for trans-browser compatibility
        var charCode = e.which || e.keyCode;
        if (charCode == 13 || e.which == 9 ) {
            calculateGrandTotal();
        }
        
    });
    
});  

function calculateGrandTotal() {
    var grandTotal = 0 ;
    var subTotal = 0;
    var ivaTotal = 0;
    var porcentajeiva = $('#facturas-porcentajeiva').val() ? $('#facturas-porcentajeiva').val() : 0 ;
    $("#myTable").find('tr').each(function () {
        var id = $(this).data("ids");
        if(id) {
            var valorTotalElement =$(this).find('[name^="DetalleFactura[' + id + '][valorTotal]"]');
            subTotal = valorTotalElement.maskMoney('destroy');
            subTotal = parseInt(subTotal.val().replace(/[,.]/g, ''));
            grandTotal += subTotal;
            console.log("iva",$(this).find('[name^="DetalleFactura[' + id + '][iva]"]').val());
            console.log("subtotal",subTotal);
            ivaTotal += parseInt(Math.round(($(this).find('[name^="DetalleFactura[' + id + '][iva]"]').val()*subTotal)/100));
            valorTotalElement.maskMoney({thousands:',', precision:'0'});
        }
    });
    //var iva = (porcentajeiva*grandTotal)/100;
    var iva = ivaTotal;
    $("#facturas-subtotal").maskMoney('mask',grandTotal);
    $("#facturas-iva").maskMoney('mask',ivaTotal);
    grandTotal+=iva;
    $("#facturas-total").maskMoney('mask',grandTotal);
}

function calculateGrandTotalNC() {
    var grandTotal = 0 ;
    var subTotal = 0;
    var ivaTotal = 0;
    var porcentajeiva = $('#facturas-porcentajeiva').val() ? $('#facturas-porcentajeiva').val() : 0 ;
    $("#tableNC").find('tr').each(function () {
        var index = $(this).data("ids");
        if(index) {
            var valorTotalElement = $('#detallefactura-' + index + '-valortotal');
            var iva = $('#detallefactura-' + index + '-iva').val();
                subTotal = valorTotalElement.maskMoney('destroy');
                subTotal = parseInt(subTotal.val().replace(/[,.]/g, ''));
                ivaTotal += parseInt(Math.round((iva * subTotal) / 100));
                grandTotal += subTotal;
                valorTotalElement.maskMoney({thousands: ',', precision: '0'});
        }
    });
    //var iva = (porcentajeiva*grandTotal)/100;
    var iva = ivaTotal;
    $("#facturas-subtotal").maskMoney('mask',grandTotal);
    $("#facturas-iva").maskMoney('mask',ivaTotal);
    grandTotal+=iva;
    $("#facturas-total").maskMoney('mask',grandTotal);
}
    
function runScript(evt) {
        var e = evt; // for trans-browser compatibility
        var charCode = e.which || e.keyCode;
        console.log("acaScript",charCode,e.which);
        if (charCode == 13 || e.which == 9 ) {
            $('#escenario-manejo').val($.calculaDigitoVerificador($('#empresas-identificacion').val()));
            var action_url = urlGlobal+"inscripcion/validarnit";
            validarNits();
        }
    }
function runScriptNit(evt) {
        $('#verificacion-manejo').val($.calculaDigitoVerificador($('#proveedortecnologico-nit').val()));
        var action_url = urlGlobal+"inscripcion/validarnit";
        validarNits();
}

function focusOutNit(event) {
    if(event==undefined)
    	$('#escenario-manejo').val($.calculaDigitoVerificador($('#empresas-identificacion').val()));
		//if($('#empresas-redirectempresa').val() == ""){
			validarNits();
		//}
}   
 
$(document.body).on('focusout',"#personas-identificacion", function(){
        validarPersona();
});

$(document.body).on('focusout',"#empresas-identificacion", function(){
		$('#escenario-manejo').val($.calculaDigitoVerificador($('#empresas-identificacion').val()));
		if($('#empresas-redirectempresa').val() == ""){
			validarNits();
		}
});

var validarNits = function(){
    var eventosId= $("#inscripciones-eventoid").val() ? $("#inscripciones-eventoid").val() : 0;
    var action_url = urlGlobal+"inscripcion/validarnit";
   
     jQuery.ajax({
                method: "POST",
                url: action_url,
                data: {id:$("#empresas-identificacion").val(),eventosId:eventosId}
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==0)
                {   $.confirm({
                                    icon: 'glyphicon glyphicon-info-sign',
                                    title: 'Advertencia',
                                    theme: 'material',
                                    confirmButtonClass: 'btn-success',
                                    cancelButtonClass: 'btn-danger',
                                    confirmButton: 'Aceptar',
                                    cancelButton: 'Cancelar',
                                    content: 'Empresa Existente Desea Ingresar Personas?',
                                 confirm: function(){
                                       window.location.href =  $("#inscripciones-eventoid").val() ? urlGlobal+"congreso/inscripcion-empresa-persona?idEmpresa="+option.idEmpresa : urlGlobal+"inscripcion/inscripcion-empresa-persona?idEmpresa="+option.idEmpresa;
                                 },
                                 cancel: function(){
                                    $("#empresas-identificacion").val(' ');   
                                    $("#escenario-manejo").val(' ');  
                                    $("#empresas-identificacion").closest(".form-group").addClass("is-focused");
                                 }
                            });
                        }
                    });
                });
                return false;
}

var validarPersona = function(){

    var form_data="id="+$("#personas-identificacion").val();
    var action_url = urlGlobal+"inscripcion/validarpersona";
    //var action_url = "/inscripcion/validarpersona";
     jQuery.ajax({
                method: "POST",
                url: action_url,
                data: form_data
            }).done(function(result){
                console.log("result",result);
            $.each(result,function(index,option) {
                if(option.respuesta==0)
                {     $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'Persona ya inscrita',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                    console.log("var-empresa",option.idEmpresa.nombre);
                    $("#personas-nombre").val(option.idEmpresa.nombre);
                    $("#personas-apellido").val(option.idEmpresa.apellido);
                    $("#personas-tipo_documento").val(option.idEmpresa.tipo_documento);
                    $("#personas-telefono").val(option.idEmpresa.telefono);
                    $("#personas-movil").val(option.idEmpresa.movil);
                    
                    $("#personas-direccion").val(option.idEmpresa.direccion);
                    $("#personas-id_departamento").val(option.idEmpresa.id_departamento);
                    $("#personas-email").val(option.idEmpresa.email);
                    $("#personas-id_cargo").val(option.idEmpresa.id_cargo);
                     $("#personas-id").val(option.idEmpresa.id);
                    $("#personas-id_tipo_asistente").val(option.idEmpresa.id_tipo_asistente);
                    
                }
            });
                });
                return false;
}


var getProducto = function($this){
    var selec = $($this);
    var id = selec.val().split("-");
    var form_data="id="+id[1]+"&tipo="+id[0];
    var action_url = "../producto/get-producto";
   
    jQuery.ajax({
        method: "POST",
        url: action_url,
        data: form_data
    }).done(function(result){
        console.log("result",result);
        $.each(result,function(index,option) {
            if(option.respuesta==1)
            { 
                var tr = selec.closest( "tr" );
                var id = tr.data("ids");
               /* if(option.description)
                {
                    var listItems= "<option value>Seleccione</option>";
                    $.each(option.description,function(index,option) {
                        console.log(index+" sss "+option);
                        listItems+= "<option value='" + index + "'>" + option + "</option>";
                    });

                    tr.find('[name^="DetalleFactura['+id+'][descripcion]"]').empty();
                    tr.find('[name^="DetalleFactura['+id+'][descripcion]"]').html(listItems);
                }*/
                console.log("dddd",option.nombre);
                tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').val(option.valor);
                tr.find('[name^="DetalleFactura['+id+'][producto]"]').val(option.nombre);
                tr.find('[name^="DetalleFactura['+id+'][cantidad]"]').val(option.cantidad);
                tr.find('[name^="DetalleFactura['+id+'][id_producto]"]').val(option.id_producto);
                tr.find('[name^="DetalleFactura['+id+'][iva]"]').val(option.iva);
                tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]').val(option.total);
                tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').maskMoney({thousands:',', precision:'0'});
                tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]').maskMoney({thousands:',', precision:'0'});
                tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').maskMoney('mask');
                tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]').maskMoney('mask');
                calculateGrandTotal();
            } 
        });
    });
};

$(document.body).on('change',"#detallerecibos-valor", function(){
 $("#detallerecibos-valor").number( true, 2 );
});

$(document.body).on('change',"#facturas-clientes", function(){
    var selec = $(this);
    var idContacto = selec.val();
    var id = selec.val().split("-");
    var form_data="id="+id[1]+"&tipo="+id[0];
    console.log("data",form_data);
    var action_url = "../factura/dropdown-contactos";
   
    jQuery.ajax({
        method: "POST",
        url: action_url,
        data: form_data,
        dataType: "json",
    }).done(function(result){
        var listItems= "<option value>Seleccione</option>";
        listItems+= "<option value="+idContacto+">IGUAL AL CLIENTE</option>";
        $.each(result,function(index,option) {
            console.log(index+"  "+option);
            listItems+= "<option value='" + index + "'>" + option + "</option>";
        });
        $('#facturas-id_contacto').empty();
        $('#facturas-id_contacto').html(listItems);

    });

    var action_url = "../factura/dropdown-facturas";
    var form_data="id="+id[1]+"&tipo="+id[0];
    jQuery.ajax({
        method: "POST",
        url: action_url,
        data: form_data,
        dataType: "json",
    }).done(function(result){
        var listItems= "";
        $.each(result,function(index,option) {
            console.log(index+"  "+option);
            listItems+= "<option value='" + index + "'>" + option + "</option>";
        });

        $('#facturas-facturas').empty();
        $('#facturas-facturas').html(listItems);
    });
});

$(document.body).on('click',"#buttonLoadFacturas", function(){
    var url = "../factura/get-detalle-factura";
    var dataString ="id="+ $("#facturas-facturas").val();
    $.ajax({
        type: "POST",
        data: dataString,
        url: url,
        dataType: "json",
    }).done(function(respuesta) {
        if($("table.order-list").find('tr').length==5) {
            $.each(respuesta['respuesta'], function (index, value) {
                console.log("drive", value['id']);
                var productos = value['observacion'].split("--");
                var newRow = $("<tr data-ids=[" + counter + "]>");
                var cols = "";
                var idInscripcion = value['id_inscripcion'] ? value['id_inscripcion'] : '';
                var selec = lo = '';
                cols += '<td class="col-sm-2"><div class="form-group field-detallefactura-valortotal required is-empty">' + productos[0] + '<div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-3"><div class="form-group field-detallefactura-valortotal required is-empty" id="detallefactura-observacion"><textarea rows="2" type="text" class="form-control search" name="DetalleFactura[' + counter + '][observacion]" id="detallefactura-observacion" class="form-control" style="text-align: center;" aria-required="true" ' + lo + ' ">' + productos[1] + '</textarea><input type="hidden" id="detallefactura-producto" class="form-control" name="DetalleFactura[' + counter + '][producto]" aria-required="true" value=""><div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="number" id="detallefactura-cantidad" class="form-control" style="text-align: center;" min="1" max="999" name="DetalleFactura[' + counter + '][cantidad]" aria-required="true" ' + lo + ' onKeypress="javascript:cantidad(this,event)" value="' + value['cantidad'] + '"><div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-valor' + counter + '" class="form-control is-number" style="text-align: right;" name="DetalleFactura[' + counter + '][valor]" aria-required="true" onKeypress="javascript:subtotal(this)" value="' + value['valor'] + '"><div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-iva' + counter + '" class="form-control is-number" style="text-align: right;" name="DetalleFactura[' + counter + '][iva]" aria-required="true" onkeyup="javascript:iva(this)" value="' + value['iva'] + '"><div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-2"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-valortotal' + counter + '" class="form-control" style="text-align: right;" name="DetalleFactura[' + counter + '][valorTotal]" aria-required="true" readonly value="' + value['valorTotal'] + '"><div class="help-block">' +
                    '<input type="hidden" id="detallefactura-id_producto' + counter + '" class="form-control" style="text-align: right;" name="DetalleFactura[' + counter + '][id_producto]" aria-required="true" value="' + value['id_producto'] + '"><input type="hidden" id="detallefactura-id' + counter + '" class="form-control" style="text-align: right;" name="DetalleFactura[' + counter + '][id]" aria-required="true" value="' + value['id'] + '">' +
                    '<input type="hidden" id="detallefactura-id_inscripcion' + counter + '" class="form-control" style="text-align: right;" name="DetalleFactura[' + counter + '][id_inscripcion]" aria-required="true" value="' + idInscripcion + '"><div class="help-block"></div></div></td>';
                cols += '<td class="col-sm-1"><button type="button"  class="btn btn-danger btn-round btn-just-icon ibtnDel" value="delete" title="" rel="tooltip" data-title="Empresa : mafirma FINAL" data-original-title=" Borrar Item"><i class="material-icons">delete</i><div class="ripple-container"></div></button></td>';
                newRow.append(cols);
                $("table.order-list").append(newRow);
                //console.log("aaa");
                $("#detallefactura-iva" + counter).numeric(false);
                $("#detallefactura-valortotal"+counter).maskMoney({thousands:',', precision:'0'});
                $("#detallefactura-valortotal"+counter).maskMoney('mask');
                $("#detallefactura-subtotal"+counter).maskMoney({thousands:',', precision:'0'});
                $("#detallefactura-subtotal"+counter).maskMoney('mask');
                //setTimeout(function(){$("#detallefactura-valortotal"+counter).maskMoney({thousands:',', precision:'0'});}, 10);
                counter++;
            });
            $("#facturas-subtotal").maskMoney('mask',respuesta['model']['subtotal']);
            $("#facturas-iva").maskMoney('mask',respuesta['model']['iva']);
            $("#facturas-total").maskMoney('mask',respuesta['model']['total']);
        }
        else{
            $.alert({
                icon: 'glyphicon glyphicon-info-sign',
                title: 'Advertencia',
                theme: 'material',
                content: 'Tiene Facturas Cargas Eliminelas',
                confirmButtonClass: 'btn-info',
                confirmButton: 'Aceptar',

            });
        }
    });
});

$(document.body).on('change',"#facturas-id_contacto", function(){
    var selec = $(this);
    var id = selec.val().split("-");
    var ids=0;
    var tipo=0;
    if(id.length==1)
    {
        tipo = 1;
        ids=id;
    }
    else if(id.length==2 && id[0]=="p")
    {
         tipo = 2;
         ids=id[1];
    }
     else if(id.length==2 && id[0]=="e")
    {
         tipo = 3;
         ids=id[1];
    }
    var form_data="id="+ids+"&tipo="+tipo;
  
    var action_url = "../factura/get-contacto";
   
    jQuery.ajax({
        method: "POST",
        url: action_url,
        data: form_data
    }).done(function(result){
        $.each(result,function(index,option) {
            if(option.respuesta==1)
            { 
                $('#facturas-direccion').val(option.direccion);
                $('#facturas-telefonocontacto').val(option.telefono);
                $('#facturas-diasfacturacion').val(option.diaFacturacion);
            } 
        });
    });
});

var counter=0;
var loadSelect = function(id,list){
    var listProducto= [];
    var listInscripcion= [];
    
    $("tr").each(function(){
        var id = $(this).data("ids");
        if(id!=undefined) {
            counter = parseInt(String(id).replace(/\D/g, ''))+1;
        }
        var selec= $(this).find('[name^="DetalleFactura['+id+'][id_inscripcion]"]').val();
        if(selec)
        {
            var res=selec.split("-");
            res[0]== 'p' ? listProducto.push(res[1]) : listInscripcion.push(res[1]);
        }
    });
    var url = list ? "dropdown-lista" : "dropdown-lista-patrocinio";
    var lo = list ? "" : ""; //readonly
    var dataString=list ? "idInscripcion="+id+"&listInscripcion="+JSON.stringify(listInscripcion) : "listProducto="+JSON.stringify(listProducto);
    var listItems= "";
   
    $.ajax({
              type: "POST",
              data: dataString,
              url: url,
              dataType: "json",
            }).done(function(respuesta){
                console.log(respuesta);
                if(!$.isEmptyObject(respuesta))
                {
                    listItems+= "<option value>Seleccione</option>";
                    //$.each(respuesta['producto'],function(index,option) {
                    var productReturn = id ? respuesta : respuesta['producto'];  
                    $.each(productReturn,function(index,option) {
                        console.log(index+" sss "+option);
                        listItems+= "<option value='" + index + "'>" + option + "</option>";
                    });
                    var newRow = $("<tr data-ids=["+counter+"]>");
                    var cols = "";
                    var selec = '<select  class="form-control" name="DetalleFactura['+counter+'][id_inscripcion]" aria-required="true" onchange="javascript:getProducto(this)">'+listItems+'</select>';

                    cols += '<td class="col-sm-2"><div class="form-group field-detallefactura-valortotal required is-empty">'+selec+'<div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-3"><div class="form-group field-detallefactura-valortotal required is-empty" id="detallefactura-descripcion"><textarea rows="2" type="text" class="form-control search" name="DetalleFactura['+counter+'][descripcion]" id="detallefactura-descripcion" class="form-control" style="text-align: center;" aria-required="true" '+lo+' "></textarea><input type="hidden" id="detallefactura-producto" class="form-control" name="DetalleFactura['+counter+'][producto]" aria-required="true" value=""><div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="number" id="detallefactura-cantidad" class="form-control" style="text-align: center;" min="1" max="999" name="DetalleFactura['+counter+'][cantidad]" aria-required="true" '+lo+' onKeypress="javascript:cantidad(this,event)"><div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-subtotal'+counter+'" class="form-control is-number" style="text-align: right;" name="DetalleFactura['+counter+'][subtotal]" aria-required="true" onKeypress="javascript:subtotal(this)"><div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-1"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-iva'+counter+'" class="form-control is-number" style="text-align: right;" name="DetalleFactura['+counter+'][iva]" aria-required="true" onkeyup="javascript:iva(this)"><div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-2"><div class="form-group field-detallefactura-valortotal required is-empty"><input type="text" id="detallefactura-valortotal'+counter+'" class="form-control" style="text-align: right;" name="DetalleFactura['+counter+'][valorTotal]" aria-required="true" readonly><div class="help-block">' +
                        '<input type="hidden" id="detallefactura-id_producto'+counter+'" class="form-control" style="text-align: right;" name="DetalleFactura['+counter+'][id_producto]" aria-required="true" readonly><div class="help-block"></div></div></td>';
                    cols += '<td class="col-sm-1"><button type="button"  class="btn btn-danger btn-round btn-just-icon ibtnDel" value="delete" title="" rel="tooltip" data-title="Empresa : mafirma FINAL" data-original-title=" Borrar Item"><i class="material-icons">delete</i><div class="ripple-container"></div></button></td>';
                    newRow.append(cols);
                    $("table.order-list").append(newRow);
                    //console.log("aaa");
                    $("#detallefactura-iva"+counter).numeric(false);
                    //$("#detallefactura-valortotal"+counter).maskMoney({precision:2});
                    counter++;
                }
                else{
                    $.alert({
                        icon: 'glyphicon glyphicon-info-sign',
                        title: 'Advertencia',
                        theme: 'material',
                        content: 'No hay mas Inscripciones para Facturar',
                        confirmButtonClass: 'btn-info',
                        confirmButton: 'Aceptar',

                    });
                }
        });
}

var subtotal = function (self){
    console.log("total",self);
    setTimeout(function(){
        var tr = $(self).closest( "tr" );
        var id = tr.data("ids");
        var subTotal = $(self).maskMoney('destroy');
        var valorTotalElement = tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]');
        subTotal = subTotal.val().replace(/[,.]/g, '');
        var total =  subTotal * tr.find('[name^="DetalleFactura['+id+'][cantidad]"]').val();
        tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').maskMoney('mask',subTotal);
        valorTotalElement.maskMoney('mask',total);
        $(self).maskMoney({thousands:',', precision:'0'});
        calculateGrandTotal();
    }, 10);
}

var subtotalNC = function (self){
	
    setTimeout(function(){
        var splitId = $(self).attr('id').split('-');
        var id = splitId[1];
        var subTotal = $(self).maskMoney('destroy');
        var valorTotalElement = $('#detallefactura-'+id+'-valortotal');
        subTotal = subTotal.val().replace(/[,.]/g, '');
        var total =  subTotal * $('#detallefactura-'+id+'-cantidad').val();
        console.log(id,$('#detallefactura-'+id+'-cantidad').val(),subTotal,total);
        $('#detallefactura-'+id+'-subtotal').maskMoney('mask',subTotal);
        valorTotalElement.maskMoney('mask',total);
        $(self).maskMoney({thousands:',', precision:'0'});
        calculateGrandTotalNC();
    }, 10);
}

$('#factura-form-id').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
       // return false;
    }
});

var cantidad = function (self,event){
    $(self).numeric(",");
    var e = event; // for trans-browser compatibility
    var charCode = e.which || e.keyCode;
    if (charCode == 13 || e.which == 9 ) {
        var tr = $(self).closest( "tr" );
        var id = tr.data("ids");
        var subTotal =  tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').val().replace(/[,.]/g, '');
        subTotal =   $(self).val() * subTotal;
        var total =  subTotal;
        tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]').maskMoney('mask',total);
        setTimeout(function(){ calculateGrandTotal(); }, 100);
        event.preventDefault();
        return false;
    }
}

var ivaNC = function (self,event){
		var splitId = $(self).attr('id').split('-');
        var tr = $(self).closest( "tr" );
        var id = splitId[1];
        var subTotal = $('#detallefactura-'+id+'-valor').val().replace(/[,.]/g, '');
        var cantidad = $('#detallefactura-'+id+'-cantidad').val().replace(/[,.]/g, '');
        var total =  cantidad * subTotal;
        $('#detallefactura-'+id+'-valortotal').maskMoney('mask',total);
        setTimeout(function(){ calculateGrandTotalNC(); }, 100);
}

var iva = function (self,event){
        var tr = $(self).closest( "tr" );
        var id = tr.data("ids");
        var subTotal =  tr.find('[name^="DetalleFactura['+id+'][subtotal]"]').val().replace(/[,.]/g, '');
        subTotal =  tr.find('[name^="DetalleFactura['+id+'][cantidad]"]').val() * subTotal;
        var total = subTotal;
        tr.find('[name^="DetalleFactura['+id+'][valorTotal]"]').maskMoney('mask',total);
        setTimeout(function(){ calculateGrandTotal(); }, 100);
}

$.calculaDigitoVerificador = function (rut) {
        vpri = new Array(16); 
        x=0 ; y=0 ; z=rut.length ;
        vpri[1]=3;
        vpri[2]=7;
        vpri[3]=13; 
        vpri[4]=17;
        vpri[5]=19;
        vpri[6]=23;
        vpri[7]=29;
        vpri[8]=37;
        vpri[9]=41;
        vpri[10]=43;
        vpri[11]=47;  
        vpri[12]=53;  
        vpri[13]=59; 
        vpri[14]=67; 
        vpri[15]=71;
        for(i=0 ; i<z ; i++)
        { 
            y=(rut.substr(i,1));
            x+=(y*vpri[z-i]);     
        } 
        y=x%11
        if (y > 1)
        {
            dv1=11-y;
        } else {
            dv1=y;
        }
        return dv1;
    };

 function setUrlInscription(id){
     var url = urlGlobal+'factura/count-transmision';
     $.ajax({
         type: "POST",
         url: url,
         dataType: "json",
     }).done(function(respuesta){
         if(respuesta['respuesta']==1)
         {
             $.confirm({
                 icon: 'glyphicon glyphicon-info-sign',
                 title: 'Advertencia',
                 theme: 'material',
                 confirmButtonClass: 'btn-success',
                 cancelButtonClass: 'btn-danger',
                 confirmButton: 'Factura',
                 cancelButton: 'Contingencia',
                 content: 'Que tipo de Consecutivo va a utilizar?',
                 confirm: function(){
                     if(respuesta['mensajeFactura']==0){
                        $.alert({
                         icon: 'glyphicon glyphicon-info-sign',
                         title: 'Advertencia',
                         theme: 'material',
                         content: 'El parametro de resolución de facturación esta fuera de rango',
                         confirmButtonClass: 'btn-info',
                         confirmButton: 'Aceptar',
        
                        });
                     }
                     if(respuesta['mensajeFactura']==1){
                        window.location.href=id ? urlGlobal+'factura/create?id_inscripcion='+id : urlGlobal+'factura/create';
                     }
                 },
                 cancel: function(){
                    if(respuesta['mensajeContingencia']==0){
                        $.alert({
                         icon: 'glyphicon glyphicon-info-sign',
                         title: 'Advertencia',
                         theme: 'material',
                         content: 'El parametro de resolución de contingencia esta fuera de rango',
                         confirmButtonClass: 'btn-info',
                         confirmButton: 'Aceptar',
        
                        });
                    }
                    if(respuesta['mensajeContingencia']==1){
                        window.location.href=id ? urlGlobal+'factura/contingencia?id_inscripcion='+id : urlGlobal+'factura/contingencia';
                    }
                 }
             });
         }
         if(respuesta['respuesta']==0)
         {
             $.alert({
                 icon: 'glyphicon glyphicon-info-sign',
                 title: 'Advertencia',
                 theme: 'material',
                 content: 'Debe Trasmitir las facturas antes para realizar una Nueva Factura',
                 confirmButtonClass: 'btn-info',
                 confirmButton: 'Aceptar',

             });
         }
     });
    }
    
    
    function openModal(id){
         var form=$(this);
        $("#modal-inicial").modal('show').find('#modalContent').load(urlGlobal+"factura/change-status?id="+id);
        $("#modal-inicial").modal('show').find('#modalHeader').text("Cambio de Estado");
    }
    
    function openModalEstado(id){
         var form=$(this);
        $("#modal-inicial").modal('show').find('#modalContent').load(urlGlobal+"factura/change-paymment?id="+id);
        $("#modal-inicial").modal('show').find('#modalHeader').text("Cambio de Estado");
    }
    
    function openModalPresence(id){
         var form=$(this);
        $("#modal-inicial").modal('show').find('#modalContent').load(urlGlobal+"inscripcion/change-presence?id="+id);
        $("#modal-inicial").modal('show').find('#modalHeader').text("Cambio de Estado");
    }
    
    
    function setNoShow(id){
        var form_data="id="+id;
        $.confirm({
             icon: 'glyphicon glyphicon-info-sign',
             title: 'Advertencia',
             theme: 'material',
             confirmButtonClass: 'btn-success',
             cancelButtonClass: 'btn-danger',
             confirmButton: 'SI',
             cancelButton: 'NO',
             content: 'Deseas colocar el inscrito en no asistio ?',
             confirm: function(){
                var url = urlGlobal+'inscripcion/noshow';
                 $.ajax({
                     type: "POST",
                     url: url,
                     data:form_data,
                     dataType: "json"
                 }).done(function(respuesta){
                    $.each(respuesta,function(index,option) {
                        if(option.respuesta===true)
                        {
                            window.location.href=urlGlobal+"factura/facturados";
                        }
                        else
                        {
                            $.alert({
                             icon: 'glyphicon glyphicon-info-sign',
                             title: 'Advertencia',
                             theme: 'material',
                             content: 'Error al realizar esta accion',
                             confirmButtonClass: 'btn-info',
                             confirmButton: 'Aceptar',
            
                            });
                        }
                    });
                 });
             }
         });
    }
    
    $(document.body).on('click',"#btUserReiniciarPassword", function(){
        !$('.reiniciarpassword').is(':visible') ? $('.reiniciarpassword').show() : $('.reiniciarpassword').hide();
    });
    $(document.body).on('click',"#BtReiniciarpass", function(){
        var form = $(this);
        var action_url=form.data('url');
        var pass = $('#user-password_hash').val();
        var form_data = "id="+form.data('id')+"&pass="+pass;
    
        jQuery.ajax({
            method: "POST",
            url: action_url,
            data: form_data,
            dataType: "json",
        }).done(function(result){
            if(result[0]['respuesta']) {
                $.alert({
                    icon: 'glyphicon glyphicon-info-sign',
                    title: 'Advertencia',
                    theme: 'material',
                    content: result[0]['respuesta'],
                    confirmButtonClass: 'btn-info',
                    confirmButton: 'Aceptar',
                });
                if(!result[0]['error']) $('.reiniciarpassword').hide();
            }
        });
    });
    
